<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var array<string, array{network: string, label: string}> */
    private array $legacySocialColumns = [
        'facebook_url' => ['network' => 'facebook', 'label' => 'Facebook'],
        'instagram_url' => ['network' => 'instagram', 'label' => 'Instagram'],
        'twitter_url' => ['network' => 'twitter', 'label' => 'X'],
        'linkedin_url' => ['network' => 'linkedin', 'label' => 'LinkedIn'],
    ];

    /**
     * The single phone field becomes a list so the chapter can publish
     * as many numbers as it needs, and the four fixed social URL
     * columns (never read by the API — the site only uses the
     * social_links list) are folded into that list and dropped.
     */
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->json('phones')->nullable()->after('address');
        });

        foreach (DB::table('site_settings')->get() as $settings) {
            $socialLinks = json_decode($settings->social_links ?? '[]', true) ?: [];
            $networks = array_column($socialLinks, 'network');

            foreach ($this->legacySocialColumns as $column => $social) {
                if (filled($settings->{$column}) && ! in_array($social['network'], $networks, true)) {
                    $socialLinks[] = $social + ['url' => $settings->{$column}];
                }
            }

            DB::table('site_settings')->where('id', $settings->id)->update([
                'phones' => json_encode(filled($settings->phone) ? [['label' => null, 'number' => $settings->phone]] : []),
                'social_links' => json_encode($socialLinks),
            ]);
        }

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['phone', ...array_keys($this->legacySocialColumns)]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('address');
            foreach (array_keys($this->legacySocialColumns) as $column) {
                $table->string($column)->nullable();
            }
        });

        foreach (DB::table('site_settings')->get() as $settings) {
            $phones = json_decode($settings->phones ?? '[]', true) ?: [];
            $socialLinks = collect(json_decode($settings->social_links ?? '[]', true) ?: []);
            $update = ['phone' => $phones[0]['number'] ?? null];

            foreach ($this->legacySocialColumns as $column => $social) {
                $update[$column] = $socialLinks->firstWhere('network', $social['network'])['url'] ?? null;
            }

            DB::table('site_settings')->where('id', $settings->id)->update($update);
        }

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('phones');
        });
    }
};
