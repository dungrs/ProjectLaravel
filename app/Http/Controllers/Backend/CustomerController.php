<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\CustomerService as CustomerService;
use App\Repositories\ProvinceRepository as ProvinceRepository;
use App\Repositories\CustomerCatalogueRepository as CustomerCatalogueRepository;
use App\Repositories\CustomerRepository as CustomerRepository;
use App\Repositories\SourceRepository as SourceRepository;

use App\Http\Request\CustomerGroup\StoreCustomerRequest;
use App\Http\Request\CustomerGroup\UpdateCustomerRequest;

class CustomerController extends Controller
{   
    protected $customerService;
    protected $provinceRepository;
    protected $customerRepository;
    protected $customerCatalogueRepository;
    protected $sourceRepository;
    
    public function __construct(
        CustomerService $customerService, 
        ProvinceRepository $provinceRepository, 
        CustomerRepository $customerRepository, 
        CustomerCatalogueRepository $customerCatalogueRepository,
        SourceRepository $sourceRepository
        ) {
        $this->customerService = $customerService;
        $this->provinceRepository = $provinceRepository;
        $this->customerRepository = $customerRepository;
        $this->customerCatalogueRepository = $customerCatalogueRepository;
        $this->sourceRepository = $sourceRepository;
    }

    public function index(Request $request) {
        $this->authorize('modules', 'customer.index');
        $customers = $this ->customerService->paginate($request);
        $sources = $this->sourceRepository->all();
        $customerCatalogues = $this->customerCatalogueRepository->all();
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ], 
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Customer'
        ];
        $config['seo'] =  __('messages.customer');
        $template = 'backend.customer.customer.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'customers',
            'customerCatalogues',
            'sources'
        ));
    }

    public function create() {
        $this->authorize('modules', 'customer.create');
        $config = $this->config();
        $config['seo'] =  __('messages.customer');
        $config['method'] = 'create';
        $provinces = $this->provinceRepository->all();
        $customerCatalogues = $this->customerCatalogueRepository->all();
        $sources = $this->sourceRepository->all();
        $template = 'backend.customer.customer.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'provinces',
            'customerCatalogues',
            'sources'
        ));
    }

    public function store(StoreCustomerRequest $request) {
        if ($this->customerService->create($request)) {
            return redirect() -> route('customer.index') -> with('success', 'Thêm mới bản ghi thành công');
        } else {
            return redirect() -> route('customer.index') -> with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
        }
    }

    public function edit($id) {
        $this->authorize('modules', 'customer.update');
        $config = $this->config();
        $template = 'backend.customer.customer.store';
        $config['seo'] =  __('messages.customer');
        $config['method'] = 'edit';
        $provinces = $this->provinceRepository->all();
        $customerCatalogues = $this->customerCatalogueRepository->all();
        $sources = $this->sourceRepository->all();
        $customer = $this->customerRepository->findById($id);
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'provinces',
            'customer',
            'customerCatalogues',
            'sources'
        ));
    }

    public function update($id, UpdateCustomerRequest $request) {
        if ($this->customerService->update($id, $request)) {
            return redirect() -> route('customer.index') -> with('success', 'Cập nhật bản ghi thành công');
        } else {
            return redirect() -> route('customer.index') -> with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
        }
    }

    public function delete($id) {
        $this->authorize('modules', 'customer.destroy');
        $customer = $this->customerRepository->findById($id);
        $config['seo'] =  __('messages.customer');
        $template = 'backend.customer.customer.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'customer',
            'config'
        ));
    }

    public function destroy($id) {
        if ($this->customerService->delete($id)) {
            return redirect() -> route('customer.index') -> with('success', 'Xóa bản ghi thành công');
        } 
        return redirect() -> route('customer.index') -> with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    public function config() {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js',
                'backend/plugin/ckfinder/ckfinder.js',
                'backend/library/finder.js'
            ]
        ];
    }

}
