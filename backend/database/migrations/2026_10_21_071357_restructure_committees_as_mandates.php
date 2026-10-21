<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Committees now follow the chapter's "Committees Mandate" document:
     * each one has a purpose, numbered objectives, a structure &
     * membership clause, a meetings clause and a reporting clause.
     * The old remit/focus areas/cadence columns map onto the first
     * three of those; chair becomes optional since the mandates don't
     * name one.
     */
    public function up(): void
    {
        Schema::table('committees', function (Blueprint $table) {
            $table->renameColumn('remit', 'purpose');
            $table->renameColumn('focus_areas', 'objectives');
            $table->renameColumn('meeting_cadence', 'meetings');
        });

        Schema::table('committees', function (Blueprint $table) {
            $table->text('meetings')->change();
            $table->string('chair')->nullable()->change();
            $table->string('subtitle')->nullable()->after('slug');
            $table->text('structure')->nullable()->after('objectives');
            $table->text('reporting')->nullable()->after('meetings');
            $table->text('note')->nullable()->after('reporting');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('committees', function (Blueprint $table) {
            $table->dropColumn(['subtitle', 'structure', 'reporting', 'note']);
            $table->renameColumn('purpose', 'remit');
            $table->renameColumn('objectives', 'focus_areas');
            $table->renameColumn('meetings', 'meeting_cadence');
        });

        Schema::table('committees', function (Blueprint $table) {
            $table->string('meeting_cadence')->change();
        });
    }
};
