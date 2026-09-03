<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use Illuminate\Http\JsonResponse;

class PublicationController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Publication::query()->orderBy('published_at', 'desc')->get()
        );
    }
}
