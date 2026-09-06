<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class SliderController extends Controller
{
    public function index(): JsonResponse
    {
        $sliders = Slider::query()
            ->with('media')
            ->where('is_active', true)
            ->whereHas('media', fn (Builder $query): Builder => $query->where('collection_name', 'image'))
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->map(fn (Slider $slider): array => [
                'id' => (string) $slider->id,
                'badge' => $slider->badge ?? '',
                'title' => $slider->title,
                'description' => $slider->description ?? '',
                'ctaLabel' => $slider->cta_label ?? '',
                'ctaLink' => $slider->cta_link ?? '',
                'secondaryCtaLabel' => $slider->secondary_cta_label,
                'secondaryCtaLink' => $slider->secondary_cta_link,
                'image' => $slider->image_url,
                'imageAlt' => $slider->image_alt ?? '',
                'imagePosition' => $slider->image_position,
            ]);

        return response()->json($sliders);
    }
}
