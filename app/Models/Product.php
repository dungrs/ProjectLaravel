<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\QueryScopes;

class Product extends Model
{
    use HasApiTokens, HasFactory, Notifiable, QueryScopes, SoftDeletes;

    protected $fillable = [
        'image',
        'album',
        'publish',
        'order',
        'user_id',
        'follow',
        'product_catalogue_id',
        'attributeCatalogue',
        'attribute',
        'variant'
    ];

    protected $table = 'products';

    public function languages() {
        // Muốn xử lý trên các trường nào thì sử dụng pivot
        return $this->belongsToMany(Language::class, 'product_language', 'product_id', 'language_id')->withPivot('name', 'canonical', 'meta_title', 'meta_keyword', 'meta_description', 'description')->withTimestamps();
    }

    public function product_catalogues() {
        return $this->belongsToMany(ProductCatalogue::class, 'product_catalogue_product', 'product_id', 'product_catalogue_id');
    }

    public function product_variants() {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id');
    }

    public function promotions() {
        // Muốn xử lý trên các trường nào thì sử dụng pivot
        return $this->belongsToMany(Promotion::class, 'promotion_product_variant', 'product_id', 'promotion_id')
        ->withPivot('variant_uuid', 'model')->withTimestamps();
    }
}