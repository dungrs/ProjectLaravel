<?php

namespace App\Repositories;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\User;

/**
 * Class UserService
 * @package App\Services
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model) {
        $this->model = $model;
    }


    // public function getAllPaginate() {
    //     return User::paginate(15);
    // }

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
                $query->where('users.name', 'LIKE', '%' . $condition['keyword'] . '%')
                        ->orWhere('users.email', 'LIKE', '%' . $condition['keyword'] . '%')
                        ->orWhere('users.address', 'LIKE', '%' . $condition['keyword'] . '%')
                        ->orWhere('users.phone', 'LIKE', '%' . $condition['keyword'] . '%');
            }

            if (isset($condition['publish']) && !empty($condition['publish']) && $condition['publish'] != 0) {
                $query->where('users.publish', '=', $condition['publish']);
            }
        
        // Nạp từ bảng user_catalogues
        })->with('user_catalogues');

        if (!empty($join)) {
            $query->join(...$join);
        }
 
        return $query->paginate($perpage)->withQueryString()->withPath(env('APP_URL'). $extend['path']);
    }

    public function getUsersForUpdate(array $userIds) {
        return $this->model->whereIn('id', $userIds)
            ->with('user_catalogues') // Load mối quan hệ với bảng user_catalogues
            ->get();
    }

}