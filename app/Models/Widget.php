<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Traits\QueryScopes;
use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    use HasFactory, Notifiable, QueryScopes, SoftDeletes;
    
    protected $fillable = [
        'name',
        'keyword',
        'description',
        'album',
        'model_id',
        'model',
        'short_code',
        'publish',
    ];

    protected $table = 'widgets';

    protected $casts = [
        'model_id' => 'json',
        'album' => 'json',
        'description' => 'json',
    ];
}
