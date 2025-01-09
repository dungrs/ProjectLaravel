<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;

use App\Repositories\SystemRepository;
use App\Repositories\ProvinceRepository;

use App\Services\CartService;

use Gloudemans\Shoppingcart\Facades\Cart;

class CartController extends FrontendController
{   

    protected $slideService;
    protected $cartService;
    protected $provinceRepository;

    public function __construct(
        SystemRepository $systemRepository,
        ProvinceRepository $provinceRepository,
        CartService $cartService,
    ) {
        parent::__construct($systemRepository);
        $this->provinceRepository = $provinceRepository;
        $this->cartService = $cartService;
    }

    public function checkout() {
        // Cart::instance('shopping')->destroy();
        $carts = Cart::instance('shopping')->content();
        $carts = $this->cartService->remakeCart($carts);
        $provinces = $this->provinceRepository->all();
        $cartConfig = $this->cartConfig();
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
            'config',
            'provinces',
            'carts',
            'cartConfig'
        ));
    }

    private function cartConfig() {
        return [
            'cartTotal' => Cart::instance('shopping')->total(),
        ];
    }

    public function config() {
        return [
            'js' => [
                'backend/library/location.js',
                'frontend/core/library/cart.js',
            ]
        ];
    }
}
