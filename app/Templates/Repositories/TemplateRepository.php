<?php

namespace App\Repositories;
use App\Repositories\BaseRepository;
use App\Models\{Module};
use App\Repositories\Interfaces\{Module}RepositoryInterface;

/**
 * Class {Module}Service
 * @package App\Services
 */
class {Module}Repository extends BaseRepository implements {Module}RepositoryInterface
{
    protected $model;

    public function __construct({Module} $model) {
        $this->model = $model;
    }

    public function get{Module}ById(int $id = 0,int $language_id = 0) {
        return $this->model->select([
            '{module}s.id',
            '{module}s.{module}_catalogue_id',
            '{module}s.image',
            '{module}s.icon',
            '{module}s.album',
            '{module}s.publish',
            '{module}s.follow',
            'tb2.name',
            'tb2.description',
            'tb2.content',
            'tb2.meta_title',
            'tb2.meta_keyword',
            'tb2.meta_description',
            'tb2.canonical'
        ])
        ->join('{module}_language as tb2', 'tb2.{module}_id', '=', '{module}s.id')
        ->with('{module}_catalogues')
        ->where('tb2.language_id', '=', $language_id)
        ->find($id);
    }

}