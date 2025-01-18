<?php

namespace App\Repositories;
use App\Repositories\Interfaces\ReviewRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Review;

/**
 * Class UserService
 * @package App\Services
 */
class ReviewRepository extends BaseRepository implements ReviewRepositoryInterface
{
    protected $model;

    public function __construct(Review $model) {
        $this->model = $model;
    }
}