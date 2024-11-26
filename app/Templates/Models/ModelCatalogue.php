<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\QueryScopes;


class {Module}Catalogue extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, QueryScopes;

    protected $fillable = [
        'parent_id',
        'lft',
        'rgt',
        'level',
        'image',
        'icon',
        'album',
        'publish',
        'order',
        'user_id',
        'follow'
    ];

    protected $table = '{module}_catalogues';

    public function languages() {
        // Muốn xử lý trên các trường nào thì sử dụng pivot
        return $this->belongsToMany(Language::class, '{module}_catalogue_language', '{module}_catalogue_id', 'language_id')
        ->withPivot('name', 
            'canonical', 
            'meta_title', 
            'meta_keyword', 
            'meta_description', 
            'description'
        )->withTimestamps();
    }

    public function {module}s() {
        return $this->belongsToMany({Module}::class, '{module}_catalogue_{module}', '{module}_catalogue_id', '{module}_id');
    }

    public function {module}_catalogue_language() {
        return $this->hasMany({Module}CatalogueLanguage::class, '{module}_catalogue_id', 'id');
    }

    public static function isNodeCheck($id = 0) {
        ${module}Catalogue = {Module}Catalogue::find($id);
        if (${module}Catalogue->rgt - ${module}Catalogue->lft !== 1) {
            return false;
        }

        return true;
    }
}
