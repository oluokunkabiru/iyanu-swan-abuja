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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignId('membership_level_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->string('bank_transfer_reference')->nullable()->after('payment_gateway');
            $table->string('review_note')->nullable()->after('bank_transfer_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('membership_level_id');
            $table->dropColumn(['bank_transfer_reference', 'review_note']);
        });
    }
};
