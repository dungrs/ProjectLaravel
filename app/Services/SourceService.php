<?php

namespace App\Services;
use App\Services\Interfaces\SourceServiceInterface;
use App\Repositories\SourceRepository;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


/**
 * Class SourceService
 * @package App\Services
 */
class SourceService extends BaseService implements SourceServiceInterface
{   
    protected $sourceRepository;

    public function __construct(SourceRepository $sourceRepository) {
        $this->sourceRepository = $sourceRepository;
    }

    public function paginate($request) {
        $condition['keyword'] = addslashes($request -> input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perpage = $request->integer('perpage');
        $source = $this->sourceRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perpage, 
            ['path' => 'source/index'], 
            ['id', 'DESC'], 
            []
        );
        return $source;
    }
    public function create($request) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $payload = $request->only('name', 'keyword', 'description');
            $this->sourceRepository->create($payload);
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function update($id, $request) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $payload = $request->only('name', 'keyword', 'description');
            $this->sourceRepository->update($id, $payload);
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
            $this->sourceRepository->delete($id);
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
            'description',
            'publish',
        ];
    }
}
