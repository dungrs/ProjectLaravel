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
        $data['info'] = $request->input('product_and_quantity');
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

    private function paginateSelect() {
        return [
            'id',
            'name',
            'code',
            'discount_information',
            'method',
            'never_end_date',
            'start_date',
            'end_date',
            'publish',
            'order'
        ];
    }
}
