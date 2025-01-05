<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\SystemRepository;
use App\Repositories\ProductCatalogueRepository;

use App\Services\ProductCatalogueService;
use App\Services\ProductService;
use App\Services\PromotionService;

class ProductCatalogueController extends FrontendController
{   
    protected $productCatalogueRepository;
    protected $productCatalogueService;
    protected $productService;
    protected $promotionService;

    public function __construct(    
        ProductCatalogueRepository $productCatalogueRepository,
        ProductCatalogueService $productCatalogueService,
        ProductService $productService,
        PromotionService $promotionService,
        SystemRepository $systemRepository,
    ) {
        parent::__construct($systemRepository);
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->productCatalogueService = $productCatalogueService;
        $this->productService = $productService;
        $this->promotionService = $promotionService;
    }

    public function index($request, $page, $id) {
        $config = $this->config();
    
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($id, $this->language);
    
        if (!$productCatalogue) {
            abort(404, 'Product catalogue not found');
        }
    
        $products = $this->productService->paginate(
            $request, 
            $this->language, 
            $productCatalogue,
            ['path' => $productCatalogue->canonical],
            $page,
        );
    
        if ($products->isEmpty()) {
            abort(404, 'No products found');
        }
    
        $productIds = $products->pluck('id')->toArray();
        $bestPromotions = $this->promotionService->getBestPromotion("product", $productIds);
    
        foreach ($products->items() as $product) {
            if ($promotion = $bestPromotions->firstWhere('product_id', $product->id)) {
                $product->promotion = $promotion;
            }
        }
    
        $seo = seo($productCatalogue, $page);
        $breadcrumb = $this->productCatalogueService->breadcrumb("ProductCatalogue", $productCatalogue, $this->language);
    
        return view('frontend.product.catalogue.index', compact(
            'config',
            'seo',
            'productCatalogue',
            'breadcrumb',
            'products'
        ));
    }

    private function config() {
        return [
            'language' => $this->language
        ];
    }
}