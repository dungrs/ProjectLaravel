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

    private function paginateSelect() {
        return [
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
