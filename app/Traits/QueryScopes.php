<?php

namespace App\Traits;

trait QueryScopes {
    /**
     * Khi chúng ta khai báo các method không thuộc trong Builder
     * PHP sẽ tìm các method có đuôi scope trong Model để thực hiện
     */

    /**
     * Không được sử dụng constructor để tránh xung đột với các với model khi sử dụng
     */
    public function scopeKeyword($query, $keyword, $fieldSearch = []) {
        if (!empty($keyword)) {
            if (count($fieldSearch) > 0) {
                foreach($fieldSearch as $key => $val) {
                    $query->orWhere($val, 'LIKE', '%' . $keyword . '%');
                }
            } else {
                $query->where('name', 'LIKE', '%' . $keyword . '%');
            }
        }
        return $query;
    }

    public function scopePublish($query, $keyword) {
        if (!empty($keyword)) {
            $query->where('publish', '=', $keyword);
        }
        return $query;
    }

    public function scopeCustomWhere($query, $where = []) {
        if (!empty($where)) {
            foreach($where as $key => $val) {
                $query->where($val[0], $val[1], $val[2]);
            }
        }
        return $query;
    }

    // Thêm điều kiện whereRaw nếu có (whereRaw là viết các câu truy vấn phổ thông như trong sql)
    public function scopeCustomWhereRaw($query, $rawQuery = []) {
        if (!empty($rawQuery) && is_array($rawQuery)) {
            foreach($rawQuery as $key => $val) {
                $query->whereRaw($val[0], $val[1]);
            }
        }
        return $query;
    }

    // Thêm vào mối quan hệ nếu có để tối giản việc truy vấn và truy xuất dữ liệu nhanh hơn
    public function scopeRelationCount($query, $relations) {
        if (!empty($relations)) {
            foreach($relations as $item) {
                // Tối giản việc truy vấn
                $query->withCount($item);
            }
        }

        return $query;
    }

    public function scopeRelation($query, $relations) {
        if (!empty($relations)) {
            foreach($relations as $item) {
                // Tối giản việc truy vấn
                $query->with($item);
            }
        }

        return $query;
    }

    // Thêm điều kiện join nếu có
    public function scopeCustomJoin($query, $join) {
        if (isset($join) && is_array($join) && count($join)) {
            foreach($join as $key => $val) {
                $query->join($val[0], $val[1], $val[2], $val[3]);
            }
        }
        return $query;
    }

    // Thêm điều kiện groupBy nếu có
    public function scopeExtendCustomGroupBy($query, $groupBy) {
        if (isset($groupBy) && !empty($groupBy)) {
            $query->groupBy($groupBy);
        }
        return $query;
    }

    public function scopeExtendCustomOrderBy($query, $orderBy) {
        if (isset($orderBy) && !empty($orderBy)) {
            $query->orderBy($orderBy[0], $orderBy[1]);
        }
        return $query;
    }

}