<?php

namespace Database\Seeders;

use App\Models\CoreValue;
use App\Models\Event;
use App\Models\ExecutiveMember;
use App\Models\Partner;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class SwanContentSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::query()->updateOrCreate(['id' => 1], [
            'chapter_name' => 'Society of Women Accountants of Nigeria (SWAN) — Abuja Chapter',
            'tagline' => 'Empowering the professional female accountant',
            'mission' => 'To maintain the dignity of the professional female accountant and to empower women through financial literacy.',
            'vision' => "To be the world's foremost professional association of Female Chartered Accountants, championing excellence in the accountancy profession.",
            'address' => 'Abuja, Nigeria',
            'phone' => '0803 450 2401',
            'email' => 'contact@swanabujachapter.com',
            'facebook_url' => 'https://facebook.com',
            'instagram_url' => 'https://instagram.com',
            'twitter_url' => 'https://x.com',
            'membership_subscription_fee' => 5000,
            'membership_welfare_fee' => 12000,
        ]);

        $values = [
            ['title' => 'Integrity', 'description' => 'Upholding honesty and strong moral principles in all we do.', 'sort_order' => 1],
            ['title' => 'Professionalism', 'description' => 'Maintaining the highest standards of the accountancy profession.', 'sort_order' => 2],
            ['title' => 'Passion', 'description' => 'Driven by a genuine commitment to our members and community.', 'sort_order' => 3],
            ['title' => 'Impact', 'description' => 'Creating measurable, lasting change through our work.', 'sort_order' => 4],
            ['title' => 'Accountability', 'description' => 'Taking ownership and responsibility for our actions and outcomes.', 'sort_order' => 5],
        ];

        foreach ($values as $value) {
            CoreValue::query()->updateOrCreate(['title' => $value['title']], $value);
        }

        $executives = [
            ['name' => 'Patricia Chinwe Ofili', 'credential' => 'ACA', 'position' => 'Chairperson', 'sort_order' => 1],
            ['name' => 'Dr Maryam Danna Mohammed', 'credential' => 'FCA', 'position' => 'Vice Chairperson', 'sort_order' => 2],
            ['name' => 'Ojoma Blessing Lawal-Adewale', 'credential' => 'ACA', 'position' => 'General Secretary', 'sort_order' => 3],
            ['name' => 'Biola Olawoore', 'credential' => 'FCA', 'position' => 'Treasurer', 'sort_order' => 4],
            ['name' => 'Ojoma Blessing Olaniran', 'credential' => 'FCA', 'position' => 'Financial Secretary', 'sort_order' => 5],
        ];

        foreach ($executives as $executive) {
            ExecutiveMember::query()->updateOrCreate(
                ['name' => $executive['name']],
                $executive + ['is_active' => true]
            );
        }

        Partner::query()->updateOrCreate(['name' => 'ICAN'], [
            'name' => 'ICAN',
            'url' => 'https://icanig.org',
            'sort_order' => 1,
        ]);

        $seminar = Event::query()->updateOrCreate(['slug' => 'swan-seminar-2026'], [
            'title' => "SWAN Seminar 2026: Demystifying Nigeria's New Tax Reform",
            'description' => "A seminar demystifying Nigeria's new tax reform for members and the public.",
            'location' => 'Abuja, Nigeria',
            'starts_at' => now()->addMonths(2)->setTime(9, 0),
            'status' => 'published',
            'is_featured' => true,
        ]);
        $seminar->ticketTypes()->updateOrCreate(['label' => 'Member Physical'], ['price' => 30000]);
        $seminar->ticketTypes()->updateOrCreate(['label' => 'Member Virtual'], ['price' => 15000]);
        $seminar->ticketTypes()->updateOrCreate(['label' => 'Non-Member Physical'], ['price' => 50000]);
        $seminar->ticketTypes()->updateOrCreate(['label' => 'Non-Member Virtual'], ['price' => 15000]);

        $orphanage = Event::query()->updateOrCreate(['slug' => 'kwali-orphanage-visit'], [
            'title' => 'Visit to Kwali Orphanage',
            'description' => 'A charitable visit and donation drive to the Kwali Orphanage.',
            'location' => 'Kwali, Abuja',
            'starts_at' => now()->addMonth()->setTime(10, 0),
            'status' => 'published',
        ]);
        $orphanage->ticketTypes()->updateOrCreate(['label' => 'Volunteer'], ['price' => 0]);

        $outreach = Event::query()->updateOrCreate(['slug' => 'medical-outreach'], [
            'title' => 'Medical Outreach',
            'description' => 'A free community medical outreach programme organised by SWAN Abuja Chapter.',
            'location' => 'Abuja, Nigeria',
            'starts_at' => now()->addWeeks(6)->setTime(9, 0),
            'status' => 'published',
        ]);
        $outreach->ticketTypes()->updateOrCreate(['label' => 'Volunteer'], ['price' => 0]);

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@the365official.com'],
            ['name' => 'SWAN Admin', 'password' => bcrypt('password'), 'role' => 'admin']
        );
        $admin->memberProfile()->updateOrCreate([], [
            'membership_status' => 'active',
            'joined_at' => now(),
        ]);
    }
}
