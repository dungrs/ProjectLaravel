<?php

namespace App\Repositories;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class BaseService
 * @package App\Services
 */
class BaseRepository implements BaseRepositoryInterface
{   
    protected $model;
    
    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function create(array $payload = []) {
        // Tạo một bản ghi mới trong cơ sở dữ liệu bằng cách sử dụng dữ liệu từ mảng $payload
        // Phương thức create() trên $this->model sẽ chèn một bản ghi mới vào bảng tương ứng trong cơ sở dữ liệu
        $model = $this->model->create($payload);
    
        // Phương thức fresh() sẽ tải lại mô hình từ cơ sở dữ liệu để lấy các thuộc tính mới nhất
        // Điều này hữu ích khi bạn muốn đảm bảo rằng mô hình bạn vừa tạo có các giá trị chính xác
        return $model->fresh();
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
            ->keyword($condition['keyword'] ?? null)
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

    public function update(int $id = 0, array $payload = []) {
        $model = $this->findById($id);
        return $model->update($payload);
    }

    public function updateAndGetData(int $id = 0, array $payload = []) {
        $model = $this->findById($id);
        $model->fill($payload);
        $model->save();
        return $model;
    }

    public function updateOrInsert(array $condition = [], array $payload = []) {
        return $this->model->updateOrInsert($condition, $payload);
    }

    public function updateByWhereIn(string $whereInField = '', array $whereIn = [], array $payload = []) {
        return $this->model->whereIn($whereInField, $whereIn)->update($payload);
    }

    public function updateByWhere(array $condition = [], array $payload = []) {
        $query = $this->model->newQuery();
        foreach($condition as $key => $val) {
            $query->where($val[0], $val[1], $val[2]);
        }
        return $query->update($payload);
    }

    public function delete(int $id = 0) {
        return $this->findById($id)->delete();
    }

    public function forceDelete(int $id = 0) {
        return $this->findById($id)->forceDelete();
    }

    public function deleteByCondition(array $condition = [], $forceDelete = true) {
        $query = $this->model->newQuery();
        foreach($condition as $key => $val) {
            $query->where($val[0], $val[1], $val[2]);
        }

        return ($forceDelete === true) ? $query->forceDelete() : $query->delete();
    }

    public function all(array $relation = []) {
        return $this->model->with($relation)->get();
    }

    public function findById(int $modelId, array $column = ['*'], array $relation = []) {
        return $this->model->select($column)->with($relation)->findOrFail($modelId);
    }

    public function findByCondition(
        $condition, 
        $flag = false, 
        array $joins = [], 
        array $orderBy = [], 
        array $select = ['*'], 
        $paginate = null, // Thêm tham số paginate, nếu có giá trị sẽ sử dụng phân trang
        array $relations = [], // Thêm tham số để chứa các mối quan hệ cần eager load
        array $groupBy = [], // Thêm tham số để hỗ trợ groupBy
        int $limit = null
    ) {
        $query = $this->model->newQuery();
    
        $query->select($select);
    
        // Thực hiện join nếu có
        if (!empty($joins)) {
            foreach ($joins as $join) {
                $type = isset($join['type']) ? strtolower($join['type']) : 'inner';
                switch ($type) {
                    case 'left':
                        $query->leftJoin($join['table'], $join['on'][0], '=', $join['on'][1]);
                        break;
                    case 'right':
                        $query->rightJoin($join['table'], $join['on'][0], '=', $join['on'][1]);
                        break;
                    default:
                        $query->join($join['table'], $join['on'][0], '=', $join['on'][1]);
                }
            }
        }
    
        // Áp dụng các điều kiện where
        foreach ($condition as $val) {
            if ($val[1] == 'IN') {
                $query->whereIn($val[0], $val[2]);
            } else {
                $query->where($val[0], $val[1], $val[2]);
            }
        }
    
        // Áp dụng eager loading các mối quan hệ nếu có
        if (!empty($relations)) {
            $query->with($relations);
        }
    
        // Áp dụng groupBy nếu có
        if (!empty($groupBy)) {
            $query->groupBy($groupBy);
        }
    
        // Áp dụng sắp xếp nếu có
        if (!empty($orderBy)) {
            foreach ($orderBy as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        }

        if ($limit !== null) {
            $query->limit($limit); 
        }
    
        if ($paginate) {
            return $query->paginate($paginate); // Trả về kết quả phân trang với số lượng mỗi trang là tham số paginate
        }
    
        return ($flag == false) ? $query->first() : $query->get();
    }

    public function findByWhereHas(array $condition = [], string $relation = '', string $alias = '') {
        return  $this->model->with('languages')->whereHas($relation, function($query) use ($condition, $alias) {
            foreach ($condition as $val) {
                $query->where($alias.'.'.$val[0], $val[1], $val[2]);
            }
        })->first();
    }

    public function createPivot($model, array $payload, string $relation = '') {
        // Sẽ thêm một bản ghi mới vào bảng pivot với id của model được truyền vào và các giá trị từ $payload
        return $this->model->{$relation}()->attach($model->id, $payload);
    }

    public function createBatch(array $payload = []) {
        return $this->model->insert($payload);
    }
    
    public function recursveCategory(string $parameter = '', $table = '') {
        $table = $table . '_catalogues';
        $query = "
            WITH RECURSIVE category_tree AS (
                SELECT id, parent_id, deleted_at
                FROM $table
                WHERE id IN (?)
                UNION ALL
                SELECT c.id, c.parent_id, c.deleted_at
                FROM $table as c
                JOIN category_tree as ct ON ct.id = c.parent_id
            )
            SELECT id FROM category_tree WHERE deleted_at IS NULL
        ";
    
        // Thực thi truy vấn
        $result = DB::select($query, [$parameter]);
    
        // Chuyển kết quả thành mảng đơn giản
        $ids = array_map(function ($row) {
            return $row->id;
        }, $result);
    
        return $ids;
    }
}
