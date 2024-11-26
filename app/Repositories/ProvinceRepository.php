<?php

namespace App\Repositories;
use App\Repositories\BaseRepository;
use App\Models\Province;
use App\Repositories\Interfaces\ProvinceRepositoryInterface;

/**
 * Class ProvinceService
 * @package App\Services
 */
class ProvinceRepository extends BaseRepository implements ProvinceRepositoryInterface
{   
    protected $model;

    public function __construct(Province $model) {
        $this->model = $model;
    }
}
