<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Repositories\SourceRepository;
use App\Services\SourceService;
use Illuminate\Support\Facades\DB;
use App\Models\Language;
use Illuminate\Http\Request;

class SourceController extends Controller
{   
    protected $sourceRepository;
    protected $sourceService;
    protected $language;

    public function __construct(
        SourceRepository $sourceRepository, 
        SourceService $sourceService
        ) {
        $this->sourceRepository = $sourceRepository;
        $this->sourceService = $sourceService;
        $this->middleware(function($request, $next) {
            // Lấy ra ngôn ngữ hiện tại     
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            // Sau khi xử lý xong nó sẽ truyền $request tới cấc middlewere khác để xử lý nốt phần còn lại
            // Rồi mới đến phần Controller
            return $next($request);
        });
    }

    public function getAllSource() {
        try {
            $sources = $this->sourceRepository->all();
            return response()->json([
                'sources' => $sources,
                'error' => false,
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'messages' => $e->getMessage(),
                'error' => true,
            ]);

        }   

    }
}
