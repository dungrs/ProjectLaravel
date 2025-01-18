<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\QueryScopes;

class Review extends Model
{
    use HasApiTokens, HasFactory, Notifiable, QueryScopes;

    protected $fillable = [
        'reviewable_type',
        'parent_id',
        'lft',
        'rgt',
        'level',
        'reviewable_id',
        'email',
        'gender',
        'fullname',
        'phone',
        'description',
        'score'
    ];

    protected $table = 'reviews';

    public function reviewable() {
        return $this->morphTo();
    }


    // protected $casts = [
    //     'variant' => 'json',
    //     'attribute' => 'json'
    // ];
}