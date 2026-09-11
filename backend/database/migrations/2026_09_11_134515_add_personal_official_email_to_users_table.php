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
        Schema::table('users', function (Blueprint $table) {
            $table->string('personal_email')->nullable()->unique()->after('email');
            $table->string('official_email')->nullable()->unique()->after('personal_email');
            $table->string('notification_email_preference')->nullable()->after('official_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['personal_email', 'official_email', 'notification_email_preference']);
        });
    }
};
