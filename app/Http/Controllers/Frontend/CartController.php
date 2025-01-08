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
        
        // // Cart::instance('shopping')->destroy();
        // $cart = Cart::instance('shopping')->content();
    }
}
