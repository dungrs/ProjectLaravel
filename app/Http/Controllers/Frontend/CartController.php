<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\SystemRepository;
use App\Repositories\ProvinceRepository;

use App\Http\Request\StoreCartRequest;

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
        $provinces = $this->provinceRepository->all();
        $reCalculateCart = $this->cartService->reCalculate();
        $cartPromotion = $this->cartService->cartPromotion($reCalculateCart['cartTotal']);
        $carts = $this->cartService->remakeCart($carts);
        $config = $this->config();
        $seo = [
            'meta_title' => 'Trang thanh toán đơn hàng',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => writeUrl('thanh-toan', true, true)
        ];

        $system = $this->getSystem();
        return view('frontend.cart.index', compact(
            'seo',
            'system',
            'config',
            'provinces',
            'carts',
            'cartPromotion',
            'reCalculateCart'
        ));
    }

    public function store(StoreCartRequest $request) {
        if ($this->cartService->order($request)) {
            return redirect()->route('cart.success')->with('success', 'Đặt hàng thành công');
        } else {
            return redirect()->route('cart.checkout')->with('error', 'Đặt hàng không thành công. Hãy thử lại');
        }
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
