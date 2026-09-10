<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('email_enabled')->default(true);
            $table->boolean('sms_enabled')->default(false);
            $table->string('sms_provider')->default('termii');
            $table->boolean('whatsapp_enabled')->default(false);
            $table->string('whatsapp_provider')->default('termii');
            $table->json('birthday_channels')->nullable();
            $table->json('broadcast_channels')->nullable();
            $table->json('newsletter_channels')->nullable();
            $table->json('event_notification_channels')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
