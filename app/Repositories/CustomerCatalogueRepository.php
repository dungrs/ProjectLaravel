<?php

namespace App\Repositories;
use App\Repositories\Interfaces\CustomerCatalogueRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\CustomerCatalogue;

/**
 * Class UserService
 * @package App\Services
 */
class CustomerCatalogueRepository extends BaseRepository implements CustomerCatalogueRepositoryInterface
{
    protected $model;

    public function __construct(CustomerCatalogue $model) {
        $this->model = $model;
    }
}