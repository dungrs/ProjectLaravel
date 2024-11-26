<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Language;
use App\Traits\QueryScopes;

class Menu extends Model
{
    use HasFactory, QueryScopes, SoftDeletes;

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
        'menu_catalogue_id'
    ];

    protected $table = 'menus';

    public function languages() {
        // Muốn xử lý trên các trường nào thì sử dụng pivot
        return $this->belongsToMany(Language::class, 'menu_language', 'menu_id', 'language_id')->withPivot('name', 'canonical')->withTimestamps();
    }

    public function menu_catalogues() {
        return $this->belongsTo(MenuCatalogue::class, 'menu_catalogue_id');
    }
}
