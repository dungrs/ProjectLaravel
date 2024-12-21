<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Customer;
use App\Traits\QueryScopes;

class CustomerCatalogue extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, QueryScopes;

    protected $fillable = [
        'name',
        'description',
        'publish'
    ];

    protected $table = 'customer_catalogues';

    public function customers() {
        return $this->hasMany(customer::class, 'customer_catalogue_id', 'id');
    }
}
