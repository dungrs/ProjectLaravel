<?php

namespace App\Classes;

class Momo {
    
    public function __construct() {

    }

    public function payment($order) {
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        $momoConfig = momoConfig();
    
        $partnerCode = $momoConfig['partnerCode'];
        $accessKey = $momoConfig['accessKey'];
        $secretKey = $momoConfig['secretKey'];
        $orderInfo = (!empty($order->description)) ? $order->description : "Thanh toán hóa đơn #" . $order->code . " qua Momo";
        $amount = $order->cart['cartTotal'] - $order->promotion['discount'];
        
        $redirectUrl = writeUrl('return/momo', true, true);
        $ipnUrl = writeUrl('return/ipn', true, true);
        $extraData = "";

        $orderId = $order->code; // Mã đơn hàng

        $requestId = time() . "";
        $requestType = "payWithATM";
        
        //before sign HMAC SHA256 signature
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        $data = array(
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            "storeId" => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        );
    
        $result = execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);  // decode json
        $jsonResult['url'] = $jsonResult['payUrl'];
        return $jsonResult;
    }
}
