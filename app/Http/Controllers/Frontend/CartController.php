<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;

use App\Repositories\SystemRepository;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartController extends FrontendController
{   

    protected $slideService;
    protected $widgetService;

    public function __construct(
        SystemRepository $systemRepository,
    ) {
        parent::__construct($systemRepository);

    }

    public function checkout() {
        // Cart::instance('shopping')->destroy();
        // $cart = Cart::instance('shopping')->content();
        $config = $this->config();
        $seo = [
            'meta_title' => 'Trang thanh toán đơn hàng',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => config('thanh-toan', true, true)
        ];

        $system = $this->getSystem();
        return view('frontend.cart.index', compact(
            'seo',
            'system',
            'config'
        ));

    }

    public function config() {
        return [

        ];
    }
}
