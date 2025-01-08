<?php

namespace App\Services;
use App\Services\Interfaces\CartServiceInterface;

use App\Services\BaseService;
use App\Services\PromotionService;
use App\Services\ProductVariantService;

use App\Repositories\ProductRepository;
use App\Repositories\ProductVariantRepository;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Gloudemans\Shoppingcart\Facades\Cart;

/**
 * Class CartService
 * @package App\Services
 */
class CartService extends BaseService implements CartServiceInterface
{   
    protected $productRepository;
    protected $productVariantRepository;
    protected $productVariantService;
    protected $promotionService;

    public function __construct(
        ProductRepository $productRepository, 
        ProductVariantRepository $productVariantRepository,
        ProductVariantService $productVariantService,
        PromotionService $promotionService,
        ) {
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->productVariantService = $productVariantService;
        $this->promotionService = $promotionService;
    }

    public function create($request, $languageId = 1) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $payload = $request->input();
            $product = $this->productRepository->getProductById($payload['product_id'], $languageId);
            $data = [
                'id' => $product->id,
                'name' => $product->name,
                'qty' => $payload['quantity']
            ];

            if (isset($payload['attribute_id']) && count($payload['attribute_id'])) {
                $attributeId = $payload['attribute_id'];
                $attributeString = sortAttributeId($attributeId);
                $productVariant = $this->productVariantService->getProductVariant($payload, $languageId, $attributeString);
                $data['price'] = $productVariant->price;
                $data['id'] = $product->id . '_' . $productVariant->uuid;
                $data['name'] = $product->name . ' ' . $productVariant->name;
                $data['options'] = [
                    'attribute' => $payload['attribute_id']
                ];
                $promotion = $this->promotionService->getPromotionForProductVariant($payload['product_id'], $productVariant)->first();
                if (!empty($promotion)) {
                    $data['price'] = $promotion->product_price - $promotion->finalDiscount;
                }
            }

            Cart::instance('shopping')->add($data);
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }

        

    }
}
