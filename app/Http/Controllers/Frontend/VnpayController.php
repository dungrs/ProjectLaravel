<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;

use App\Repositories\SystemRepository;

use App\Services\OrderService;

use Illuminate\Http\Request;

class VnpayController extends FrontendController
{   

    protected $orderService;

    public function __construct(    
        SystemRepository $systemRepository,
        OrderService $orderService
    ) {
        parent::__construct($systemRepository);
        $this->orderService = $orderService;
    }

    public function vnpay_return(Request $request) {
        $configVnpay = vnpayConfig();
        $orderCode =  $request->input('vnp_TxnRef');
        $system = $this->getSystem();
        $seo = [
            'meta_title' => 'Thông tin thanh toán đơn hàng #' . $orderCode,
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => writeUrl('cart/success', true, true)
        ];


        $vnp_Url = $configVnpay['vnp_Url'];
        $vnp_Returnurl = $configVnpay['vnp_Returnurl'];
        $vnp_TmnCode = $configVnpay['vnp_TmnCode'];//Mã website tại VNPAY 
        $vnp_HashSecret = $configVnpay['vnp_HashSecret']; //Chuỗi bí mật
        $vnp_apiUrl = $configVnpay['vnp_apiUrl'];
        $apiUrl = $configVnpay['apiUrl'];
        $startTime = date("YmdHis");
        $expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));


        $vnp_SecureHash = $_GET['vnp_SecureHash'];
        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        if ($secureHash == $vnp_SecureHash) {
            $this->vnpay_ipn();
            $condition = [['orders.code', '=', $orderCode]];
            $order = $this->orderService->getOrder($condition);
            $template = 'frontend.cart.component.vnpay';
            $data = [
                'template' => $template,
                'secureHash' => $secureHash,
                'vnp_SecureHash' => $vnp_SecureHash,
                'order' => $order
            ];
            $this->mail($order->first()->code, $data);

            return view('frontend.cart.success', compact(
                'seo', 'system', 'data'
            ));
        } else {
            die("Chu ky khong hop le");
        }
    }

    public function vnpay_ipn() {
        /* Payment Notify
        * IPN URL: Ghi nhận kết quả thanh toán từ VNPAY
        * Các bước thực hiện:
        * Kiểm tra checksum 
        * Tìm giao dịch trong database
        * Kiểm tra số tiền giữa hai hệ thống
        * Kiểm tra tình trạng của giao dịch trước khi cập nhật
        * Cập nhật kết quả vào Database
        * Trả kết quả ghi nhận lại cho VNPAY
        */
        $configVnpay = vnpayConfig();
        $vnp_Url = $configVnpay['vnp_Url'];
        $vnp_Returnurl = $configVnpay['vnp_Returnurl'];
        $vnp_TmnCode = $configVnpay['vnp_TmnCode'];//Mã website tại VNPAY 
        $vnp_HashSecret = $configVnpay['vnp_HashSecret']; //Chuỗi bí mật
        $vnp_apiUrl = $configVnpay['vnp_apiUrl'];
        $apiUrl = $configVnpay['apiUrl'];
        $startTime = date("YmdHis");
        $expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));
        $inputData = array();
        $returnData = array();
        
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $vnpTranId = $inputData['vnp_TransactionNo']; //Mã giao dịch tại VNPAY
        $vnp_BankCode = $inputData['vnp_BankCode']; //Ngân hàng thanh toán
        $vnp_Amount = $inputData['vnp_Amount']/100; // Số tiền thanh toán VNPAY phản hồi
        
        $Status = 0; // Là trạng thái thanh toán của giao dịch chưa có IPN lưu tại hệ thống của merchant chiều khởi tạo URL thanh toán.
        $orderId = $inputData['vnp_TxnRef'];
        $condition = [
            ['orders.code', '=', $orderId],
        ];
        
        try {
            //Check Orderid    
            //Kiểm tra checksum của dữ liệu
            if ($secureHash == $vnp_SecureHash) {
                //Lấy thông tin đơn hàng lưu trong Database và kiểm tra trạng thái của đơn hàng, mã đơn hàng là: $orderId            
                //Việc kiểm tra trạng thái của đơn hàng giúp hệ thống không xử lý trùng lặp, xử lý nhiều lần một giao dịch
                //Giả sử: $order = mysqli_fetch_assoc($result);   
                $order = $this->orderService->getOrder($condition)->first();
                if ($order != NULL) {
                    $orderAmount = $order->cart['cartTotal'] - $order->promotion['discount'];
                    if($orderAmount == $vnp_Amount) //Kiểm tra số tiền thanh toán của giao dịch: giả sử số tiền kiểm tra là đúng. //$order["Amount"] == $vnp_Amount
                    {
                        if ($order->payment != null && $order->payment == 'unpaid') {
                            if ($inputData['vnp_ResponseCode'] == '00' || $inputData['vnp_TransactionStatus'] == '00') {
                                $payload['payment'] = 'paid';
                                $payload['confirm'] = 'confirm';
                            } else {
                                $payload['payment'] = 'failed';
                                $payload['confirm'] = 'confirm';
                            }
                            $this->orderService->updateVnpay($payload, $order);
                            $returnData['RspCode'] = '00';
                            $returnData['Message'] = 'Confirm Success';
                        } else {
                            $returnData['RspCode'] = '02';
                            $returnData['Message'] = 'Order already confirmed';
                        }
                    }
                    else {
                        $returnData['RspCode'] = '04';
                        $returnData['Message'] = 'invalid amount';
                    }
                } else {
                    $returnData['RspCode'] = '01';
                    $returnData['Message'] = 'Order not found';
                }
            } else {
                $returnData['RspCode'] = '97';
                $returnData['Message'] = 'Invalid signature';
            }
        } catch (\Exception $e) {
            $returnData['RspCode'] = '99';
            $returnData['Message'] = 'Unknow error';
        }
    }
}
