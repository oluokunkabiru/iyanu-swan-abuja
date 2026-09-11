<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * credential used to hold only "ACA"/"FCA" (varchar(20) was plenty),
     * but it now stores whichever Membership Level name a member
     * registered under — e.g. "AATWA (Associate Accounting Technician)"
     * — so 20 characters truncates real level names, and this app runs
     * with strict mode on, so that's a hard registration failure, not a
     * silent truncation. Raw SQL rather than the schema builder's
     * change(), since that needs doctrine/dbal, which isn't installed.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE member_profiles MODIFY credential VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE member_profiles MODIFY credential VARCHAR(20) NULL');
    }
};
