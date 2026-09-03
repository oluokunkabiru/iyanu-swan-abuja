<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\JsonResponse;

class GalleryImageController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            GalleryImage::query()->orderBy('sort_order')->get()
        );
    }
}
