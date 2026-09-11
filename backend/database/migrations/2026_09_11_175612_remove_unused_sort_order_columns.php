<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * These tables' sort_order columns were never actually used for
     * ordering — every public listing already sorted by created_at
     * (or, for programme entries, is moving to starts_at) — so this
     * drops the dead column in favour of alphabetical or date order.
     * Sliders and membership levels keep theirs: homepage carousel
     * position and admin-curated level pricing order genuinely need
     * manual control that no other column can express. Executive
     * members moves to alphabetical too, by explicit request, even
     * though that won't reflect committee protocol rank.
     *
     * @var string[]
     */
    private array $tablesWithIndex = ['committees', 'faqs', 'resource_items', 'programme_entries'];

    /** @var string[] */
    private array $tablesWithoutIndex = ['core_values', 'partners', 'gallery_images', 'member_spotlights', 'announcements', 'executive_members'];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tablesWithIndex as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                $blueprint->dropIndex("{$table}_is_active_sort_order_index");
                $blueprint->dropColumn('sort_order');
            });
        }

        foreach ($this->tablesWithoutIndex as $table) {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropColumn('sort_order'));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ([...$this->tablesWithIndex, ...$this->tablesWithoutIndex] as $table) {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->unsignedInteger('sort_order')->default(0));
        }

        foreach ($this->tablesWithIndex as $table) {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->index(['is_active', 'sort_order']));
        }
    }
};
