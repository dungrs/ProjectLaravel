<?php

namespace App\Services;
use App\Services\Interfaces\LanguageServiceInterface;
use App\Repositories\LanguageRepository;
use App\Repositories\RouterRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class UserService
 * @package App\Services
 */
class LanguageService extends BaseService implements LanguageServiceInterface
{   
    protected $languageRepository;
    protected $routerRepository;

    public function __construct(LanguageRepository $languageRepository, RouterRepository $routerRepository) {
        $this->languageRepository = $languageRepository;
        $this->routerRepository = $routerRepository;
    }

    public function paginate($request) {
        $condition['keyword'] = addslashes($request -> input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perpage = $request->integer('perpage');
        $language = $this->languageRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perpage, 
            ['path' => 'language/index'], 
        );
        // dd($language);
        return $language;
    }

    public function create($request) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
        try {
            // Lấy tất cả dữ liệu từ request
            $payload = $request->except('_token', 'send');
            $payload['user_id'] = Auth::id();
            $this->languageRepository->create($payload);

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
            $this->languageRepository->update($id, $payload);

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
            $this->languageRepository->delete($id);
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function switch($id) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $this->languageRepository->update($id, ['current' => 1]);
            $payload = ['current' => "0"];
            $where = [['id', '!=', $id]];
            $this->languageRepository->updateByWhere($where, $payload);
            
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function saveTranslate($option, $request) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $payload = [
                'name' => $request->input("translate_name"),
                'description' => $request->input("translate_description"),
                'content' => $request->input("translate_content"),
                'meta_title' => $request->input("translate_meta_title"),
                'meta_keyword' => $request->input("translate_meta_keyword"),
                'meta_description' => $request->input("translate_meta_description"),
                'canonical' => $request->input("translate_canonical"),
                $this->convertModelToField($option['model']) => $option['id'],
                'language_id' => $option['languageId']
            ];
            
            $controllerName = $option['model'] . 'Controller';
            $repositoryNamespace = 'App\Repositories\\'.ucfirst($option['model']).'Repository';
            if (class_exists($repositoryNamespace)) {
                $repositoryInstance = app($repositoryNamespace);
            }

            $model = $repositoryInstance->findById($option['id']);
            $model->languages()->detach([$option['languageId'], $model->id]);
            $repositoryInstance->createPivot($model, $payload, 'languages');

            $this->routerRepository->deleteByCondition([
                ['module_id', '=', $option['id']],
                ['controllers' , '=', 'App\Http\Controllers\Frontend\\'. $controllerName .''],
                ['language_id' , '=', $option['languageId']]
            ]);

            $router = [
                'canonical' => Str::slug($payload['canonical']),
                'module_id' => $option['id'],
                'language_id' => $option['languageId'],
                'controllers' => 'App\Http\Controllers\Frontend\\'. $controllerName .''
            ];

            $this->routerRepository->create($router);

            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    private function convertModelToField($model) {
        $temp = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $model));
        return $temp . '_id';
    }

    private function paginateSelect() {
        return ['id', 'name', 'image', 'canonical', 'publish', 'description'];
    }
}
