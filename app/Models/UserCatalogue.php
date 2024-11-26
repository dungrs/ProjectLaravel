<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\User;
use App\Models\Permission;
use App\Traits\QueryScopes;

class UserCatalogue extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, QueryScopes;

    protected $fillable = [
        'name',
        'description',
        'publish'
    ];

    protected $table = 'user_catalogues';

    public function users() {
        return $this->hasMany(User::class, 'user_catalogue_id', 'id');
    }

    public function permission() {
        return $this->belongsToMany(Permission::class, 'user_catalogue_permission', 'user_catalogue_id',  'permission_id');
    }

}
