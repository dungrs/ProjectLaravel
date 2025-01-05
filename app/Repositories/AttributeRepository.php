<?php

namespace App\Repositories;
use App\Repositories\BaseRepository;
use App\Models\Attribute;
use App\Repositories\Interfaces\AttributeRepositoryInterface;
use Illuminate\Support\Facades\DB;


/**
 * Class AttributeService
 * @package App\Services
 */
class AttributeRepository extends BaseRepository implements AttributeRepositoryInterface
{
    protected $model;

    public function __construct(Attribute $model) {
        $this->model = $model;
    }

    public function getAttributeById(int $id = 0,int $language_id = 0) {
        return $this->model->select([
            'attributes.id',
            'attributes.attribute_catalogue_id',
            'attributes.image',
            'attributes.icon',
            'attributes.album',
            'attributes.publish',
            'attributes.follow',
            'tb2.name',
            'tb2.description',
            'tb2.content',
            'tb2.meta_title',
            'tb2.meta_keyword',
            'tb2.meta_description',
            'tb2.canonical'
        ])
        ->join('attribute_language as tb2', 'tb2.attribute_id', '=', 'attributes.id')
        ->with('attribute_catalogues')
        ->where('tb2.language_id', '=', $language_id)
        ->find($id);
    }

    public function searchAttributes(string $keyword = '', array $option = [], int $languageId)
    {
        return DB::table('attributes')
            ->join('attribute_catalogue_attribute', 'attributes.id', '=', 'attribute_catalogue_attribute.attribute_id')
            ->join('attribute_language', 'attributes.id', '=', 'attribute_language.attribute_id')
            ->where('attribute_catalogue_attribute.attribute_catalogue_id', $option['attributeCatalogueId'])
            ->where('attribute_language.name', 'LIKE', '%' . $keyword . '%')
            ->where('attribute_language.language_id', $languageId)
            ->select('attributes.id', 'attribute_language.name') // Chọn các trường từ bảng attribute_language
            ->get();
    }

    public function findAttributeByIdArray(array $attributeArray = [], int $languageId = 1) {
        return $this->model->select([
            'attributes.id',
            'attribute_language.name',
            'attributes.attribute_catalogue_id'
        ])
        ->join('attribute_language as attribute_language', 'attribute_language.attribute_id', '=', 'attributes.id')
        ->where('attribute_language.language_id', $languageId)
        ->whereIn('attribute_language.attribute_id', $attributeArray)
        ->get();
    }
}