<?php

namespace App\Repositories;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Order;

/**
 * Class OrderService
 * @package App\Services
 */
class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    protected $model;

    public function __construct(Order $model) {
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
        int $page = 1  // Thêm tham số $page để xác định trang hiện tại
    ) {
        // Khởi tạo truy vấn để lấy các cột đã chỉ định từ model
        $query = $this->model->select($column);
    
        return $query
            ->keyword($condition['keyword'] ?? null, ['fullname', 'phone', 'email', 'address', 'code'])
            ->publish($condition['publish'] ?? null)
            ->customWhere($condition['where'] ?? null)
            ->customWhereRaw($rawQuery['whereRaw'] ?? null)
            ->relation($relations ?? null)
            ->relationCount($relations ?? null)
            ->customJoin($join ?? null)
            ->extendCustomGroupBy($extend['groupBy'] ?? null)
            ->extendCustomOrderBy($orderBy ?? ['id', 'DESC'])
            // Thiết lập trang chỉ định trước khi phân trang
            ->forPage($page, $perpage)
            ->paginate($perpage)
            ->withQueryString()
            ->withPath(env('APP_URL') . ($extend['path'] ?? ''));
    }

}