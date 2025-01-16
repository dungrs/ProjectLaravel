<?php

namespace App\Services;
use App\Services\Interfaces\OrderServiceInterface;
use App\Repositories\OrderRepository;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


/**
 * Class OrderService
 * @package App\Services
 */
class OrderService extends BaseService implements OrderServiceInterface
{   
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository) {
        $this->orderRepository = $orderRepository;
    }

    public function paginate($request) {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $perpage = $request->integer('perpage');
        foreach(__('cart') as $key => $val) {
            $condition['dropdown'][$key] = $request->string($key);
        }
        $condition['created_at'] = $request->string('created_at');

        $order = $this->orderRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perpage, 
            ['path' => 'order/index'],
            ['orders.id', 'DESC'],
        );
    
        return $order;
    }

    
    public function getOrder($condition) {
        $order =  $this->orderRepository->findByCondition(
            $condition,
            true,
            [
                [
                    'table' => 'order_product as op',
                    'on' => ['op.order_id', 'orders.id']
                ],
                [
                    'table' => 'product_variants as pv',
                    'on' => ['pv.uuid', 'op.uuid']
                ],
                [
                    'table' => 'provinces as p',
                    'on' => ['p.code', 'orders.province_id'],
                    'type' => 'left'
                ],
                [
                    'table' => 'districts as d',
                    'on' => ['d.code', 'orders.district_id'],
                    'type' => 'left'
                ],
                [
                    'table' => 'wards as w',
                    'on' => ['w.code', 'orders.ward_id'],
                    'type' => 'left'
                ],
            ],
            
            ['id' => 'ASC'],
            [
                'orders.*', 
                'op.name',
                'op.uuid',
                'op.qty',
                'op.price',
                'op.price_original' ,
                'pv.album',
                'p.name as province_name',
                'd.name as district_name',
                'w.name as ward_name',
            ]
        );

        return $order;
    }

    public function update($request) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $orderId = $request->input('id');
            $payload = $request->input('payload');

            $this->orderRepository->update($orderId, $payload);
    
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function updateVnpay($payload, $order) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $orderId = $order->id;
            $this->orderRepository->update($orderId, $payload);
    
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
            'code',
            'fullname',
            'phone',
            'email',
            'province_id',
            'district_id',
            'ward_id',
            'address',
            'description',
            'promotion',
            'cart',
            'customer_id',
            'guest_cookie',
            'method',
            'payment',
            'confirm',
            'delivery',
            'shipping',
            'created_at',
        ];
    }
}
