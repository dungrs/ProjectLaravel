<?php

namespace App\Services;
use App\Services\Interfaces\ProductServiceInterface;
use App\Repositories\RouterRepository;
use App\Repositories\ProductVariantLanguageRepository;
use App\Services\BaseService;
use App\Repositories\ProductRepository;
use App\Repositories\AttributeCatalogueRepository;
use App\Repositories\AttributeRepository;
use App\Repositories\ProductVariantAttributeRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Pagination\Paginator;

/**
 * Class ProductService
 * @package App\Services
 */
class ProductService extends BaseService implements ProductServiceInterface
{   
    protected $productRepository;
    protected $routerRepository;
    protected $attributeRepository;
    protected $attributeCatalogueRepository;
    protected $productVariantLanguageRepository;
    protected $productVariantAttributeRepository;

    public function __construct(
        ProductRepository $productRepository, 
        RouterRepository $routerRepository,
        ProductVariantLanguageRepository $productVariantLanguageRepository,
        ProductVariantAttributeRepository $productVariantAttributeRepository,
        AttributeCatalogueRepository $attributeCatalogueRepository,
        AttributeRepository $attributeRepository,
    ) {
        $this->productRepository = $productRepository;
        $this->routerRepository = $routerRepository;
        $this->attributeCatalogueRepository = $attributeCatalogueRepository;
        $this->attributeRepository = $attributeRepository;
        $this->productVariantLanguageRepository = $productVariantLanguageRepository;
        $this->productVariantAttributeRepository = $productVariantAttributeRepository;
        $this->controllerName = 'ProductController';
    }

    public function paginate($request, $languageId, $productCatalogue = null, $extend = [], $page = 1) {
        if (!is_null($productCatalogue)) {
            Paginator::currentPageResolver(function() use ($page) {
                return $page;
            });
        }
    
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $condition['where'] = [
            ['tb2.language_id', '=', $languageId]
        ];
        $condition['product_catalogue_id'] = !is_null($productCatalogue) 
            ? $productCatalogue->id 
            : $request->integer('product_catalogue_id');
    
        // Thiết lập `path` dựa trên `productCatalogue`
        $basePath = $productCatalogue 
            ? $productCatalogue->canonical 
            : 'product/index';
            
        $perpage = 15; 
        if (is_null($productCatalogue)) {
            $perpage = $request->integer('perpage'); 
        }

        $products = $this->productRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perpage,
            [
                'path' => url($basePath), // Đảm bảo URL đúng
                'groupBy' => $this->paginateSelect()
            ],
            ['products.id', 'DESC'],
            [
                ['product_language as tb2', 'tb2.product_id', '=', 'products.id'],
                ['product_catalogue_product as tb3', 'products.id', '=', 'tb3.product_id']
            ], 
            ['product_catalogues'],
            $this->whereRaw($request, $languageId, $productCatalogue)
        );
    
