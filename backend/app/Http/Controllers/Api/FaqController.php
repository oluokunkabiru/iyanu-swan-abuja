<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;

class FaqController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Faq::query()
                ->where('is_active', true)
                ->orderBy('created_at')
                ->orderBy('id')
                ->get()
                ->map(fn (Faq $faq): array => [
                    'id' => (string) $faq->id,
                    'topic' => $faq->topic,
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                ])
        );
    }
}
