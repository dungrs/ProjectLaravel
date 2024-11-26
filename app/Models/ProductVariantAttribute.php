<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\QueryScopes;

class ProductVariantAttribute extends Model
{
    use HasFactory, QueryScopes;

    protected $table = 'product_variant_attribute';

    protected $fillable = [
        'product_variant_id',
        'attribute_id'
    ];
}
