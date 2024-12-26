<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use App\Repositories\ProductCatalogueRepository;
use App\Services\ProductCatalogueService;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;
use App\Models\Language;
use Illuminate\Http\Request;

class ProductController extends Controller
{   
    protected $productRepository;
    protected $productService;
    protected $productCatalogueService;
    protected $productCatalogueRepository;

    protected $language;
    protected $nestedSet;

    public function __construct(ProductRepository $productRepository, ProductCatalogueService $productCatalogueService, ProductService $productService, ProductCatalogueRepository $productCatalogueRepository) {
        $this->productRepository = $productRepository;
        $this->productCatalogueService = $productCatalogueService;
        $this->productService = $productService;
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->middleware(function($request, $next) {
            // Lấy ra ngôn ngữ hiện tại     
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            // Sau khi xử lý xong nó sẽ truyền $request tới cấc middlewere khác để xử lý nốt phần còn lại
            // Rồi mới đến phần Controller
            return $next($request);
        });
    }

    public function loadProductAnimation(Request $request) {
        $get = $request->input();
        $model = $get['model'];
        if ($model == 'Product') {
            $objects = $this->productRepository->findByCondition(
                [
                    ['tb2.language_id', '=', $this->language],
                    ['tb2.name', 'LIKE', '%' . $get['keyword'] . '%'],
                ],
                true,
                [
                    [
                        'table' => 'product_language as tb2', // Bảng liên kết
                        'on' => ['products.id', 'tb2.product_id'] // Điều kiện join
                    ],
                    [   
                        'left',
                        'table' => 'product_variants as tb3', // Bảng liên kết
                        'on' => ['products.id', 'tb3.product_id'] // Điều kiện join
                    ],
                    [   
                        'left',
                        'table' => 'product_variant_language as tb4', // Bảng liên kết
                        'on' => ['tb3.id', 'tb4.product_variant_id'] // Điều kiện join
                    ],
                ],
                
                ['id' => 'DESC'],
                [
                    'products.id', 
                    'products.image',
                    'tb3.price',
                    'tb3.uuid as variant_uuid',
                    'tb3.quantity',
                    'tb3.sku',
                    'tb2.name',
                    'tb3.id as product_variant_id', 
                    DB::raw('CONCAT(tb2.name, " - ", COALESCE(tb4.name, " Default")) as variant_name'),
                ],
                10
            );
        } else {
            $objects = $this->productCatalogueRepository->findByCondition(
                [
                    ['tb2.language_id', '=', $this->language],
                    ['tb2.name', 'LIKE', '%' . $get['keyword'] . '%'],
                ],
                true,
                [
                    [
                        'table' => 'product_catalogue_language as tb2', // Bảng liên kết
                        'on' => ['product_catalogues.id', 'tb2.product_catalogue_id'] // Điều kiện join
                    ],
                ],
                
                ['id' => 'DESC'],
                [
                    'product_catalogues.id', 
                    'tb2.name',
                ],
                10
            );
        }

        return response()->json([
            'model' => $get['model'],
            'objects' => $objects,
        ]);
    }
}
