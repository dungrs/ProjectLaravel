<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Traits\QueryScopes;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory, Notifiable, QueryScopes, SoftDeletes;
    
    protected $fillable = [
        'id',
        'name',
        'type',
        'code',
        'description',
        'method',
        'module_type',
        'discount_information',
        'apply_source',
        'never_end_date',
        'start_date',
        'end_date',
        'publish',
        'order',
    ];

    protected $table = 'promotions';

    protected $casts = [
        'discount_information' => 'json',
    ];

    public function products() {
        // Muốn xử lý trên các trường nào thì sử dụng pivot
        return $this->belongsToMany(Product::class, 'promotion_product_variant', 'promotion_id', 'product_id')
        ->withPivot('variant_uuid', 'model')->withTimestamps();
    }
}
