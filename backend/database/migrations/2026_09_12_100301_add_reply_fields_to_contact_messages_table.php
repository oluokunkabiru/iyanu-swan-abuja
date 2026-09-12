<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->text('reply_message')->nullable()->after('message');
            $table->dateTime('replied_at')->nullable()->after('reply_message');
            $table->foreignId('replied_by_user_id')->nullable()->after('replied_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('replied_by_user_id');
            $table->dropColumn(['reply_message', 'replied_at']);
        });
    }
};
