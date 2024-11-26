<?php

namespace App\Services;
use App\Services\Interfaces\SystemServiceInterface;
use App\Repositories\SystemRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Class SystemService
 * @package App\Services
 */
class SystemService implements SystemServiceInterface
{   
    protected $systemRepository;

    public function __construct(SystemRepository $systemRepository) {
        $this->systemRepository = $systemRepository;
    }

    public function save($request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            // Lấy tất cả dữ liệu từ request
            $config = $request->input("config");
            if (count($config)) {
                foreach($config as $key => $val) {
                    // Chuẩn bị dữ liệu cho từng bản ghi
                    $payload = [
                        'keyword' => $key,
                        'content' => $val,
                        'language_id' => $languageId,
                        'user_id' => Auth::id(),
                    ];
                    
                    // Điều kiện để xác định bản ghi nào cần update/insert
                    $condition = ['keyword' => $key, 'language_id' => $languageId, 'user_id' => Auth::id()];
                    
                    // Gọi updateOrInsert cho từng bản ghi
                    $this->systemRepository->updateOrInsert($payload, $condition);
                }
            }
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }
}
