<?php

namespace App\Services;
use App\Services\Interfaces\SlideServiceInterface;
use App\Repositories\SlideRepository;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


/**
 * Class SlideService
 * @package App\Services
 */
class SlideService extends BaseService implements SlideServiceInterface
{   
    protected $slideRepository;

    public function __construct(SlideRepository $slideRepository) {
        $this->slideRepository = $slideRepository;
    }

    public function paginate($request) {
        $condition['keyword'] = addslashes($request -> input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perpage = $request->integer('perpage');
        $permission = $this->slideRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perpage, 
            ['path' => 'slide/index'], 
            ['id', 'DESC'], 
            []
        );
        return $permission;
    }

    public function create($request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            // Lấy tất cả dữ liệu từ request
            $payload = $request->only('_token', 'name', 'keyword', 'setting', 'short_code');
            $payload['setting'] = $this->formatJson($request, 'setting');
            $payload['item'] = json_encode($this->handleSlideItem($request, $languageId));
            $this->slideRepository->create($payload);

            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    private function handleSlideItem($request, $languageId) {
        $temp = [];
        $slide = $request->input('slide');
        foreach ($slide['image'] as $key => $val) {
            $temp[$languageId][] = [
                'name' => $val,
                'description' => $slide['description'][$key],
                'canonical' => $slide['canonical'][$key],
                'alt' => $slide['alt'][$key],
                'window' => (isset($slide['window'][$key])) ? $slide['window'][$key] : '',
            ];
        }

        return $temp;
    }

    public function convertSlideArray(array $slide = [], $languageId) : array {
        dd($slide);
        return [];
    }

    public function update($id, $request) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            // Lấy tất cả dữ liệu từ request
            $payload = $request->except('_token', 'send'); 
            $slide = $this->slideRepository->update($id, $payload);

            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function delete($id) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $slide = $this->slideRepository->delete($id);
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    private function paginateSelect() {
        return [
            'id',
            'name',
            'keyword',
            'item'
        ];
    }
}
