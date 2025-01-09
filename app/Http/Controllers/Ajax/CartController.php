<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\FrontendController;

use App\Repositories\SystemRepository;

use App\Services\CartService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class CartController extends FrontendController
{   
    protected $cartService;

    public function __construct(
        SystemRepository $systemRepository,
        CartService $cartService
    ) {
        parent::__construct($systemRepository);
        $this->cartService = $cartService;
    }

    public function create(Request $request) {
        $flag = $this->cartService->create($request, $this->language);
        $cart = Cart::instance('shopping')->content();
        return response()->json([
            'cart' => $cart,
            'messages' => 'Thêm sản phẩm vào giỏ hàng thành công',
            'code' => $flag ? 10 : 11
        ]);
    }

    public function update(Request $request) {
        $response = $this->cartService->update($request, $this->language);
        return response()->json([
            'response' => $response,
            'messages' => 'Cập nhật số lượng thành công',
            'code' => $response['flag'] ? 10 : 11
        ]);
    }

    public function delete(Request $request) {
        $response = $this->cartService->delete($request, $this->language);
        return response()->json([
            'response' => $response,
            'messages' => 'Xóa sản phẩm khỏi giỏ hàng thành công',
            'code' => $response['flag'] ? 10 : 11
        ]);
    }
}
