<?php

namespace App\Services;
use App\Services\Interfaces\ProductVariantServiceInterface;

use App\Services\BaseService;

use App\Repositories\ProductRepository;
use App\Repositories\ProductVariantRepository;

/**
 * Class ProductVariantService
 * @package App\Services
 */
class ProductVariantService extends BaseService implements ProductVariantServiceInterface
{   
    protected $productRepository;
    protected $productVariantRepository;
    public function __construct(ProductRepository $productRepository, ProductVariantRepository $productVariantRepository) {
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
    }

    public function getProductVariant($payload, $languageId, $attributeString) {
        $variants = $this->productVariantRepository->findByCondition(
            [
                ['tb2.language_id', '=', $languageId],
                ['product_variants.product_id', '=', $payload['product_id']]
            ],
            true,
            [
                [
                    'table' => 'product_variant_language as tb2', // Bảng liên kết với alias
                    'on' => ['tb2.product_variant_id', 'product_variants.id'] // Điều kiện join
                ]
            ],
            ['product_variants.id' => 'ASC'], // Sắp xếp theo id
            [
                'product_variants.id',          // Lấy các cột từ bảng chính
                'product_variants.code',
                'product_variants.price',
                'product_variants.uuid',
                'tb2.*',                        // Lấy tất cả các cột từ bảng liên kết
            ]
        );
    
        foreach ($variants as $variant) {
            $dbAttributeId = explode(',', $variant->code);
            $dbAttributeString = sortAttributeId($dbAttributeId); 
    
            if ($dbAttributeString == $attributeString) {
                return $variant;
            }
        }
    }
}
