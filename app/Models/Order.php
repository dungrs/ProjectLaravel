<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class Order extends Model
{
    use HasFactory, QueryScopes, SoftDeletes;

    protected $fillable = [
        'code',
        'fullname',
        'phone',
        'email',
        'province_id',
        'district_id',
        'ward_id',
        'address',
        'description',
        'promotion',
        'cart',
        'customer_id',
        'guest_cookie',
        'method',
        'payment',
        'confirm',
        'delivery',
        'shipping'
    ];

    protected $table = 'orders';

    protected $casts = [
        'cart' => 'json',
        'promotion' => 'json'
    ];

    public function products() {
        return $this->belongsToMany(Product::class, 'order_product', 'order_id', 'product_id')
        ->withPivot('uuid', 'name', 'qty', 'price', 'price_original', 'option');
    }

    public function order_payments() {
        return $this->hasMany(OrderPayment::class, 'order_id', 'id');
    }
}
