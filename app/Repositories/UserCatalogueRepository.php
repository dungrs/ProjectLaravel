<?php

namespace App\Repositories;
use App\Repositories\Interfaces\UserCatalogueRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\UserCatalogue;

/**
 * Class UserService
 * @package App\Services
 */
class UserCatalogueRepository extends BaseRepository implements UserCatalogueRepositoryInterface
{
    protected $model;

    public function __construct(UserCatalogue $model) {
        $this->model = $model;
    }


    // public function getAllPaginate() {
    //     return User::paginate(15);
    // }

    // public function getUserCatagolue() {
    //     return $this->
    // }

}