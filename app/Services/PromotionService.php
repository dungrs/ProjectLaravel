<?php

namespace App\Services;
use App\Services\Interfaces\PromotionServiceInterface;
use App\Repositories\PromotionRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


/**
 * Class PromotionService
 * @package App\Services
 */
class PromotionService extends BaseService implements PromotionServiceInterface
{   
    protected $promotionRepository;

    public function __construct(PromotionRepository $promotionRepository) {
        $this->promotionRepository = $promotionRepository;
    }

    public function paginate($request) {
        $condition['keyword'] = addslashes($request -> input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perpage = $request->integer('perpage');
        $promotion = $this->promotionRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perpage, 
            ['path' => 'promotion/index'], 
            ['id', 'DESC'], 
            []
        );
        return $promotion;
    }
    public function create($request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            // Lấy tất cả dữ liệu từ request
            $payload = $this->payload($request, $languageId);
            $this->promotionRepository->create($payload);
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function update($id, $request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            // Lấy tất cả dữ liệu từ request
            $payload = $this->payload($request, $languageId); 
            $promotion = $this->promotionRepository->update($id, $payload);

            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    private function payload($request, $languageId) {
        $payload = $request->only(['name', 'keyword', 'short_code', 'album', 'model']);
        $payload['model_id'] = $request->input('modelItem.id');
        $payload['description'] = json_encode([
            $languageId => $request->input('description'),
        ]);

        return $payload;
    }

    public function delete($id) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $promotion = $this->promotionRepository->delete($id);
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
            'model',
            'publish',
            'description'
        ];
    }
}
