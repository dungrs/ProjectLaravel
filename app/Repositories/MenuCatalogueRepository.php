<?php

namespace App\Repositories;
use App\Repositories\Interfaces\MenuCatalogueRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\MenuCatalogue;

/**
 * Class UserService
 * @package App\Services
 */
class MenuCatalogueRepository extends BaseRepository implements MenuCatalogueRepositoryInterface
{
    protected $model;

    public function __construct(MenuCatalogue $model) {
        $this->model = $model;
    }

}