<?php

namespace App\Services;
use App\Services\Interfaces\ReviewServiceInterface;
use App\Repositories\ReviewRepository;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Classes\ReviewNestedset;


/**
 * Class ReviewService
 * @package App\Services
 */
class ReviewService extends BaseService implements ReviewServiceInterface
{   
    protected $reviewRepository;

    public function __construct(ReviewRepository $reviewRepository) {
        $this->reviewRepository = $reviewRepository;
    }

    public function create($request) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $payload = $request->except('_token');
            $review = $this->reviewRepository->create($payload);
            $this->reviewNestedSet = new ReviewNestedset([
                'table' => 'reviews',
                'reviewable_type' => $payload['reviewable_type']
            ]);
            
            $this->reviewNestedSet->Get();
            $this->reviewNestedSet->Recursive(0, $this->reviewNestedSet->Set());
            $this->reviewNestedSet->Action();

            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return [
                'code' => 10,
                'messages' => 'Đánh giá sản phẩm thành công'
            ];
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            // echo $e->getMessage();
            // die();
            return [
                'code' => 11,
                'messages' => 'Có vấn đề xảy ra! Hãy thử lại'
            ];
        }
    }

}
