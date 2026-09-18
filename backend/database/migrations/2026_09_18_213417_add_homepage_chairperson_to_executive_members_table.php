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
        Schema::table('executive_members', function (Blueprint $table) {
            $table->boolean('is_chairperson')->default(false)->after('is_ex_officio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('executive_members', function (Blueprint $table) {
            $table->dropColumn('is_chairperson');
        });
    }
};
