<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;

use App\Repositories\SystemRepository;

use App\Services\OrderService;

use Illuminate\Http\Request;

use Srmklive\PayPal\Services\PayPal as PayPalClient;


class PaypalController extends FrontendController
{   

    protected $orderService;

    public function __construct(    
        SystemRepository $systemRepository,
        OrderService $orderService
    ) {
        parent::__construct($systemRepository);
        $this->orderService = $orderService;
    }

    public function success(Request $request) {
        $provider = new PayPalClient;

        $system = $this->getSystem();
        $orderId = $request->orderId;
        $seo = [
            'meta_title' => 'Thông tin thanh toán đơn hàng #' . $orderId,
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => writeUrl('cart/success', true, true)
        ];

        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request->token);

        $condition = [['orders.code', '=', $orderId]];
        $order = $this->orderService->getOrder($condition);
                    
        if (isset($response['status']) && $response['data'] == "COMPLETED") {
                $payload['payment'] = 'paid';
                $payload['confirm'] = 'confirm';
        } else {
            $payload['payment'] = 'failed';
            $payload['confirm'] = 'confirm';
        } 
        $this->orderService->updatePaymentOnline($payload, $order->first());

        $template = 'frontend.cart.component.paypal';
        $data = [
            'order' => $order,
            'template' => $template
        ];
        
        $this->mail($order->first()->code, $data);
        return view('frontend.cart.success', compact(
            'seo', 'system', 'data'
        ));
    }

    public function cancel(Request $request) {

    }
}
