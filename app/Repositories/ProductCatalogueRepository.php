<?php

namespace App\Repositories;
use App\Repositories\BaseRepository;
use App\Models\ProductCatalogue;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface;

/**
 * Class ProductCatalogueService
 * @package App\Services
 */
class ProductCatalogueRepository extends BaseRepository implements ProductCatalogueRepositoryInterface
{
    protected $model;

    public function __construct(ProductCatalogue $model) {
        $this->model = $model;
    }

    public function getProductCatalogueById(int $id = 0,int $language_id = 0) {
        return $this->model->select([
            'product_catalogues.id',
            'product_catalogues.lft',
            'product_catalogues.rgt',
            'product_catalogues.parent_id',
            'product_catalogues.image',
            'product_catalogues.icon',
            'product_catalogues.album',
            'product_catalogues.publish',
            'product_catalogues.follow',
            'tb2.name',
            'tb2.description',
            'tb2.content',
            'tb2.meta_title',
            'tb2.meta_keyword',
            'tb2.meta_description',
            'tb2.canonical'
        ])
        ->join('product_catalogue_language as tb2', 'tb2.product_catalogue_id', '=', 'product_catalogues.id')
        ->where('tb2.language_id', '=', $language_id)
        ->find($id);
    }

}