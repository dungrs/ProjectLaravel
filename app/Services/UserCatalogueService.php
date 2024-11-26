<?php

namespace App\Services;
use App\Services\Interfaces\UserCatalogueServiceInterface;
use App\Repositories\UserCatalogueRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\DB;


/**
 * Class UserService
 * @package App\Services
 */
class UserCatalogueService extends BaseService implements UserCatalogueServiceInterface
{   
    protected $userCatalogueRepository;
    protected $userRepository;

    public function __construct(UserCatalogueRepository $userCatalogueRepository, UserRepository $userRepository) {
        $this->userCatalogueRepository = $userCatalogueRepository;
        $this->userRepository = $userRepository;
    }

    public function paginate($request) {
        // Gửi mảng condition về UserCatalogueRepository xử lý
        $condition['keyword'] = addslashes($request -> input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perpage = 20;
        $userCatalogue = $this->userCatalogueRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perpage, 
            ['path' => 'user/catalogue/index'], 
            ['id', 'DESC'],
            [],
            ['users'],
        );
        return $userCatalogue;
    }

    public function create($request) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            // Lấy tất cả dữ liệu từ request
            $payload = $request->except('_token', 'send'); 

            $user = $this->userCatalogueRepository->create($payload);

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
            $user = $this->userCatalogueRepository->update($id, $payload);

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
            $user = $this->userCatalogueRepository->delete($id);
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function changeUserStatus($post, $value) {
        
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

            $this->userRepository->updateByWhereIn('user_catalogue_id', $array, $payload);
            
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
                $userCatalogue = $this->userCatalogueRepository->findById($key);
                $userCatalogue->permission()->sync($val);
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
