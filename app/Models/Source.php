<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Traits\QueryScopes;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use HasFactory, Notifiable, QueryScopes, SoftDeletes;
    
    protected $fillable = [
        'id',
        'name',
        'keyword',
        'description',
        'publish'
    ];

    protected $table = 'sources';

    // Note
    public function customers() {
        return $this->hasMany(Customer::class, 'source_id', 'id');
    }
}