        return $products;
    }

    public function create($request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
        try {
            $product = $this->createProduct($request);
            if ($product->id > 0) {
                $this->updateLanguageForProduct($product, $request, $languageId);
                $this->uploadCatalogueForProduct($product, $request);
                $this->createRouter($request, $product, $this->controllerName, $languageId);

                // Sẽ xóa tất cả bản ghi trong product_variants mà có product_id->id 
                $product->product_variants()->delete();
                $this->createVariant($product, $request, $languageId);
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
            $product = $this->productRepository->findById($id);
            if ($this->uploadProduct($id, $request)) {
                $this->updateLanguageForProduct($product, $request, $languageId);
                $this->uploadCatalogueForProduct($product, $request);
                $this->updateRouter($request, $product, $this->controllerName, $languageId);
            }

            // Sẽ xóa tất cả bản ghi trong product_variants mà có product_id->id 
            $product->product_variants()->each(function($variant) {
                $variant->languages()->detach();
                $variant->attributes()->detach();
                $variant->delete();
            });
            $this->createVariant($product, $request, $languageId);

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
            $this->productRepository->delete($id); // Soft Delete
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

    public function getAttribute($product, $languageId) {
        $attributeCatalogueId = array_keys(json_decode($product->attribute, true));
        $attrCatalogues = $this->attributeCatalogueRepository->getAttributeCatalogue($attributeCatalogueId, $languageId);

        $attributeId = array_merge(...json_decode($product->attribute, true));
        $attrs = $this->attributeRepository->findAttributeByIdArray($attributeId, $languageId);
        if (!is_null($attrCatalogues)) {
            foreach($attrCatalogues as $key => $val) {
                $tempAttributes = [];
                foreach($attrs as $attr) {
                    if ($val->attribute_catalogue_id  == $attr->attribute_catalogue_id) {
                        $tempAttributes[] = $attr;
                    }
                }
                $val->attributes = $tempAttributes;
            }
        }

        $product->attributeCatalogue = $attrCatalogues;
        return $product;
    }

    private function createVariant($product, $request, $languageId) {
        $payload = $request->only(['variant', 'productVariant', 'attribute']);
        $variant = $this->createVariantArray($payload, $product);
       
        $variants = $product->product_variants()->createMany($variant);
        // Lấy ra tất cả 
        $variantId = $variants->pluck('id');
        $productVariantLanguage = [];
        $productVariantAttribute = [];

        $attributes = $this->combineAttribute(array_values($payload['attribute']));
        if (count($variantId)) {
            foreach($variantId as $key => $val) {
                $productVariantLanguage[] = [
                    'product_variant_id' => $val,
                    'language_id' => $languageId,
                    'name' => $payload['productVariant']['name'][$key]
                ];

                if (count($attributes)) {
                    foreach($attributes[$key] as $attributeId) {
                        $productVariantAttribute[] = [
                            'product_variant_id' => $val,
                            'attribute_id' => $attributeId, 
                        ];
                    }
                }
            }
        }

        $variantAttribute = $this->productVariantAttributeRepository->createBatch($productVariantAttribute);
        $variantLanguage = $this->productVariantLanguageRepository->createBatch($productVariantLanguage);
    }

    private function combineAttribute($attributes = [], $index = 0) {
        if ($index === count($attributes)) return [[]];

        $subCombines = $this->combineAttribute($attributes, $index + 1);
        $combines = [];
        foreach ($attributes[$index] as $key => $val) {
            foreach($subCombines as $keySub => $valSub) {
                $combines[] = array_merge([$val], $valSub);
            }
        }

        return $combines;
    }

    private function createVariantArray(array $payload = [], $product): array {
        $variant = [];
        if (isset($payload['variant']['sku']) && count($payload['variant']['sku'])) {
            foreach($payload['variant']['sku'] as $key => $val) {
                $uuId = \Ramsey\Uuid\Guid\Guid::uuid5(\Ramsey\Uuid\Guid\Guid::NAMESPACE_DNS, $product->id . ', ' . $payload['productVariant']['id'][$key]);
                $vId =  ($payload['productVariant']['id'][$key]) ?? '';
                $productVariantId = sortString($vId);
                $variant[] = [
                    'uuid' => $uuId,
                    'code' => $productVariantId,
                    'quantity' => ($payload['variant']['quantity'][$key]) ?? '',
                    'sku' => $val,
                    'price' => ($payload['variant']['price'][$key]) ? str_replace(',', '', $payload['variant']['price'][$key])  : 0,
                    'barcode' => ($payload['variant']['barcode'][$key]) ?? '',
                    'file_name' => ($payload['variant']['file_name'][$key]) ?? '',
                    'file_url' => ($payload['variant']['file_url'][$key]) ?? '',
                    'album' => ($payload['variant']['album'][$key]) ?? '',
                    'user_id' => Auth::id(),
                ];

            }
        }
        return $variant;
    }

    private function createProduct($request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['attributeCatalogue'] = $this->formatJson($request, 'attributeCatalogue');
        $payload['attribute'] = $this->formatJson($request, 'attribute');
        $payload['variant'] = $this->formatJson($request, 'variant');
        $payload['user_id'] = Auth::id();
        return $this->productRepository->create($payload);
    }

    private function uploadProduct($id, $request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        return $this->productRepository->update($id, $payload);
    }

    private function updateLanguageForProduct($product, $request, $languageId) {
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, $product->id, $languageId
    );
        // Xóa các bản ghi có payload['language_id'] và $product->id trong bảng pivot
        $product->languages()->detach($payload['language_id'], $product->id);
        return $this->productRepository->createPivot($product, $payload, 'languages');
    }

    private function formatLanguagePayload($payload, $productId, $languageId) {
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['product_id'] = $productId;
        return $payload;
    }

    private function uploadCatalogueForProduct($product, $request) {
        // Đồng bộ hóa mối quan hệ giữa các dữ liệu
        // Xóa liên kết cũ, thêm liên kết mới và cập nhật liên kết
        $product->product_catalogues()->sync($this->catalogue($request));
    }
        
    private function catalogue($request) {
        // Lấy mảng danh mục từ input của request và kết hợp với danh mục product_catalogue_id
        if ($request->input('catalogue') != null) {
            // Trả về mảng kết quả với các giá trị duy nhất, loại bỏ các phần tử trùng lặp
            return array_unique(
                array_merge(
                    $request->input('catalogue'),  // Lấy các giá trị từ input 'catalogue'
                    [$request->product_catalogue_id]  // Thêm giá trị 'product_catalogue_id' vào mảng
                )
            );
        }

        return [$request->product_catalogue_id];
    }

    private function whereRaw($request, $languageId, $productCatalogue = null) {
        $rawCondition = [];
        if ($request->integer('product_catalogue_id') > 0 || !is_null($productCatalogue)) {
            $catId = ($request->integer('product_catalogue_id') > 0 ) ? $request->integer('product_catalogue_id') : $productCatalogue->id;
            $rawCondition['whereRaw'] =  [
                [
                    "tb3.product_catalogue_id IN (
                        SELECT product_catalogues.id
                        FROM product_catalogues
                        JOIN product_catalogue_language 
                            ON product_catalogues.id = product_catalogue_language.product_catalogue_id
                        WHERE product_catalogues.lft >= (
                                SELECT pc.lft 
                                FROM product_catalogues AS pc 
                                WHERE pc.id = ?
                            )
                        AND product_catalogues.rgt <= (
                                SELECT pc.rgt 
                                FROM product_catalogues AS pc 
                                WHERE pc.id = ?
                            )
                        AND product_catalogue_language.language_id = ?
                    )
                    ",
                    [$catId, $catId, $languageId]
                ]
            ];
        }

        return $rawCondition;
    }

    private function paginateSelect() {
        return ['products.id', 'products.publish', 'products.image', 'products.order', 'tb2.language_id', 'tb2.name', 'tb2.canonical'];
    }

    private function payload() {
        return ['follow', 'publish','made_in', 'image', 'album', 'product_catalogue_id', 'attributeCatalogue', 'attribute', 'variant'];
    }

    private function payloadLanguage() {
        return ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }
}
