<?php

namespace Database\Seeders;

use App\Models\CoreValue;
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
            'short_name' => 'SWAN Abuja',
            'parent_body' => 'Institute of Chartered Accountants of Nigeria',
            'tagline' => 'Empowering the professional female accountant',
            'mission' => 'To maintain the dignity of the professional female accountant and to empower women through financial literacy.',
            'mission_items' => [
                'To maintain the dignity of the professional female accountant, to be relevant and impactful, and to empower, mentor and uphold the ideals of the accounting profession.',
                'To foster growth, visibility and professional development, and to serve our communities through financial literacy.',
            ],
            'vision' => "To be the world's foremost professional association of Female Chartered Accountants, championing excellence in the accountancy profession.",
            'aims' => 'Promoting and upholding high standards of effectiveness and professional conduct without discrimination, and supporting ICAN in safeguarding its Charter, the status of the profession, and the interests of its female members.',
            'address' => 'Abuja, Nigeria',
            'phone' => '0803 450 2401',
            'email' => 'contact@swanabujachapter.com',
            'facebook_url' => 'https://facebook.com',
            'instagram_url' => 'https://instagram.com',
            'twitter_url' => 'https://x.com',
            'chairperson_heading' => 'Welcome to a chapter built for women to thrive.',
            'chairperson_message' => [
                'Welcome to the Abuja Chapter of the Society of Women Accountants of Nigeria. Our chapter exists to help women accountants grow in competence, lead with integrity and stay connected to a professional community that supports them.',
                'I invite you to take part in our technical programmes, mentorship, service projects and chapter life. Whether you are newly inducted or an established leader, there is a place here for your experience, your ambition and your contribution.',
            ],
            'chapter_stats' => [
                ['label' => 'Members on the chapter roll', 'value' => '640+', 'note' => 'Female ICAN members across the FCT'],
                ['label' => 'CPD hours delivered', 'value' => '1,240', 'note' => 'Across seminars, clinics and technical sessions'],
                ['label' => 'Standing committees', 'value' => '8', 'note' => 'Technical, welfare, outreach and mentorship'],
                ['label' => 'Years in the FCT', 'value' => '24', 'note' => 'Serving Abuja and the surrounding districts'],
            ],
            'registration_steps' => [
                ['step' => 1, 'title' => 'Pay your dues', 'description' => 'Pay ₦17,000 for the year — ₦5,000 subscription and ₦12,000 welfare. Card, transfer and USSD are all accepted through the portal.'],
                ['step' => 2, 'title' => 'Get confirmed', 'description' => 'Once the Financial Secretary confirms your payment, you are formally admitted to the Society and your record is opened.'],
                ['step' => 3, 'title' => 'Attend a meeting', 'description' => 'Show up at any scheduled chapter meeting. Attendance completes your registration and puts you on the active roll.'],
            ],
            'member_benefits' => [
                ['title' => 'Member rates on every event', 'description' => 'Sign in before you check out and the member price applies automatically — roughly 40% off the public rate on chapter seminars.'],
                ['title' => 'CPD credits that count', 'description' => 'Technical sessions, workshops and the annual seminar qualify for ICAN Continuing Professional Development credit. Register with your ICAN details so attendance is tracked.'],
                ['title' => 'Committee and leadership roles', 'description' => 'Active members are eligible to join standing committees and to stand for executive office within the chapter.'],
                ['title' => 'Mentorship and networking', 'description' => 'Direct access to senior female professionals, executive directors and decision-makers across the public and private sectors in Abuja.'],
                ['title' => 'Welfare support', 'description' => 'The welfare fund stands behind members through bereavement, illness and major life events, administered by the Welfare Officer.'],
                ['title' => 'Practice and career support', 'description' => 'Firm registration guidance, the chapter job board, and referrals through the members directory.'],
            ],
            'aims_objectives' => [
                'Promote and uphold high standards of effectiveness and professional conduct among female members, without discrimination.',
                'Support ICAN in safeguarding its Charter, the status of the profession, and the interests of its female members.',
                'Advance the professional development of women in accountancy through structured CPD, technical sessions and mentorship.',
                'Improve the visibility of female chartered accountants in leadership, governance and public financial management.',
                'Deliver financial literacy and community service across the Federal Capital Territory.',
                'Build a welfare structure that supports members through the whole of their professional lives.',
            ],
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
            ['email' => 'admin@swan.org'],
            ['name' => 'SWAN Admin', 'password' => bcrypt('password'), 'role' => 'admin']
        );
        $admin->memberProfile()->updateOrCreate([], [
            'membership_status' => 'active',
            'joined_at' => now(),
        ]);
    }
}
