<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;

use App\Services\ReviewService;

use Illuminate\Http\Request;

class ReviewController extends Controller
{   
    protected $reviewService;

    public function __construct(
        ReviewService $reviewService    
    ) {
        $this->reviewService = $reviewService;
    }

    public function create(Request $request) {
        $response = $this->reviewService->create($request);
        return response()->json($response);
    }
}
