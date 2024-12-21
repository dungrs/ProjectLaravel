<?php

namespace App\Services;
use App\Services\Interfaces\CustomerCatalogueServiceInterface;
use App\Repositories\CustomerCatalogueRepository;
use App\Repositories\CustomerRepository;
use Exception;
use Illuminate\Support\Facades\DB;


/**
 * Class CustomerService
 * @package App\Services
 */
class CustomerCatalogueService extends BaseService implements CustomerCatalogueServiceInterface
{   
    protected $customerCatalogueRepository;
    protected $customerRepository;

    public function __construct(CustomerCatalogueRepository $customerCatalogueRepository, CustomerRepository $customerRepository) {
        $this->customerCatalogueRepository = $customerCatalogueRepository;
        $this->customerRepository = $customerRepository;
    }

    public function paginate($request) {
        $condition['keyword'] = addslashes($request -> input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perpage = 20;
        $customerCatalogue = $this->customerCatalogueRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perpage, 
            ['path' => 'customer/catalogue/index'], 
            ['id', 'DESC'],
            [],
            ['customers'],
        );
        return $customerCatalogue;
    }

    public function create($request) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            // Lấy tất cả dữ liệu từ request
            $payload = $request->except('_token', 'send'); 
            $this->customerCatalogueRepository->create($payload);
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function update($id, $request) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            // Lấy tất cả dữ liệu từ request
            $payload = $request->except('_token', 'send'); 
            $this->customerCatalogueRepository->update($id, $payload);
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function delete($id) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $customer = $this->customerCatalogueRepository->delete($id);
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function changeCustomerStatus($post, $value) {
        
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $array = [];
            if (isset($post['modelId'])) {
                $array[] = $post['modelId'];
            } else {
                $array = $post['id'];
            }

            $payload = [
                $post["field"] => $value
            ];

            $this->customerRepository->updateByWhereIn('customer_catalogue_id', $array, $payload);
            
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function setPermission($request) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $permissions = $request->input('permission');
            foreach($permissions as $key => $val) {
                $customerCatalogue = $this->customerCatalogueRepository->findById($key);
                $customerCatalogue->permission()->sync($val);
            }

            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    private function paginateSelect() {
        return ['id', 'name', 'description', 'publish'];
    }
}
