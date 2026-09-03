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
            $table->string('short_name')->nullable()->after('chapter_name');
            $table->string('parent_body')->nullable()->after('short_name');
            $table->text('aims')->nullable()->after('vision');
            $table->json('mission_items')->nullable()->after('mission');
            $table->json('social_links')->nullable()->after('twitter_url');
            $table->string('linkedin_url')->nullable()->after('twitter_url');
            $table->string('chairperson_heading')->nullable()->after('hero_video_url');
            $table->json('chairperson_message')->nullable()->after('chairperson_heading');
            $table->json('chapter_stats')->nullable()->after('chairperson_message');
            $table->json('registration_steps')->nullable()->after('chapter_stats');
            $table->json('member_benefits')->nullable()->after('registration_steps');
            $table->json('aims_objectives')->nullable()->after('member_benefits');
        });

        Schema::table('executive_members', function (Blueprint $table) {
            $table->boolean('is_principal')->default(false)->after('is_active');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->text('summary')->nullable()->after('slug');
            $table->json('body')->nullable()->after('description');
            $table->string('category')->nullable()->after('body');
            $table->unsignedSmallInteger('cpd_hours')->default(0)->after('ends_at');
            $table->json('speakers')->nullable()->after('cpd_hours');
        });

        Schema::table('event_ticket_types', function (Blueprint $table) {
            $table->string('audience')->nullable()->after('label');
            $table->string('mode')->nullable()->after('audience');
            $table->json('includes')->nullable()->after('currency');
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->string('reference')->nullable()->unique()->after('amount');
            $table->dateTime('issued_at')->nullable()->after('reference');
        });

        Schema::table('news_posts', function (Blueprint $table) {
            $table->string('category')->nullable()->after('excerpt');
            $table->string('author')->nullable()->after('category');
        });

        Schema::table('gallery_images', function (Blueprint $table) {
            $table->string('album')->nullable()->after('caption');
            $table->unsignedSmallInteger('year')->nullable()->after('album');
        });

        Schema::table('partners', function (Blueprint $table) {
            $table->string('scope')->nullable()->after('url');
        });

        Schema::table('member_profiles', function (Blueprint $table) {
            $table->string('credential', 20)->nullable()->after('membership_number');
            $table->string('sector')->nullable()->after('phone');
            $table->string('specialisation')->nullable()->after('sector');
            $table->unsignedSmallInteger('year_admitted')->nullable()->after('specialisation');
            $table->string('chapter_role')->nullable()->after('year_admitted');
            $table->unsignedSmallInteger('cpd_target')->default(120)->after('chapter_role');
            $table->boolean('is_directory_listed')->default(true)->after('cpd_target');
            $table->index(['is_directory_listed', 'membership_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_profiles', function (Blueprint $table) {
            $table->dropIndex(['is_directory_listed', 'membership_status']);
            $table->dropColumn([
                'credential',
                'sector',
                'specialisation',
                'year_admitted',
                'chapter_role',
                'cpd_target',
                'is_directory_listed',
            ]);
        });

        Schema::table('partners', fn (Blueprint $table) => $table->dropColumn('scope'));
        Schema::table('gallery_images', fn (Blueprint $table) => $table->dropColumn(['album', 'year']));
        Schema::table('news_posts', fn (Blueprint $table) => $table->dropColumn(['category', 'author']));
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropUnique(['reference']);
            $table->dropColumn(['reference', 'issued_at']);
        });
        Schema::table('event_ticket_types', fn (Blueprint $table) => $table->dropColumn(['audience', 'mode', 'includes']));
        Schema::table('events', fn (Blueprint $table) => $table->dropColumn(['summary', 'body', 'category', 'cpd_hours', 'speakers']));
        Schema::table('executive_members', fn (Blueprint $table) => $table->dropColumn('is_principal'));
        Schema::table('site_settings', fn (Blueprint $table) => $table->dropColumn([
            'short_name',
            'parent_body',
            'aims',
            'mission_items',
            'social_links',
            'linkedin_url',
            'chairperson_heading',
            'chairperson_message',
            'chapter_stats',
            'registration_steps',
            'member_benefits',
            'aims_objectives',
        ]));
    }
};
