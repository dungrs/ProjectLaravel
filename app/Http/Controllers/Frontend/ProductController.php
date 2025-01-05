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

        $product = $this->productRepository->getProductById($id, $this->language);
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($product->product_catalogue_id, $this->language);
        $product->promotions = $this->promotionService->getPromotionForProduct('product', $id);

        $breadcrumb = $this->productCatalogueService->breadcrumb("ProductCatalogue", $productCatalogue, $this->language);
        $product->attributes = $this->productService->getAttribute($product, $this->language);
        $seo = seo($product);

        return view('frontend.product.product.index', compact(
            'config',
            'seo',
            'productCatalogue',
            'breadcrumb',
            'product'
        ));
    }

    public function config() {
        
    }
}
