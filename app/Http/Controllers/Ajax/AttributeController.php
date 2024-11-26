<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\AttributeRepository;
use App\Models\Language;
use App\Classes\Nestedsetbie;

class AttributeController extends Controller
{   
    protected $attributeRepository;
    protected $language;
    protected $nestedSet;

    public function __construct(AttributeRepository $attributeRepository) {
        $this->attributeRepository = $attributeRepository;
        $this->middleware(function($request, $next) {
            // Lấy ra ngôn ngữ hiện tại     
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this ->initialize();
            // Sau khi xử lý xong nó sẽ truyền $request tới cấc middlewere khác để xử lý nốt phần còn lại
            // Rồi mới đến phần Controller
            return $next($request);
        });
    }

    public function getAttribute(Request $request) {
        $payload = $request->input();
        $attributes = $this->attributeRepository->searchAttributes($payload['search'], $payload['option'], $this->language);
        $attributeMapped = $attributes->map(function($attribute) {
            return [
                'id' => $attribute->id,
                'text' => $attribute->name,
            ];
        })->all();

        return response()->json(array('items' => $attributeMapped));
    }

    public function loadAttribute(Request $request) {
        $payload = [
            'attribute' => json_decode(base64_decode($request->input('attribute')), TRUE),
            'attributeCatalogueId' => $request->input('attributeCatalogueId'),
        ];
        
        $attributeList = $payload['attribute'][$payload['attributeCatalogueId']];
        $attributes = [];
        if (count($attributeList)) {
            $attributes = $this->attributeRepository->findAttributeByIdArray($attributeList, $this->language);
        }
 
        $temp = [];
        if (count($attributes)) {
            foreach ($attributes as $key => $val) {
                $temp[] = [
                    'id' => $val->id,
                    'text' => $val->name
                ];
            }
        }

        return response()->json(array('items' => $temp));
    }

    private function initialize() {
        $this->nestedSet = new Nestedsetbie([
            'table' => 'attribute_catalogues',
            'foreignkey' => 'attribute_catalogue_id',
            'language_id' => $this->language,
        ]);
    }

}
