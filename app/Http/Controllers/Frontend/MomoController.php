<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;

use App\Repositories\SystemRepository;

use App\Services\OrderService;

use Illuminate\Http\Request;

class MomoController extends FrontendController
{   

    protected $orderService;

    public function __construct(    
        SystemRepository $systemRepository,
        OrderService $orderService
    ) {
        parent::__construct($systemRepository);
        $this->orderService = $orderService;
    }

    public function momo_return(Request $request) {
        $momoConfig = momoConfig();
        $system = $this->getSystem();
        $secretKey = $momoConfig['secretKey'];
        $partnerCode = $momoConfig['partnerCode'];
        $accessKey = $momoConfig['accessKey'];
    
        if (!empty($_GET)) {

            // Khởi tạo biến $rawData
            $rawData = "accessKey=" . $accessKey;
            $rawData .= "&amount=" . $_GET['amount'];
            $rawData .= "&extraData=" . $_GET['extraData'];
            $rawData .= "&message=" . $_GET['message'];
            $rawData .= "&orderId=" . $_GET['orderId'];
            $rawData .= "&orderInfo=" . $_GET['orderInfo'];
            $rawData .= "&orderType=" . $_GET['orderType'];
            $rawData .= "&partnerCode=" . $_GET['partnerCode'];
            $rawData .= "&payType=" . $_GET['payType'];
            $rawData .= "&requestId=" . $_GET['requestId'];
            $rawData .= "&responseTime=" . $_GET['responseTime'];
            $rawData .= "&resultCode=" . $_GET['resultCode'];
            $rawData .= "&transId=" . $_GET['transId'];

    
            $partnerSignature = hash_hmac("sha256", $rawData, $secretKey);
            $m2signature = $_GET['signature'];
    
            $seo = [
                'meta_title' => 'Thông tin thanh toán đơn hàng #' . $_GET['orderId'],
                'meta_keyword' => '',
                'meta_description' => '',
                'meta_image' => '',
                'canonical' => writeUrl('cart/success', true, true)
            ];



            $condition = [['orders.code', '=', $_GET['orderId']]];
            $order = $this->orderService->getOrder($condition);
                        
            if ($m2signature == $partnerSignature) {
                if ($_GET['resultCode'] == 0) {
                    $payload['payment'] = 'paid';
                    $payload['confirm'] = 'confirm';
                } else {
                    $payload['payment'] = 'failed';
                    $payload['confirm'] = 'confirm';
                }
                $this->orderService->updatePaymentOnline($payload, $order->first());
            } 

            $template = 'frontend.cart.component.momo';
            $data = [
                'template' => $template,
                'm2signature' => $m2signature,
                'partnerSignature' => $partnerSignature,
                'order' => $order,
                'resultCode' => $_GET['resultCode'],
                'message' => $_GET['message'],
            ];
            
            $this->mail($order->first()->code, $data);
            return view('frontend.cart.success', compact(
                'seo', 'system', 'data'
            ));
        };
    }
    
    public function vnpay_ipn() {
        http_response_code(200); //200 - Everything will be 200 Oke
        $momoConfig = momoConfig();
        $secretKey = $momoConfig['secretKey'];
        $partnerCode = $momoConfig['partnerCode'];
        $accessKey = $momoConfig['accessKey'];
        if (!empty($_POST)) {
            $response = array();
            try {
                $rawData = "accessKey=" . $accessKey;
                $rawData .= "&amount=" . $_POST['amount'];
                $rawData .= "&extraData=" . $_POST['extraData'];
                $rawData .= "&message=" . $_POST['message'];
                $rawData .= "&orderId=" . $_POST['orderId'];
                $rawData .= "&orderInfo=" . $_POST['orderInfo'];
                $rawData .= "&orderType=" . $_POST['orderType'];
                $rawData .= "&partnerCode=" . $_POST['partnerCode'];
                $rawData .= "&payType=" . $_POST['payType'];
                $rawData .= "&requestId=" . $_POST['requestId'];
                $rawData .= "&responseTime=" . $_POST['responseTime'];
                $rawData .= "&resultCode=" . $_POST['resultCode'];
                $rawData .= "&transId=" . $_POST['transId'];

                $partnerSignature = hash_hmac("sha256", $rawData, $secretKey);
                $m2signature = $_POST['signature'];


                if ($m2signature == $partnerSignature) {
                    // $condition = [
                    //     ['orders.code', '=', $_POST['orderId']],
                    // ];
                    // $order = $this->orderService->getOrder($condition)->first();
                    // if ($_POST['resultCode'] == 0) {
                    //     $payload['payment'] = 'paid';
                    //     $payload['confirm'] = 'confirm';
                    // } else {
                    //     $payload['payment'] = 'failed';
                    //     $payload['confirm'] = 'confirm';
                    // }
                    // $this->orderService->updateVnpay($payload, $order);
                } else {
                    $result = '<div class="alert alert-danger">This transaction could be hacked, please check your signature and returned signature</div>';
                }

            } catch (\Exception $e) {
                echo $response['message'] = $e;
            }

            $debugger = array();
            $debugger['rawData'] = $rawData;
            $debugger['momoSignature'] = $m2signature;
            $debugger['partnerSignature'] = $partnerSignature;

            if ($m2signature == $partnerSignature) {
                $response['message'] = "Received payment result success";
            } else {
                $response['message'] = "ERROR! Fail checksum";
            }
            $response['debugger'] = $debugger;
            echo json_encode($response);
        }

    }
}
