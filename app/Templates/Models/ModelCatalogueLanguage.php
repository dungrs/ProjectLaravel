<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {Module}CatalogueLanguage extends Model
{   
    use HasFactory;

    protected $table = '{module}_catalogue_language';

    public function {module}_catalogues() {
        return $this->belongsTo({Module}Catalogue::class, '{module}_catalogue_id', 'id');
    }
}
