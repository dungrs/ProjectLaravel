<?php

namespace App\Repositories;
use App\Repositories\Interfaces\PromotionRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Promotion;

/**
 * Class UserService
 * @package App\Services
 */
class PromotionRepository extends BaseRepository implements PromotionRepositoryInterface
{
    protected $model;

    public function __construct(Promotion $model) {
        $this->model = $model;
    }

    public function getPromotionByCartTotal() {
        return $this->model
                ->where('promotions.publish', 2)
                ->where('promotions.method', 'order_amount_range')
                ->whereDate('promotions.end_date', '>=', now())
                ->whereDate('promotions.start_date', '<=', now())
                ->get();
    }

}