<?php

namespace App\Repositories;
use App\Repositories\BaseRepository;
use App\Models\Source;
use App\Repositories\Interfaces\SourceRepositoryInterface;

/**
 * Class SourceRepository
 * @package App\Repositorys
 */
class SourceRepository extends BaseRepository implements SourceRepositoryInterface
{
    protected $model;

    public function __construct(Source $model) {
        $this->model = $model;
    }
}