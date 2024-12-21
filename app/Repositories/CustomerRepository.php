<?php

namespace App\Repositories;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Customer;

/**
 * Class CustomerService
 * @package App\Services
 */
class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    protected $model;

    public function __construct(Customer $model) {
        $this->model = $model;
    }

    public function pagination(
        array $column = ['*'], 
        array $condition = [], 
        int $perpage = 1, 
        array $extend = [], 
        array $orderBy = ['id', 'DESC'],
        array $join = [], 
        array $relations = [],
        array $rawQuery = [],
        int $page = 1
    ) {
        // Khởi tạo truy vấn để lấy các cột đã chỉ định từ model
        $query = $this->model->select($column)->where(function($query) use ($condition) {
            // Kiểm tra xem 'keyword' có tồn tại và không rỗng trong mảng $condition hay không
            if (isset($condition['keyword']) && !empty($condition['keyword'])) {
                // Thêm điều kiện vào truy vấn, tìm kiếm trong cột 'name' với từ khóa tương tự (LIKE)
                $query->where('customers.name', 'LIKE', '%' . $condition['keyword'] . '%')
                        ->orWhere('customers.email', 'LIKE', '%' . $condition['keyword'] . '%')
                        ->orWhere('customers.address', 'LIKE', '%' . $condition['keyword'] . '%')
                        ->orWhere('customers.phone', 'LIKE', '%' . $condition['keyword'] . '%');
            }

            if (isset($condition['publish']) && !empty($condition['publish']) && $condition['publish'] != 0) {
                $query->where('customers.publish', '=', $condition['publish']);
            }
        
        // Nạp từ bảng customer_catalogues
        })->with('customer_catalogues');

        if (!empty($join)) {
            $query->join(...$join);
        }
 
        return $query->paginate($perpage)->withQueryString()->withPath(env('APP_URL'). $extend['path']);
    }

    public function getCustomersForUpdate(array $customerIds) {
        return $this->model->whereIn('id', $customerIds)
            ->with('customer_catalogues') 
            ->get();
    }

}