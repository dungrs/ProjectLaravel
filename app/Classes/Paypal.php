<?php

namespace App\Classes;

use Srmklive\PayPal\Services\PayPal as PayPalClient;

class Paypal
{
    protected $provider;

    public function __construct()
    {
        // Khởi tạo đối tượng PayPalClient
        $this->provider = new PayPalClient;
    }

    public function payment($order)
    {
        try {
            // Lấy access token
            
            $usd = 23000;
            $cartTotal = ($order->cart['cartTotal'] - $order->promotion['discount']);
            $paypalValue = number_format($cartTotal / $usd, 2, '.', '');
            
            $accessToken = $this->provider->getAccessToken();
            
            $data = [
                "intent" => "CAPTURE",
                "application_context" => [
                    'return_url' => route('paypal.success', ['orderId' => $order->code]),
                    'cancel_url' => route('paypal.cancel')
                ],
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => $paypalValue
                        ]
                    ]
                ]
            ];
            
            $response = $this->provider->createOrder($data);
            $res['url'] = '';
            if (!empty($response['id']) && $response['id'] != '') {
                foreach($response['links'] as $key => $val) {
                    if ($val['rel'] === 'approve') {
                        $res['url'] = $val['href'];
                        $res['resultCode'] = 0;
                    }
                }
            }

            return $res;
        } catch (\Exception $e) {
            // Xử lý ngoại lệ
            return 'Error: ' . $e->getMessage();
        }
    }
}
