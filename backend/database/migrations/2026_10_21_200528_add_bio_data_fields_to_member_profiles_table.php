<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bio-data and next-of-kin fields modelled on the ICAN member
     * profile. All optional, so existing members aren't blocked until
     * they fill them in.
     */
    public function up(): void
    {
        Schema::table('member_profiles', function (Blueprint $table) {
            $table->string('title', 50)->nullable()->after('membership_number');
            $table->string('former_name')->nullable()->after('title');
            $table->string('alternate_phone', 50)->nullable()->after('phone');
            $table->string('marital_status', 50)->nullable()->after('date_of_birth');
            $table->string('nationality', 100)->nullable()->after('marital_status');
            $table->string('state_of_origin', 100)->nullable()->after('nationality');
            $table->string('lga_of_origin')->nullable()->after('state_of_origin');
            $table->string('next_of_kin_name')->nullable()->after('lga_of_origin');
            $table->string('next_of_kin_relationship', 100)->nullable()->after('next_of_kin_name');
            $table->string('next_of_kin_phone', 50)->nullable()->after('next_of_kin_relationship');
            $table->text('next_of_kin_address')->nullable()->after('next_of_kin_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'title', 'former_name', 'alternate_phone', 'marital_status', 'nationality',
                'state_of_origin', 'lga_of_origin', 'next_of_kin_name', 'next_of_kin_relationship',
                'next_of_kin_phone', 'next_of_kin_address',
            ]);
        });
    }
};
