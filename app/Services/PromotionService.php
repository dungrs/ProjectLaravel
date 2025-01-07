<?php

namespace App\Services;
use App\Services\Interfaces\PromotionServiceInterface;
use App\Repositories\PromotionRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Classes\PromotionEnum;

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
        DB::beginTransaction();
        
        try {
            $payload = $this->request($request);
            $promotion = $this->handlePromotionMethod($payload, $request);
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
        }
    }
    
    public function update($id, $request, $languageId) {
        DB::beginTransaction();
        
        try {
            $payload = $this->request($request);
            $promotion = $this->handlePromotionMethod($payload, $request, $id);
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
        }
    }
    
    private function handlePromotionMethod($payload, $request, $id = null) {
        $promotion = null;
        
        switch ($payload['method']) {
            case PromotionEnum::ORDER_AMOUNT_RANGE:
                $payload['discount_information'] = $this->orderByRange($request);
                $promotion = $this->handlePromotionCreateOrUpdate($id, $payload);
                break;
                
            case PromotionEnum::PRODUCT_AND_QUANTITY:
                $payload['discountValue'] = normalizeAmount($request->input('product_and_quantity.discountValue'));
                $payload['maxDiscountValue'] = normalizeAmount($request->input('product_and_quantity.maxDiscountValue'));
                $payload['discountType'] = $request->input('product_and_quantity.discountType');
                $payload['discount_information'] = $this->productAndQuantity($request);
                $promotion = $this->handlePromotionCreateOrUpdate($id, $payload);
                $this->creatPromotionProductVariant($promotion, $request);
                break;
        }
    
        return $promotion;
    }
    
    private function handlePromotionCreateOrUpdate($id, $payload) {
        if ($id) {
            $promotion = $this->promotionRepository->update($id, $payload);
            $promotion = $this->promotionRepository->findById($id);
        } else {
            $promotion = $this->promotionRepository->create($payload);
        }
        
        return $promotion;
    }

    private function request($request) {
        $payload = $request->only(
            'name',
            'code',
            'description',
            'method',
            'start_date',
            'end_date',
            'never_end_date'
        );
        
        $payload['start_date'] = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $payload['start_date']);
        if (isset($payload['end_date'])) {
            $payload['end_date'] = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $payload['end_date']);
        }

        $payload['code'] = (empty($payload['code'])) ? time() : $payload['code'];

        return $payload;
    }

    private function handleSourceAndCondition($request) {
        $data = [
            'source' => [
                'status' => $request->input('source'),
                'data' => $request->input('sourceValue')
            ],
            'apply' => [
                'status' => $request->input('apply'),
                'data' => $request->input('applyValue')
            ]
        ];

        if (!is_null($data['apply']['data'])) {
            foreach($data['apply']['data'] as $key => $val) {
                $data['apply']['condition'][$val] = $request->input($val);
            }
        }

        return $data;
    }

    private function orderByRange($request) {
        $data['info'] = $request->input('promotion_order_amount_range');
        return $data + $this->handleSourceAndCondition($request);
    }

    private function productAndQuantity($request) {
        // $data['info'] = $request->input('product_and_quantity');
        $data['info']['model'] = $request->input('module_type');
        $data['info']['object'] = $request->input('object');
        return $data + $this->handleSourceAndCondition($request);
    }

    private function creatPromotionProductVariant($promotion, $request) {
        $object = $request->input('object');
        $payloadRepository = array_map(function ($key) use ($object, $request, $promotion) {
            return [
                'promotion_id' => $promotion->id,
                'product_id' => $object['product_id'][$key],
                'variant_uuid' => $object['variant_uuid'][$key],
                'model' => $request->input('module_type'),
            ];
        }, array_keys($object['name']));
        $promotion->products()->sync($payloadRepository);
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

    public function getPromotionValue() {
        $types = [
            'staff_take_care_customer' => 'User',
            'customer_group' => 'Customer',
            'customer_gender' => __('module.gender'),
            'customer_birthday' => __('module.day'),
        ];
    
        $result = [];
        foreach ($types as $key => $type) {
            $objects = [];
    
            if (is_string($type)) {
                $class = $this->loadClass($type);
                $objects = $class->all()->toArray();
            } elseif (is_array($type)) {
                $objects = $type;
            }
    
            if (empty($objects)) {
                $result[$key] = [];
                continue;
            }
    
            foreach ($objects as $keyObj => $val) {
                $result[$key][] = [
                    'id' => $val['id'] ?? $keyObj,
                    'name' => $val['name'] ?? $val,
                ];
            }
        }
    
        return $result;
    }

    public function getInputProductAndQuantity($promotion) {
        return [
            'discountValue' => $promotion->discountValue,
            'discountType' => $promotion->discountType,
            'maxDiscountValue' => $promotion->maxDiscountValue,
        ];
    }
    
    public function applyPromotionsToItems($items, $bestPromotions) {
        return $items->map(function ($item) use ($bestPromotions) {
            if ($promotion = $bestPromotions->firstWhere('product_id', $item->id)) {
                $item->promotion = $promotion;
            }
            return $item;
        });
    }

    public function getBestPromotion($tableChild, $itemIdPromotions, $extend = []) {
        $condition = [
            ['promotions.publish', '=', 2],
            ["{$tableChild}s.publish", '=', 2],
            ["{$tableChild}s.id", 'IN', $itemIdPromotions],
            ['promotions.end_date', '>', now()],
        ];

        if (!empty($extend) && isset($extend['condition'])) {
            $condition[] = $extend['condition'];
        }
        
        $promotion = $this->promotionRepository->findByCondition(
            $condition,
            true,
            [
                ['table' => "promotion_{$tableChild}_variant as ppv", 'on' => ["ppv.promotion_id", "promotions.id"]],
                ['table' => "{$tableChild}s", 'on' => ["{$tableChild}s.id", "ppv.{$tableChild}_id"]],
                ['table' => "{$tableChild}_variants", 'on' => ["{$tableChild}_variants.uuid", "ppv.variant_uuid"]]
            ],
            ["{$tableChild}s.id" => 'ASC'],
            [
                DB::raw("MAX(LEAST(CASE WHEN promotions.discountType = 'cash' THEN promotions.discountValue 
                    WHEN promotions.discountType = 'percent' THEN ({$tableChild}_variants.price * promotions.discountValue / 100) ELSE 0 END,
                    CASE WHEN promotions.maxDiscountValue > 0 THEN promotions.maxDiscountValue ELSE 1e9 END)) AS finalDiscount"),
                "{$tableChild}s.id AS {$tableChild}_id", "{$tableChild}_variants.price AS {$tableChild}_price",
                "promotions.discountType", "promotions.discountValue", "promotions.maxDiscountValue"
            ],
            null,
            [],
            [
                "{$tableChild}s.id", "{$tableChild}_variants.price", 'promotions.discountType', 
                'promotions.discountValue', 'promotions.maxDiscountValue', "{$tableChild}_variants.uuid"
            ],
            8
        );

        // Lọc và áp dụng promotion tốt nhất cho sản phẩm
        $bestPromotions = collect($promotion)->groupBy("{$tableChild}_id")
        ->map(fn($group) => $group->sortByDesc('finalDiscount')->first())
        ->values();

        return $bestPromotions;
    }

    public function getPromotionForProduct($tableChild, $productId) {
        // Lấy thông tin khuyến mãi cho từng variant
        $promotions = $this->promotionRepository->findByCondition(
            [
                ['promotions.publish', '=', 2],
                ["{$tableChild}s.publish", '=', 2],
                ["{$tableChild}s.id", '=', $productId],
                ['promotions.end_date', '>', now()],
            ],
            true,
            [
                ['table' => "promotion_{$tableChild}_variant as ppv", 'on' => ["ppv.promotion_id", "promotions.id"]],
                ['table' => "{$tableChild}s", 'on' => ["{$tableChild}s.id", "ppv.{$tableChild}_id"]],
                ['table' => "{$tableChild}_variants", 'on' => ["{$tableChild}_variants.uuid", "ppv.variant_uuid"]],
            ],
            ["{$tableChild}s.id" => 'ASC'],
            [
                DB::raw("CASE WHEN promotions.discountType = 'cash' THEN promotions.discountValue 
                    WHEN promotions.discountType = 'percent' THEN ({$tableChild}_variants.price * promotions.discountValue / 100) ELSE 0 END AS finalDiscount"),
                "{$tableChild}s.id AS {$tableChild}_id", "{$tableChild}_variants.uuid AS variant_uuid", "{$tableChild}_variants.price AS variant_price",
                "promotions.discountType", "promotions.discountValue", "promotions.maxDiscountValue"
            ],
            null,
            [],
            [
                "{$tableChild}s.id", "{$tableChild}_variants.uuid", "{$tableChild}_variants.price", 'promotions.discountType', 
                'promotions.discountValue', 'promotions.maxDiscountValue'
            ],
            8
        );

        // Trả về khuyến mãi cho từng variant
        return collect($promotions)->map(function ($promotion) {
            // Tính toán giá trị giảm giá cho từng variant
            $finalDiscount = $promotion->discountType == 'cash'
                ? $promotion->discountValue
                : ($promotion->variant_price * $promotion->discountValue / 100);

            return [
                'discountType' => $promotion->discountType,
                'discountValue' => $promotion->discountValue,
                'variant_uuid' => $promotion->variant_uuid,
                'finalDiscount' => $finalDiscount,
                'product_price' => $promotion->variant_price,
                'finalPrice' => $promotion->variant_price - $finalDiscount
            ];
        });
    }

    public function applyPromotionToProduct(&$product, $tableChild) {
       $bestPromotions = $this->getBestPromotion($tableChild, [$product->id]);
       if ($promotion = $bestPromotions->firstWhere('product_id', $product->id)) {
           $product->promotion = $promotion;
       }
   }

    public function applyPromotionToProductCollection($productCollection, $productSource, $tableChild) {
       $itemIdPromotions = $productSource->pluck('id')->unique();
       $bestPromotions = $this->getBestPromotion($tableChild, $itemIdPromotions);
   
       return $productCollection->map(function ($product) use ($bestPromotions) {
           if ($promotion = $bestPromotions->firstWhere('product_id', $product->id)) {
               $product->promotion = $promotion;
           }
           return $product;
       });
   }

    private function paginateSelect() {
        return [
            'id',
            'name',
            'code',
            'discount_information',
            'discountValue',
            'maxDiscountValue',
            'discountType',
            'method',
            'never_end_date',
            'start_date',
            'end_date',
            'publish',
            'order'
        ];
    }
}
