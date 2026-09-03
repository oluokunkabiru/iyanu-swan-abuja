<?php

namespace Database\Seeders;

use App\Models\CoreValue;
use App\Models\Event;
use App\Models\ExecutiveMember;
use App\Models\Partner;
use App\Models\SiteSetting;
use App\Models\Slider;
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

        $slides = [
            [
                'attributes' => [
                    'badge' => 'SWAN Abuja Chapter',
                    'title' => 'Women who hold the Charter and hold each other to it.',
                    'description' => 'A professional community for technical growth, leadership, mentorship and meaningful service across the Federal Capital Territory.',
                    'cta_label' => 'Join the chapter',
                    'cta_link' => '/membership/register',
                    'secondary_cta_label' => 'Discover SWAN',
                    'secondary_cta_link' => '/about',
                    'image_alt' => 'SWAN Abuja members at a professional seminar',
                    'image_position' => 'center',
                    'sort_order' => 1,
                    'is_active' => true,
                ],
                'image' => base_path('../frontend/src/assets/images/SWAN-17-1024x684.jpg'),
            ],
            [
                'attributes' => [
                    'badge' => 'Professional development',
                    'title' => 'Learning that keeps women at the front of the profession.',
                    'description' => 'Earn relevant CPD hours through technical seminars, practical workshops and leadership conversations designed for today’s accountant.',
                    'cta_label' => 'View upcoming events',
                    'cta_link' => '/events',
                    'secondary_cta_label' => 'Explore CPD',
                    'secondary_cta_link' => '/cpd',
                    'image_alt' => 'Delegates attending an accountancy conference',
                    'image_position' => 'center',
                    'sort_order' => 2,
                    'is_active' => true,
                ],
                'image' => base_path('../frontend/src/assets/images/DSC06955-1.jpg'),
            ],
            [
                'attributes' => [
                    'badge' => 'Connection and service',
                    'title' => 'A network that grows careers and strengthens communities.',
                    'description' => 'Build trusted professional relationships, find mentors and join chapter programmes that turn expertise into lasting impact.',
                    'cta_label' => 'Meet the community',
                    'cta_link' => '/governance',
                    'secondary_cta_label' => 'See chapter life',
                    'secondary_cta_link' => '/gallery',
                    'image_alt' => 'Members of SWAN Abuja spending time together at the chapter picnic',
                    'image_position' => 'center',
                    'sort_order' => 3,
                    'is_active' => true,
                ],
                'image' => base_path('../frontend/src/assets/images/SWAN-ABUJA-PICNIC-085-1024x663.jpg'),
            ],
        ];

        foreach ($slides as $slideData) {
            $slider = Slider::query()->updateOrCreate(
                ['title' => $slideData['attributes']['title']],
                $slideData['attributes'],
            );

            if (! $slider->hasMedia('image') && is_file($slideData['image'])) {
                $slider->addMedia($slideData['image'])
                    ->preservingOriginal()
                    ->toMediaCollection('image');
            }
        }

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
