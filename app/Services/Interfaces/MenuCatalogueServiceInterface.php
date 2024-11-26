<?php

namespace App\Services\Interfaces;

/**
 * Interface MenuCatalogueServiceInterface
 * @package App\Services\Interfaces
 */
interface MenuCatalogueServiceInterface
{
    public function paginate($request, $languageId);

}
