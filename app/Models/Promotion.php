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
    ];

    protected $table = 'promotion';

    protected $casts = [
        
    ];
}
