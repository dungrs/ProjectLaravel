<?php

namespace App\Repositories;
use App\Repositories\Interfaces\WidgetRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Widget;

/**
 * Class UserService
 * @package App\Services
 */
class WidgetRepository extends BaseRepository implements WidgetRepositoryInterface
{
    protected $model;

    public function __construct(Widget $model) {
        $this->model = $model;
    }

}