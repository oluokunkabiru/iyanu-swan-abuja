<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->unsignedTinyInteger('password_min_length')->default(12)->after('constitution_label');
            $table->boolean('password_require_mixed_case')->default(true)->after('password_min_length');
            $table->boolean('password_require_numbers')->default(true)->after('password_require_mixed_case');
            $table->boolean('password_require_symbols')->default(true)->after('password_require_numbers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'password_min_length',
                'password_require_mixed_case',
                'password_require_numbers',
                'password_require_symbols',
            ]);
        });
    }
};
