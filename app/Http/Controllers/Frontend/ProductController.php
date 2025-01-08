<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;

use App\Repositories\SystemRepository;
use App\Repositories\RouterRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductCatalogueRepository;

use App\Services\ProductCatalogueService;
use App\Services\PromotionService;
use App\Services\ProductService;

use Illuminate\Support\Facades\DB;

class ProductController extends FrontendController
{   
    protected $routerRepository;
    protected $productCatalogueRepository;
    protected $productRepository;
    protected $productService;
    protected $productCatalogueService;
    protected $promotionService;

    public function __construct(    
        RouterRepository $routerRepository,
        SystemRepository $systemRepository,
        ProductRepository $productRepository,
        ProductService $productService,
        ProductCatalogueRepository $productCatalogueRepository,
        ProductCatalogueService $productCatalogueService,
        PromotionService $promotionService,
    ) {
        parent::__construct($systemRepository);
        $this->routerRepository = $routerRepository;
        $this->productRepository = $productRepository;
        $this->productService = $productService;
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->productCatalogueService = $productCatalogueService;
        $this->promotionService = $promotionService;
    }

    public function index($request, $canonical, $id) {
        $config = $this->config();
        $languageId = $this->language;

        $product = $this->productRepository->getProductById($id, $languageId);
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($product->product_catalogue_id, $languageId);
        $product->promotions = $this->promotionService->getPromotionForProduct('product', $id);

        $breadcrumb = $this->productCatalogueService->breadcrumb("ProductCatalogue", $productCatalogue, $languageId);
        $product->attributes = $this->productService->getAttribute($product, $languageId);

        $objectCategory = recursive($this->productCatalogueRepository->findByCondition(
            [
                ['tb3.language_id', '=', $languageId],
            ],
            true,
            [
                [
                    'table' => 'product_catalogue_language as tb3', // Bảng liên kết
                    'on' => ['tb3.product_catalogue_id', 'product_catalogues.id'] // Điều kiện join
                ]
            ],
            [],
            [
                'product_catalogues.*', 
                'tb3.name', 'tb3.canonical',
                DB::raw("
                    (
                        SELECT COUNT(DISTINCT items.id)
                        FROM products AS items
                        JOIN product_catalogue_product AS pivot 
                          ON pivot.product_id = items.id
                        WHERE pivot.product_catalogue_id IN (
                            SELECT sub_catalogue.id
                            FROM product_catalogues AS sub_catalogue
                            WHERE sub_catalogue.lft >= product_catalogues.lft
                              AND sub_catalogue.rgt <= product_catalogues.rgt
                        )
                    ) AS product_count
                ")
            ]
        ));
   
        $seo = seo($product);

        return view('frontend.product.product.index', compact(
            'config',
            'seo',
            'productCatalogue',
            'breadcrumb',
            'product',
            'objectCategory',
            'languageId'
        ));
    }

    public function config() {
        return [
            'language' => $this->language,
            'js' => [
                'frontend/core/library/cart.js'
            ]
        ];
    }
}
