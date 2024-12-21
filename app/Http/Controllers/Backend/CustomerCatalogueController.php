<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\CustomerCatalogueService as CustomerCatalogueService;
use App\Repositories\CustomerCatalogueRepository as CustomerCatalogueRepository;
use App\Repositories\PermissionRepository;

use App\Http\Request\CustomerGroup\StoreCustomerCatalogueRequest;

class CustomerCatalogueController extends Controller
{   
    protected $customerCatalogueService;
    protected $customerCatalogueRepository;
    protected $permissionRepository;
    
    public function __construct(CustomerCatalogueService $customerCatalogueService, CustomerCatalogueRepository $customerCatalogueRepository, PermissionRepository $permissionRepository) {
        $this->customerCatalogueService = $customerCatalogueService;
        $this->customerCatalogueRepository = $customerCatalogueRepository;
        $this->permissionRepository = $permissionRepository;
    }

    public function index(Request $request) {
        $this->authorize('modules', 'customer.catalogue.index');
        $customerCatalogues = $this ->customerCatalogueService->paginate($request);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ], 
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'CustomerCatalogue'
        ];
        $config['seo'] = __('messages.customerCatalogue');
        $template = 'backend.customer.catalogue.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'customerCatalogues'
        ));
    }

    public function create() {
        $this->authorize('modules', 'customer.catalogue.create');
        $config['seo'] = __('messages.customerCatalogue');
        $config['method'] = 'create';
        $template = 'backend.customer.catalogue.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreCustomerCatalogueRequest $request) {
        if ($this->customerCatalogueService->create($request)) {
            return redirect() -> route('customer.catalogue.index') -> with('success', 'Thêm mới bản ghi thành công');
        } else {
            return redirect() -> route('customer.catalogue.index') -> with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
        }
    }

    public function edit($id) {
        $this->authorize('modules', 'customer.catalogue.update');
        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js',
            ]
        ];
        $template = 'backend.customer.catalogue.store';
        $config['seo'] = __('messages.customerCatalogue');
        $config['method'] = 'edit';
        $customerCatalogue = $this->customerCatalogueRepository->findById($id);
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'customerCatalogue'
        ));
    }

    public function update($id, StoreCustomerCatalogueRequest $request) {
        if ($this->customerCatalogueService->update($id, $request)) {
            return redirect() -> route('customer.catalogue.index') -> with('success', 'Cập nhật bản ghi thành công');
        } else {
            return redirect() -> route('customer.catalogue.index') -> with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
        }
    }

    public function delete($id) {
        $this->authorize('modules', 'customer.catalogue.destroy');
        $customerCatalogue = $this->customerCatalogueRepository->findById($id);
        $config['seo'] = __('messages.customerCatalogue');
        $template = 'backend.customer.catalogue.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'customerCatalogue',
            'config'
        ));
    }

    public function destroy($id) {
        if ($this->customerCatalogueService->delete($id)) {
            return redirect() -> route('customer.catalogue.index') -> with('success', 'Xóa bản ghi thành công');
        } 
        return redirect() -> route('customer.catalogue.index') -> with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    public function permission() {
        // $this->authorize('modules', 'customer.catalogue.permission');
        $customerCatalogues = $this->customerCatalogueRepository->all(['permission']);
        $config['seo'] = __('messages.customerCatalogue');
        return view('backend.dashboard.layout', compact(
            'template',
            'customerCatalogues',
            'config'
        ));
    }

    public function updatePermission(Request $request) {
        if ($this->customerCatalogueService->setPermission($request)) {
            return redirect()->route('customer.catalogue.index')->with("success", "Cập nhật quyền thành công");
        } else {
            return redirect()->route('customer.catalogue.index')->with("error", "Có vấn đề xảy ra xin vui lòng thử lại");
        }
    }
}
