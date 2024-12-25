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
        'name',
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
        
    ];
}
