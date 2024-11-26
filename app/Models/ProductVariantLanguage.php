<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\QueryScopes;

class ProductVariantLanguage extends Model
{
    use HasFactory, QueryScopes;

    protected $table = 'product_variant_language';

    protected $fillable = [
        'product_varinat_id',
        'language_id',
        'name_id'
    ];

    public $timestamps = true;
}
