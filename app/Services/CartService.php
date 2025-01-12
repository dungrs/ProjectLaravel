<?php

namespace App\Services;
use App\Services\Interfaces\CartServiceInterface;

use App\Services\BaseService;
use App\Services\PromotionService;
use App\Services\ProductVariantService;

use App\Repositories\ProductRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PromotionRepository;
use App\Repositories\ProductVariantRepository;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Mail\OrderMail;

/**
 * Class CartService
 * @package App\Services
 */
class CartService extends BaseService implements CartServiceInterface
{   
    protected $productRepository;
    protected $promotionRepository;
    protected $orderRepository;
    protected $productVariantRepository;
    protected $productVariantService;
    protected $promotionService;
    protected $priceOriginal;
    protected $image;

    public function __construct(
        ProductRepository $productRepository,
        PromotionRepository $promotionRepository,
        OrderRepository $orderRepository,
        ProductVariantRepository $productVariantRepository,
        ProductVariantService $productVariantService,
        PromotionService $promotionService,
        ) {
        $this->productRepository = $productRepository;
        $this->promotionRepository = $promotionRepository;
        $this->orderRepository = $orderRepository;
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
                $cart->setImage($variantImage)->setPriceOriginal($variantItem->price);
            } elseif (isset($objects['products'][$objectId])) {
                $productItem = $objects['products'][$objectId];
                $cart->setImage($cart->image)->setPriceOriginal($productItem->price);
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
            $cartRecaculate = $this->cartAndPromotion();
            
            $cartRecaculate['cartItemSubTotal'] = $cartItem->qty * $cartItem->price;

            return $cartRecaculate;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            echo $e->getMessage();
            die();
        }

    }

    public function delete($request, $language) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $payload = $request->input();
            Cart::instance('shopping')->remove($payload['rowId']);
            $cartRecaculate = $this->cartAndPromotion();

            return $cartRecaculate;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            echo $e->getMessage();
            die();
        }
    }

    public function order($request, $system) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
            $payload = $this->request($request);
            $order = $this->orderRepository->create($payload);
            if ($order->id > 0) {
                $this->createOrderProduct($payload, $order);
                $this->paymentOnline($payload['method']);
                // Cart::instance('shopping')->destroy();
            }
            
            DB::commit();
            $this->mail($order->code, $system);
            return [
                'order' => $order,
                'flag' => true,
            ];
        try {
        } catch (Exception $e) {
            echo $e->getMessage();
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            return [
                'order' => null,
                'flag' => false,
            ];
        }
    }

    public function getOrder($code) {
        $order =  $this->orderRepository->findByCondition(
            [
                ['code', '=', $code],
            ],
            true,
            [
                [
                    'table' => 'order_product as op',
                    'on' => ['op.order_id', 'orders.id'] 
                ]
            ],
            
            ['id' => 'ASC'],
            [
                'orders.*', 
                'op.name',
                'op.uuid',
                'op.qty',
                'op.price',
                'op.price_original' 
            ]
        );

        return $order;
    }

    
    private function mail($code, $system) {
        $data = [];

        $order = $this->getOrder($code);
        $to = $order->first()->email;
        \Mail::to($to)->cc($system['contact_email'])->send(new OrderMail($order));
    }

    private function paymentOnline($method = '') {
        // switch ($method) {
        //     case 'zalo':
        //         $this->zaloPay();
        //         break;
        //     case 'momo':
        //         $this->momoPay();
        //         break;
        //     case 'shopee':
        //         $this->shopeePay();
        //         break;
        //     case 'vnpay':
        //         $this->vnpayPay();
        //         break;
        //     case 'paypal':
        //         $this->paypal();
        //         break;
        // }
    }

    private function createOrderProduct($payload, $order) {
        $temp = [];
        if (!is_null($payload['cart']['details'])) {
            foreach ($payload['cart']['details'] as $key => $val) {
                $extract = explode('_', $val->id);
                $temp[] = [
                    'product_id' => $extract[0],
                    'uuid' => ($extract[1]) ?? null,
                    'name' => $val->name,
                    'qty' => $val->qty,
                    'price' => $val->price,
                    'price_original' => $val->priceOriginal,
                    'option' => json_encode($val->options),
                ];
            }

            $order->products()->sync($temp);
        }
    }

    private function request($request) {
        $carts = Cart::instance('shopping')->content();
        $carts = $this->remakeCart($carts);
        $reCalculateCart = $this->reCalculate();
        $cartPromotion = $this->cartPromotion($reCalculateCart['cartTotal']);
        
        $payload = $request->except(['_token', 'voucher', 'create']);
        $payload['code'] = time();
        $payload['cart'] = $reCalculateCart;
        $payload['cart']['details'] = $carts;
        $payload['promotion']['discount'] = $cartPromotion['discount'];
        $payload['promotion']['code'] = $cartPromotion['selectedPromotion']->code;
        $payload['promotion']['name'] = $cartPromotion['selectedPromotion']->name;
        $payload['promotion']['start_date'] = $cartPromotion['selectedPromotion']->start_date;
        $payload['promotion']['end_date'] = $cartPromotion['selectedPromotion']->end_date;
        $payload['confirm'] = 'pending';
        $payload['delivery'] = 'pending';
        $payload['payment'] = 'unpaid';

        return $payload;
    }

    private function cartAndPromotion() {
        $cartRecaculate = $this->reCalculate();
        $cartPromotion = $this->cartPromotion($cartRecaculate['cartTotal']);
        $cartRecaculate['cartTotal'] = $cartRecaculate['cartTotal'] - $cartPromotion['discount'];
        $cartRecaculate['cartDiscount'] = $cartPromotion['discount'];

        return $cartRecaculate;
    }

    public function reCalculate() {
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

    public function cartPromotion($cartTotal) {
        $maxDiscount = 0;
        $promotions = $this->promotionRepository->getPromotionByCartTotal();
        $selectedPromotion = null;

        if (!is_null($promotions)) {
            foreach ($promotions as $promotion) {
                $discount = $promotion->discount_information['info'];
                $amountFrom = $discount['amountFrom'] ?? [];
                $amountTo = $discount['amountTo'] ?? [];
                $amountValue = $discount['amountValue'] ?? [];
                $amountType = $discount['amountType'] ?? [];
    
                if (!empty($amountFrom) && count($amountFrom) == count($amountTo) && count($amountTo) == count($amountValue)) {
                    for ($i = 0; $i < count($amountFrom); $i++) {
                        $currentAmountFrom = convert_price($amountFrom[$i], true);
                        $currentAmountTo = convert_price($amountTo[$i], true);
                        $currentAmountValue = convert_price($amountValue[$i], true);
                        $currentAmountType = $amountType[$i];
    
                        // Kiểm tra giá trị cartTotal
                        if (($cartTotal > $currentAmountFrom && $cartTotal <= $currentAmountTo) || $cartTotal > $currentAmountTo) {
                            if ($currentAmountType == 'cash') {
                                $maxDiscount = max($maxDiscount, $currentAmountValue);
                            } elseif ($currentAmountType == 'percent') {
                                $discountValue = ($currentAmountValue / 100) * $cartTotal;
                                $maxDiscount = max($maxDiscount, $discountValue);
                            }

                            $selectedPromotion = $promotion;
                        }
                    }
                }
            }
        }
    
        return [
            'discount' => $maxDiscount,
            'selectedPromotion' => $selectedPromotion,
        ];
    }
    
}
