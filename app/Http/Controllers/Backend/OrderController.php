<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\OrderService as OrderService;
use App\Repositories\OrderRepository as OrderRepository;

use App\Models\Language;


class OrderController extends Controller
{   
    protected $orderService;
    protected $orderRepository;
    protected $language;
    
    
    public function __construct(
        OrderService $orderService, 
        OrderRepository $orderRepository,
    ) { 
        // Thay vì khai báo ở route để xác 
        $this->middleware(function($request, $next) {
            // Lấy ra ngôn ngữ hiện tại     
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            // Sau khi xử lý xong nó sẽ truyền $request tới các middlewere khác để xử lý nốt phần còn lại
            // Rồi mới đến phần Controller
            return $next($request);
        });

        $this->orderService = $orderService;
        $this->orderRepository = $orderRepository;
    }

    public function index(Request $request) {
        $this->authorize('modules', 'order.index');
        $orders = $this->orderService->paginate($request);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js',
                'backend/js/plugins/daterangepicker/daterangepicker.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ], 
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'backend/css/plugins/daterangepicker/daterangepicker-bs3.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
            ],
            'model' => 'Order',
        ];
        $config['seo'] = __('messages.order');
        $template = 'backend.order.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'orders',
        ));
    }
}
