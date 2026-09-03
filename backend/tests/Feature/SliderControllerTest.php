<?php

namespace Tests\Feature;

use App\Models\Slider;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SliderControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_returns_active_sliders_with_images_in_display_order(): void
    {
        Storage::fake('public');

        $second = Slider::factory()->create([
            'title' => 'Second slide',
            'sort_order' => 20,
        ]);
        $second->addMedia(UploadedFile::fake()->image('second.jpg', 1200, 800))
            ->toMediaCollection('image');

        $first = Slider::factory()->create([
            'badge' => 'Chapter news',
            'title' => 'First slide',
            'description' => 'The first active slide.',
            'cta_label' => 'Read more',
            'cta_link' => '/news',
            'image_alt' => 'Members at a chapter event',
            'image_position' => 'top',
            'sort_order' => 10,
        ]);
        $first->addMedia(UploadedFile::fake()->image('first.jpg', 1200, 800))
            ->toMediaCollection('image');

        $inactive = Slider::factory()->inactive()->create([
            'title' => 'Inactive slide',
            'sort_order' => 1,
        ]);
        $inactive->addMedia(UploadedFile::fake()->image('inactive.jpg', 1200, 800))
            ->toMediaCollection('image');

        Slider::factory()->create([
            'title' => 'Slide without an image',
            'sort_order' => 2,
        ]);

        $response = $this->getJson('/api/sliders');

        $response
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonPath('0.id', (string) $first->id)
            ->assertJsonPath('0.badge', 'Chapter news')
            ->assertJsonPath('0.title', 'First slide')
            ->assertJsonPath('0.description', 'The first active slide.')
            ->assertJsonPath('0.ctaLabel', 'Read more')
            ->assertJsonPath('0.ctaLink', '/news')
            ->assertJsonPath('0.imageAlt', 'Members at a chapter event')
            ->assertJsonPath('0.imagePosition', 'top')
            ->assertJsonPath('1.id', (string) $second->id)
            ->assertJsonMissing(['title' => 'Inactive slide'])
            ->assertJsonMissing(['title' => 'Slide without an image']);

        $this->assertIsString($response->json('0.image'));
        $this->assertStringContainsString('first.jpg', $response->json('0.image'));
    }
}
