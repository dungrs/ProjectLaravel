<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PostCatalogue;

class PostCatalogueLanguage extends Model
{   
    use HasFactory;

    protected $table = 'post_catalogue_language';

    public function post_catalogues() {
        return $this->belongsTo(PostCatalogue::class, 'post_catalogue_id', 'id');
    }
}
