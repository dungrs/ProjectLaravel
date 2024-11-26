<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\QueryScopes;
use App\Models\UserCatalogue;

class Permission extends Model
{
    use  HasFactory, QueryScopes;

    protected $fillable = [
        'name',
        'canonical',
    ];

    protected $table = 'permission';

    public function user_catalogues() {
        return $this->belongsToMany(UserCatalogue::class, 'user_catalogue_permission', 'user_catalogue_id', 'permission_id');
    }
}
