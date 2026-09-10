<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('executive_members', function (Blueprint $table) {
            $table->unsignedSmallInteger('term_start_year')->nullable()->after('is_principal');
            $table->unsignedSmallInteger('term_end_year')->nullable()->after('term_start_year');
        });
    }

    public function down(): void
    {
        Schema::table('executive_members', function (Blueprint $table) {
            $table->dropColumn(['term_start_year', 'term_end_year']);
        });
    }
};
