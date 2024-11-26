<?php

namespace App\Services;
use App\Services\Interfaces\PostServiceInterface;
use App\Repositories\RouterRepository;
use App\Services\BaseService;
use App\Repositories\PostRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class PostService
 * @package App\Services
 */
class PostService extends BaseService implements PostServiceInterface
{   
    protected $postRepository;
    protected $routerRepository;

    public function __construct(PostRepository $postRepository, RouterRepository $routerRepository) {
        $this->postRepository = $postRepository;
        $this->routerRepository = $routerRepository;
        $this->controllerName = 'PostController';
    }

    public function paginate($request, $languageId) {
        $condition['keyword'] = addslashes($request -> input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $condition['where'] = [
            ['tb2.language_id', '=', $languageId]
        ];
        $condition['post_catalogue_id'] = $request->integer('post_catalogue_id');
        $perpage = $request->integer('perpage');
        $posts = $this->postRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perpage,
            [
                'path' => 'post/index', 
                'groupBy' => $this->paginateSelect()
            ],
            ['posts.id', 'DESC'],
            [
                ['post_language as tb2', 'tb2.post_id', '=', 'posts.id'],
                ['post_catalogue_post as tb3', 'posts.id', '=', 'tb3.post_id']
            ], 
            ['post_catalogues'],
            $this->whereRaw($request, $languageId)
        );
        return $posts;
    }

    public function create($request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
        try {
            $post = $this->createPost($request);
            if ($post->id > 0) {
                $this->updateLanguageForPost($post, $request, $languageId);
                $this->uploadCatalogueForPost($post, $request);
                $this->createRouter($request, $post, $this->controllerName, $languageId);
            }
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function update($id, $request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            // Lấy tất cả dữ liệu từ request
            $post = $this->postRepository->findById($id);
            if ($this->uploadPost($id, $request)) {
                $this->updateLanguageForPost($post, $request, $languageId);
                $this->uploadCatalogueForPost($post, $request);
                $this->updateRouter($request, $post, $this->controllerName, $languageId);
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

    public function delete($id) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $this->postRepository->delete($id); // Soft Delete
            $this->routerRepository->deleteByCondition([
                ['module_id', '=', $id],
                ['controllers' , '=', 'App\Http\Controllers\Frontend\PostController']
            ]);
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }
    private function createPost($request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();
        return $this->postRepository->create($payload);
    }

    private function uploadPost($id, $request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        return $this->postRepository->update($id, $payload);
    }

    private function updateLanguageForPost($post, $request, $languageId) {
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, $post->id, $languageId
    );
        // Xóa các bản ghi có payload['language_id'] và $post->id trong bảng pivot
        $post->languages()->detach($payload['language_id'], $post->id);
        return $this->postRepository->createPivot($post, $payload, 'languages');
    }

    private function formatLanguagePayload($payload, $postId, $languageId) {
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['post_id'] = $postId;
        return $payload;
    }

    private function uploadCatalogueForPost($post, $request) {
        // Đồng bộ hóa mối quan hệ giữa các dữ liệu
        // Xóa liên kết cũ, thêm liên kết mới và cập nhật liên kết
        $post->post_catalogues()->sync($this->catalogue($request));
    }
        
    private function catalogue($request) {
        // Lấy mảng danh mục từ input của request và kết hợp với danh mục post_catalogue_id
        if ($request->input('catalogue') != null) {
            // Trả về mảng kết quả với các giá trị duy nhất, loại bỏ các phần tử trùng lặp
            return array_unique(
                array_merge(
                    $request->input('catalogue'),  // Lấy các giá trị từ input 'catalogue'
                    [$request->post_catalogue_id]  // Thêm giá trị 'post_catalogue_id' vào mảng
                )
            );
        }

        return [$request->post_catalogue_id];
    }

    private function whereRaw($request, $languageId) {
        $rawCondition = [];
        if ($request->integer('post_catalogue_id') > 0) {
            $rawCondition['whereRaw'] =  [
                [
                    "tb3.post_catalogue_id IN (
                        SELECT post_catalogues.id
                        FROM post_catalogues
                        JOIN post_catalogue_language 
                            ON post_catalogues.id = post_catalogue_language.post_catalogue_id
                        WHERE post_catalogues.lft >= (
                                SELECT pc.lft 
                                FROM post_catalogues AS pc 
                                WHERE pc.id = ?
                            )
                        AND post_catalogues.rgt <= (
                                SELECT pc.rgt 
                                FROM post_catalogues AS pc 
                                WHERE pc.id = ?
                            )
                        AND post_catalogue_language.language_id = ?
                    )
                    ",
                    [$request->integer('post_catalogue_id'), $request->integer('post_catalogue_id'), $languageId]
                ]
            ];
        }

        return $rawCondition;
    }

    private function paginateSelect() {
        return ['posts.id', 'posts.publish', 'posts.image', 'posts.order', 'tb2.language_id', 'tb2.name', 'tb2.canonical'];
    }

    private function payload() {
        return ['follow', 'publish', 'image', 'album', 'post_catalogue_id'];
    }

    private function payloadLanguage() {
        return ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }
}
