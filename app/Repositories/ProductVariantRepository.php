<?php

namespace App\Repositories;
use App\Repositories\BaseRepository;
use App\Models\ProductVariant;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface;

/**
 * Class ProductVariantService
 * @package App\Services
 */
class ProductVariantRepository extends BaseRepository implements ProductVariantRepositoryInterface
{
    protected $model;

    public function __construct(ProductVariant $model) {
        $this->model = $model;
    }

}