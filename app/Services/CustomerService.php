<?php

namespace App\Services;
use App\Services\Interfaces\CustomerServiceInterface;
use App\Repositories\CustomerRepository;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


/**
 * Class CustomerService
 * @package App\Services
 */
class CustomerService extends BaseService implements CustomerServiceInterface
{   
    protected $customerRepository;

    public function __construct(CustomerRepository $customerRepository) {
        $this->customerRepository = $customerRepository;
    }

    public function paginate($request) {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $condition['customer_catalogue_id'] = $request->integer('customer_catalogue_id');
        $condition['source_id'] = $request->integer('source_id');
        $perpage = $request->integer('perpage');
        
        $join = [
            'customer_catalogues as uc', // Tên bảng
            'uc.id', '=', 'customers.customer_catalogue_id' // Điều kiện nối
        ];
    
        $customer = $this->customerRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perpage, 
            ['path' => 'customer/index'],
            ['id', 'DESC'],
            $join, // Truyền đúng tham số join
            ['customer_catalogues']
        );
    
        return $customer;
    }

    public function create($request) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            // Lấy tất cả dữ liệu từ request
            $payload = $request->except('_token', 'send', 're_password'); 
            $payload['birthday'] = $this->convertBirthdayDate($payload['birthday']);  
            $payload['password'] = Hash::make($payload['password']);
            $this->customerRepository->create($payload);

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
            $payload['birthday'] = $this->convertBirthdayDate($payload['birthday']);
            $this->customerRepository->update($id, $payload);
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
            $customer = $this->customerRepository->delete($id);
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function updateStatus($post = []) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $payload = [
                $post["field"] => (($post['value'] == 1) ? 2 : 1)
            ];
            $customer = $this->customerRepository->update($post['modelId'], $payload);
            
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function updateStatusAll($post = []) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $customerIds = $post['id'];
    
            $customersToUpdate = $this->customerRepository->getCustomersForUpdate($customerIds);
    
            $filteredCustomerIds = $customersToUpdate->filter(function($customer) {
                return $customer->customer_catalogues->publish != 1;
            })->pluck('id')->toArray();
    
            if (!empty($filteredCustomerIds)) {
                $payload = [
                    $post["field"] => $post["value"]
                ];
                $this->customerRepository->updateByWhereIn('id', $filteredCustomerIds, $payload);
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

    private function convertBirthdayDate($birthday = '') {
        $carbonDate = Carbon::createFromFormat('Y-m-d', $birthday);
        $birthday = $carbonDate->format('Y-m-d H:i:s');
        return $birthday;
    }

    private function paginateSelect() {
        return [
            'customers.id', 
            'customers.name', 
            'customers.email', 
            'customers.phone', 
            'customers.address', 
            'customers.publish', 
            'customers.customer_catalogue_id', 
            'customers.source_id',
            'uc.publish as catalogue_publish'
        ];
    }
}
