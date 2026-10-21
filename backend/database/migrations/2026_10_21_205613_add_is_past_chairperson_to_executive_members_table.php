<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Past chairpersons get their own admin section instead of being
     * inferred from position = "Chairperson" + inactive/ex officio.
     * Rows that matched the old rule are flagged so the public list
     * doesn't change.
     */
    public function up(): void
    {
        Schema::table('executive_members', function (Blueprint $table) {
            $table->boolean('is_past_chairperson')->default(false)->after('is_chairperson');
        });

        DB::table('executive_members')
            ->where('position', 'Chairperson')
            ->where(fn ($query) => $query->where('is_active', false)->orWhere('is_ex_officio', true))
            ->update(['is_past_chairperson' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('executive_members', function (Blueprint $table) {
            $table->dropColumn('is_past_chairperson');
        });
    }
};
