<?php

namespace App\Services;

use App\Services\Interfaces\ProductCatalogueServiceInterface;
use App\Services\BaseService;

use App\Repositories\ProductCatalogueRepository;
use App\Repositories\ProductRepository;
use App\Repositories\AttributeCatalogueRepository;
use App\Repositories\AttributeRepository;
use App\Repositories\RouterRepository;
use App\Repositories\ProductVariantRepository;

use App\Classes\Nestedsetbie;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class ProductCatalogueService
 * @package App\Services
 */
class ProductCatalogueService extends BaseService implements ProductCatalogueServiceInterface
{   
    protected $productCatalogueRepository;
    protected $productVariantRepository;
    protected $attributeCatalogueRepository;
    protected $attributeRepository;
    protected $productRepository;
    protected $routerRepository;
    protected $nestedSet;
    protected $language;
    protected $controllerName = 'ProductCatalogueController';

    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
        ProductVariantRepository $productVariantRepository,
        AttributeCatalogueRepository $attributeCatalogueRepository,
        AttributeRepository $attributeRepository,
        RouterRepository $routerRepository,
        ProductRepository $productRepository
        ) {
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->attributeCatalogueRepository = $attributeCatalogueRepository;
        $this->attributeRepository = $attributeRepository;
        $this->routerRepository = $routerRepository;
        $this->productRepository = $productRepository;
    }

    public function paginate($request, $languageId) {
        $perpage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request -> input('keyword')),
            'publish' => $request->integer('publish'),
            'where' => [
                ['tb2.language_id', '=', $languageId]
            ]
        ];
        $productCatalogues = $this->productCatalogueRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perpage,
            ['path' => 'product/catalogue/index'],
            [
                'product_catalogues.lft', 'ASC'
            ],
            [
                ['product_catalogue_language as tb2', 'tb2.product_catalogue_id', '=', 'product_catalogues.id']
            ], 
            [], 
        );
        return $productCatalogues;
    }

    public function create($request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
        try {
            // Lấy tất cả dữ liệu từ request
            $productCatalogue = $this->createProductCatalogue($request);
            if ($productCatalogue->id > 0) {
                $this->updateLanguageForProductCatalogue($request, $productCatalogue, $languageId);
                $this->createRouter($request, $productCatalogue, $this->controllerName, $languageId);
                $this->initialize($languageId);
                $this->nestedSet();
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

    public function update($id, $request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {   
            $productCatalogue = $this->productCatalogueRepository->findById($id);
            $flag = $this->updateProductCatalogue($request, $id);
            if ($flag == true) {
                $this->updateLanguageForProductCatalogue($request, $productCatalogue, $languageId);
                $this->updateRouter($request, $productCatalogue, $this->controllerName, $languageId);
                $this->initialize($languageId);
                $this->nestedSet();
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

    public function delete($id, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $this->productCatalogueRepository->delete($id);
            $this->routerRepository->deleteByCondition([
                ['module_id', '=', $id],
                ['controllers' , '=', 'App\Http\Controllers\Frontend\ProductCatalogueController']
            ]);
            $this->initialize($languageId);
            $this->nestedSet->Get();
            $this->nestedSet->Recursive(0, $this->nestedSet->Set());
            $this->nestedSet->Action();
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function setAttribute($product, $languageId) {
        $attribute = $product->attribute;
        $productCatalogue = $this->productCatalogueRepository->findById($product->product_catalogue_id);

        $catalogueIds = $this->productCatalogueRepository->recursveCategoryGetParentAChild($product->product_catalogue_id, 'product');
        
        $productList = [];
        foreach ($catalogueIds as $id) {
            $productCatalogueItem = $this->productCatalogueRepository->getProductCatalogueById($id, $languageId);
            $productIds = $productCatalogueItem->products->pluck('id');
            $productItems = $this->productRepository->findByCondition(
                [   
                    ['product_language.language_id', '=', $languageId],
                    ['products.id', 'IN', $productIds],
                    config('apps.general.defaultPublish')
                ],
                true,
                [
                    [
                        'table' => 'product_language',
                        'on' => ['product_language.product_id', 'products.id'] 
                    ],
                ],
                ['products.id' => 'ASC'], // Sắp xếp theo ID
                [
                    'product_language.*', // Lấy dữ liệu ngôn ngữ sản phẩm
                ]
            );
            $productList[$id] = $productItems;
        }

        if (!is_array($attribute)) {
            $decodedAttribute = json_decode($attribute, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $attribute = $decodedAttribute; // Gán giá trị sau khi decode
            } else {
                $attribute = []; // Nếu không decode được, đặt giá trị là mảng rỗng
            }
        }
        
        if (!is_array($productCatalogue->attribute)) {
            $payload['attribute'] = $attribute;
        } else {
            $mergeArray = $productCatalogue->attribute;
            foreach ($attribute as $key => $val) {
                if (!isset($mergeArray[$key])) {
                    $mergeArray[$key] = $val;
                } else {
                    $mergeArray[$key] = array_values(array_unique(array_merge($mergeArray[$key], $val)));
                }
            }
            $payload['attribute'] = $mergeArray;
        }
        

        $attributeList = [];
        foreach ($productList as $key => $val) {
            $attributesForProduct = []; // Khởi tạo mảng để lưu các thuộc tính cho sản phẩm
            foreach ($val as $product) {
                // Lấy danh sách các biến thể cho sản phẩm
                $variants = $this->productVariantRepository->findByCondition(
                    [
                        ['product_variant_language.language_id', '=', $languageId],
                        ['product_variants.product_id', '=', $product->product_id],
                    ],
                    true,
                    [
                        [
                            'table' => 'product_variant_language', // Bảng liên kết
                            'on' => ['product_variant_language.product_variant_id', 'product_variants.id']
                        ]
                    ],
                    ['product_variants.id' => 'ASC'],
                    ['product_variant_language.*']
                );

                // Gộp tất cả các thuộc tính từ biến thể vào mảng $attributesForProduct
                foreach ($variants as $variant) {
                    $attributeId = loadClass('ProductVariantAttribute')->findByCondition(
                        [
                            ['product_variant_attribute.product_variant_id', '=', $variant->product_variant_id],
                        ],
                        true,
                        [
                        ],
                        [],
                        ['product_variant_attribute.attribute_id']
                    )->toArray();
                    $attributeId = array_map(function ($item) {
                        return $item['attribute_id'];
                    }, $attributeId);
                    $attributesForProduct = array_merge($attributesForProduct, $attributeId);
                }

            }
            $attributeList[$key] = array_unique($attributesForProduct);
        }

        foreach ($payload['attribute'] as $key => $val) {
            foreach($attributeList as $validKey => $validValues) {
                $payload['attribute'][$key] = array_filter($val, function ($value) use ($validValues) {
                    return in_array($value, $validValues);
                });
        
                $payload['attribute'][$key] = array_values($payload['attribute'][$key]);

                $this->productCatalogueRepository->update($validKey, $payload);
            }
        }

        return $this->productCatalogueRepository->findByCondition(
            [
                ['product_catalogues.id', '=', $product->product_catalogue_id],
            ],
            true,
            [
            ],
            [],
            ['*']
        );
    }

    public function getFilterList(array $attribute = [], $languageId) {
        $attributeCatalogueId = array_keys($attribute);
        $attributeId = array_unique(array_merge(...$attribute));
        $attributeCatalogues = $this->attributeCatalogueRepository->findByCondition(
            [   
                ['attribute_catalogue_language.language_id', '=', $languageId],
                ['attribute_catalogues.id', 'IN', $attributeCatalogueId],
                config('apps.general.defaultPublish')
            ],
            true,
            [
                [
                    'table' => 'attribute_catalogue_language', // Bảng liên kết
                    'on' => ['attribute_catalogue_language.attribute_catalogue_id', 'attribute_catalogues.id'] 
                ]
            ],
            
            ['attribute_catalogues.id' => 'ASC'],
            [
                'attribute_catalogue_language.*', 
            ]
        );

        $attributes = $this->attributeRepository->findByCondition(
            [   
                ['attribute_language.language_id', '=', $languageId],
                ['attributes.id', 'IN', $attributeId],
                config('apps.general.defaultPublish')
            ],
            true,
            [
                [
                    'table' => 'attribute_language', // Bảng liên kết
                    'on' => ['attribute_language.attribute_id', 'attributes.id'] 
                ]
            ],
            
            ['attributes.id' => 'ASC'],
            [
                'attributes.attribute_catalogue_id',
                'attribute_language.*', 
            ]
        );
        // Sắp xếp các attributes theo attribute_catalogue_id
        $attributesGrouped = [];
        foreach ($attributes as $attribute) {
            $attributesGrouped[$attribute->attribute_catalogue_id][] = $attribute;
        }

        // Gắn attributes vào attributeCatalogues
        foreach ($attributeCatalogues as $catalogue) {
            $catalogue->attributes = $attributesGrouped[$catalogue->attribute_catalogue_id] ?? []; // Gắn tất cả attributes tương ứng
        }

        return $attributeCatalogues;
    }

    private function initialize($languageId) {
        $this->nestedSet = new Nestedsetbie([
            'table' => 'product_catalogues',
            'foreignkey' => 'product_catalogue_id',
            'language_id' => $languageId,
        ]);
    }

    private function createProductCatalogue($request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();
        return $this->productCatalogueRepository->create($payload);
    }

    public function updateProductCatalogue($request, $id) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $flag = $this->productCatalogueRepository->update($id, $payload);
        return $flag;
    }

    private function updateLanguageForProductCatalogue($request, $productCatalogue, $languageId) {
        $payload = $this->formatLanguagePayload($request, $productCatalogue->id, $languageId);
        // Detach được sử dụng để xóa theo id trong các table many to many
        $productCatalogue->languages()->detach($payload['language_id'], $productCatalogue->id);
        return $this->productCatalogueRepository->createPivot($productCatalogue, $payload, 'languages');
    }

    private function formatLanguagePayload($request, $id, $languageId) {
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['product_catalogue_id'] = $id;
        return $payload;
    }

    private function paginateSelect() {
        return ['product_catalogues.id', 'product_catalogues.publish', 'product_catalogues.image', 'product_catalogues.level', 'product_catalogues.order', 'tb2.name', 'tb2.canonical'];
    }

    private function payload() {
        return ['parent_id', 'follow', 'publish', 'image', 'album'];
    }

    private function payloadLanguage() {
        return ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }
}
