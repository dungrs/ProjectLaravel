<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PostCatalogue;
use App\Traits\QueryScopes;

class Language extends Model
{
    use  HasFactory, SoftDeletes, QueryScopes;

    protected $fillable = [
        'name',
        'canonical',
        'image',
        'description',
        'user_id',
        'publish',
        'current'
    ];

    protected $table = 'language';

    public function languages() {
        // Muốn xử lý trên các trường nào thì sử dụng pivot
        return $this->belongsToMany(PostCatalogue::class, 'post_catalogue_language', 'language_id', 'post_catalogue_id')->withPivot('name', 'canonical', 'meta_title', 'meta_keyword', 'meta_description', 'description')->withTimestamps();
    }

    // public function post_catalogues() {
    //     // Muốn xử lý trên các trường nào thì sử dụng pivot
    //     return $this->belongsToMany(PostCatalogue::class, 'post_catalogue_language', 'language_id', 'post_catalogue_id')->withPivot('name', 'canonical', 'meta_title', 'meta_keyword', 'meta_description', 'description')->withTimestamps();
    // }

    public function product_catalogue() {
        // Muốn xử lý trên các trường nào thì sử dụng pivot
        return $this->belongsToMany(ProductCatalogue::class, 'product_catalogue_language', 'language_id', 'product_catalogue_id')->withPivot('name', 'canonical', 'meta_title', 'meta_keyword', 'meta_description', 'description')->withTimestamps();
    }

    public function products() {
        // Muốn xử lý trên các trường nào thì sử dụng pivot
        return $this->belongsToMany(Product::class, 'product_language', 'language_id', 'product_id')->withPivot('name', 'canonical', 'meta_title', 'meta_keyword', 'meta_description', 'description')->withTimestamps();
    }

    public function attribute_catalogue() {
        // Muốn xử lý trên các trường nào thì sử dụng pivot
        return $this->belongsToMany(AttributeCatalogue::class, 'attribute_catalogue_language', 'language_id', 'attribute_catalogue_id')->withPivot('name', 'canonical', 'meta_title', 'meta_keyword', 'meta_description', 'description')->withTimestamps();
    }

    public function attributes() {
        // Muốn xử lý trên các trường nào thì sử dụng pivot
        return $this->belongsToMany(Attribute::class, 'attribute_language', 'language_id', 'attribute_id')->withPivot('name', 'canonical', 'meta_title', 'meta_keyword', 'meta_description', 'description')->withTimestamps();
    }

    public function product_variants() {
        // Muốn xử lý trên các trường nào thì sử dụng pivot
        return $this->belongsToMany(Product::class, 'product_variant_language', 'language_id', 'product_variant_id')->withPivot('name')->withTimestamps();
    }
}
