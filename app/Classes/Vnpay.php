<?php

namespace App\Classes;

class Vnpay {
    
    public function __construct() {

    }

    public function payment($order) {
        $configVnpay = vnpayConfig();

        $vnp_Url = $configVnpay['vnp_Url'];
        $vnp_Returnurl = $configVnpay['vnp_Returnurl'];
        $vnp_TmnCode = $configVnpay['vnp_TmnCode'];//Mã website tại VNPAY 
        $vnp_HashSecret = $configVnpay['vnp_HashSecret']; //Chuỗi bí mật

        $vnp_TxnRef = $order->code; //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = (!empty($order->description)) ? $order->description : "Thanh toán hóa đơn #" . $order->code . " qua Vnpay";
        $vnp_OrderType = "billpayment";
        $vnp_Amount = ($order->cart['cartTotal'] - $order->promotion['discount']) * 100;
        $vnp_Locale = 'vn';

        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        
        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        $returnData = array(
            'code' => '00', 
            'message' => 'success',
            'url' => $vnp_Url
        );

        return $returnData;
    }
}