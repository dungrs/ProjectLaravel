<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\QueryScopes;


class ProductCatalogue extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, QueryScopes;

    protected $fillable = [
        'parent_id',
        'lft',
        'rgt',
        'level',
        'image',
        'icon',
        'album',
        'publish',
        'order',
        'user_id',
        'follow',
        'attribute'
    ];

    protected $casts = [
        'attribute' => 'json'
    ];

    protected $table = 'product_catalogues';

    public function languages() {
        // Muốn xử lý trên các trường nào thì sử dụng pivot
        return $this->belongsToMany(Language::class, 'product_catalogue_language', 'product_catalogue_id', 'language_id')
        ->withPivot('name', 
            'canonical', 
            'meta_title', 
            'meta_keyword', 
            'meta_description', 
            'description'
        )->withTimestamps();
    }

    public function products() {
        return $this->belongsToMany(Product::class, 'product_catalogue_product', 'product_catalogue_id', 'product_id');
    }

    public function product_catalogue_language() {
        return $this->hasMany(ProductCatalogueLanguage::class, 'product_catalogue_id', 'id');
    }

    public static function isNodeCheck($id = 0) {
        $productCatalogue = ProductCatalogue::find($id);
        if ($productCatalogue->rgt - $productCatalogue->lft !== 1) {
            return false;
        }

        return true;
    }
}
