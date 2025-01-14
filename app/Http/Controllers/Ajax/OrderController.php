<?php

namespace App\Http\Controllers\Ajax;


use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController
{   
    
    protected $orderService;

    public function __construct(
        OrderService $orderService
    ) {
        $this->orderService = $orderService;
    }


    public function update(Request $request) {
        
        if ($this->orderService->update($request)) {
            $condition = [
                ['orders.id', '=', $request->input('id')]
            ];
            $order = $this->orderService->getOrder($condition)->first();
            return response()->json([
                'code' => 10,
                'messages' => 'Cập nhật dữ liệu thành công!',
                'order' => $order
            ]);
        } 
        
        return response()->json([
            'code' => 11,
            'messages' => 'Cập nhật dữ liệu không thành công!'
        ]);
    }

}
