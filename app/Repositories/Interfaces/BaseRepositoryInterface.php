<?php

namespace App\Repositories\Interfaces;

/**
 * Interface DistrictServiceInterface
 * @package App\Services\Interfaces
 */
interface BaseRepositoryInterface
{
    public function all(array $relation, string $selectRaw = '*');
    public function findById(int $modelId);
    public function create(array $payload = []);
    public function update(int $id = 0, array $payload = []);
    public function updateOrInsert(array $condition = [], array $payload = []);
    public function updateByWhereIn(string $whereInField = '', array $whereIn = [], array $payload = []);
    public function updateByWhere(array $condition = [], array $payload = []);
    public function delete(int $id = 0);
    public function pagination(
        array $column = ['*'], 
        array $condition = [], 
        int $perpage = 1, 
        array $extend = [], 
        array $orderBy = ['id', 'DESC'],
        array $join = [], 
        array $relations = [],
        array $rawQuery = []
    );
    public function createPivot($model, array $payload, string $relation);
    public function deleteByCondition(array $condition = [], $forceDelete = true);
    public function createBatch(array $payload = []);
    public function findByCondition(
        $condition, 
        $flag = false, 
        array $joins = [], 
        array $orderBy = [], 
        array $select = ['*'], 
        $paginate = null, // Thêm tham số paginate, nếu có giá trị sẽ sử dụng phân trang
        array $relations = [], // Thêm tham số để chứa các mối quan hệ cần eager load
        array $groupBy = [] // Thêm tham số để hỗ trợ groupBy
    ) ;
    public function findByWhereHas(array $condition = [], string $relation = '', string $alias = '');
}
