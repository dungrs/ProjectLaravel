<?php

namespace App\Services;
use App\Services\Interfaces\UserServiceInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


/**
 * Class UserService
 * @package App\Services
 */
class UserService extends BaseService implements UserServiceInterface
{   
    protected $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function paginate($request) {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perpage = $request->integer('perpage');
        
        // Thêm điều kiện join vào bảng user_catalogues và lấy cột publish
        $join = [
            'user_catalogues as uc', // Tên bảng
            'uc.id', '=', 'users.user_catalogue_id' // Điều kiện nối
        ];
    
        $user = $this->userRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perpage, 
            ['path' => 'user/index'],
            ['id', 'DESC'],
            $join, // Truyền đúng tham số join
            ['user_catalogues']
        );
    
        return $user;
    }

    public function create($request) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            // Lấy tất cả dữ liệu từ request
            $payload = $request->except('_token', 'send', 're_password'); 
            $payload['birthday'] = $this->convertBirthdayDate($payload['birthday']);  
            $payload['password'] = Hash::make($payload['password']);

            $user = $this->userRepository->create($payload);

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
            $user = $this->userRepository->update($id, $payload);

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
            $user = $this->userRepository->delete($id);
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
            $user = $this->userRepository->update($post['modelId'], $payload);
            
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
            // Lấy danh sách ID người dùng cần kiểm tra
            $userIds = $post['id'];
    
            // Lấy danh sách các user với id nằm trong danh sách userIds và kiểm tra điều kiện publish
            $usersToUpdate = $this->userRepository->getUsersForUpdate($userIds);
    
            // Lọc ra các user có publish = 1 trong bảng user_catalogues
            $filteredUserIds = $usersToUpdate->filter(function($user) {
                return $user->user_catalogues->publish != 1;
            })->pluck('id')->toArray();
    
            if (!empty($filteredUserIds)) {
                $payload = [
                    $post["field"] => $post["value"]
                ];
                // Chỉ cập nhật cho những user có publish khác 1
                $this->userRepository->updateByWhereIn('id', $filteredUserIds, $payload);
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
            'users.id', 
            'users.name', 
            'users.email', 
            'users.phone', 
            'users.address', 
            'users.publish', 
            'users.user_catalogue_id', 
            'uc.publish as catalogue_publish'
        ];
    }
}
