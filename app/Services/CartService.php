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

    public function remakeCart($carts) {
        $cartId = $carts->pluck('id')->all();
        $temp = [];
        $objects = [];

        if(count($cartId)) {
            foreach($cartId as $key => $val) {
                $extract = explode('_', $val);
                if (count($extract) > 1) {
                    $temp['variants'][] = $extract[1];
                } else {
                    $temp['products'][] = $extract[0];
                }
            }
        }

        if (isset($temp['variants']) && count($temp['variants'])) {
            $objects['variants'] = $this->productVariantRepository->findByCondition(
                [
                    ['product_variants.uuid', 'IN', $temp['variants']],
                ],
                true,
                [],
                ['id' => 'DESC']
            )->keyBy('uuid');
        }
    
        if (isset($temp['products']) && count($temp['products'])) {
            $objects['products'] = $this->productRepository->findByCondition(
                [
                    ['products.id', 'IN', $temp['products']],
                ],
                true,
                [],
                ['id' => 'DESC']
            )->keyBy('id');
        }

        /* 
            Chưa tính được khuyến mãi của 1 sản phẩm không có phiên bản
        */
        foreach($carts as $cart) {
            $explode = explode('_', $cart->id);
            $objectId = $explode[1] ?? $explode[0];

            if (isset($objects['variants'][$objectId])) {
                $variantItem = $objects['variants'][$objectId];
                $variantImage = explode(',', $variantItem->album)[0] ?? null;
                $cart->image = $variantImage;
                $cart->priceOriginal = $variantItem->price;
            } elseif (isset($objects['products'][$objectId])) {
                $productItem = $objects['products'][$objectId];
                $cart->image = $productItem->image;
                $cart->priceOriginal = $productItem->price;
            }
        }

        return $carts;
    }

    public function update($request, $language) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $payload = $request->input();
            Cart::instance('shopping')->update($payload['rowId'], $payload['qty']);
            $cartItem = Cart::instance('shopping')->get($payload['rowId']);
            $cartRecaculate = $this->reCalculate();
            $cartRecaculate['cartItemSubTotal'] = $cartItem->qty * $cartItem->price;

            return $cartRecaculate;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }

    }

    public function delete($request, $language) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $payload = $request->input();
            Cart::instance('shopping')->remove($payload['rowId']);
            $cartRecaculate = $this->reCalculate();

            return $cartRecaculate;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }

    }

    private function reCalculate() {
        $carts = Cart::instance('shopping')->content();
        $total = 0;
        $totalItem = 0;
        foreach($carts as $cart) {
            $total += $cart->price * $cart->qty;
            $totalItem += $cart->qty;
        }

        return [
            'flag' => true,
            'cartTotal' => $total,
            'cartTotalItems' => $totalItem,
        ];
    }
}
