<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;

use App\Repositories\SystemRepository;
use App\Repositories\ProvinceRepository;
use App\Repositories\OrderRepository;

use App\Http\Request\StoreCartRequest;

use App\Services\CartService;

use Gloudemans\Shoppingcart\Facades\Cart;

class CartController extends FrontendController
{   

    protected $slideService;
    protected $cartService;
    protected $provinceRepository;
    protected $orderRepository;

    public function __construct(
        SystemRepository $systemRepository,
        ProvinceRepository $provinceRepository,
        OrderRepository $orderRepository,
        CartService $cartService,
    ) {
        parent::__construct($systemRepository);
        $this->provinceRepository = $provinceRepository;
        $this->orderRepository = $orderRepository;
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
        $order = $this->cartService->order($request);
        if ($order['flag']) {
            return redirect()->route('cart.success', ['code' => $order['order']->code])->with('success', 'Đặt hàng thành công');
        }
        return redirect()->route('cart.checkout')->with('error', 'Đặt hàng không thành công. Hãy thử lại');
    }

    public function success($code) {
        $order = $this->orderRepository->findByCondition(
            [
                ['code', '=', $code],
            ],
            true,
            [
                [
                    'table' => 'order_product as op',
                    'on' => ['op.order_id', 'orders.id'] 
                ]
            ],
            
            ['id' => 'ASC'],
            [
                'orders.*', 
                'op.name',
                'op.uuid',
                'op.qty',
                'op.price',
                'op.price_original' 
            ]
        );
        $seo = [
            'meta_title' => 'Thanh toán đơn hàng thành công',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => writeUrl('cart/success', true, true)
        ];

        $system = $this->getSystem();
        $config = $this->config();
        return view('frontend.cart.success', compact(
            'seo',
            'system',
            'config',
            'order',
        ));
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
