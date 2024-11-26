<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserCatalogue;
use App\Models\Permission;

class UserCataloguePermission extends Model
{
    use HasFactory;

    protected $table = 'user_catalogue_permission';

    public function user_catalogue() {
        return $this->belongsTo(UserCatalogue::class, 'user_catalogue_id');
    }

    public function permission() {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}
