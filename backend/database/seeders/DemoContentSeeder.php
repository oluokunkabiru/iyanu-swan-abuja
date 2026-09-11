<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Committee;
use App\Models\CpdRecord;
use App\Models\Event;
use App\Models\ExecutiveMember;
use App\Models\Faq;
use App\Models\Firm;
use App\Models\GalleryImage;
use App\Models\JobListing;
use App\Models\NewsPost;
use App\Models\Partner;
use App\Models\ProgrammeEntry;
use App\Models\Publication;
use App\Models\ResourceItem;
use App\Models\SiteSetting;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\MediaLibrary\HasMedia;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedExecutives();
        $this->seedEventsAndNews();
        $this->seedPublicContent();
        $this->seedDirectories();
        $this->seedMemberArea();
    }

    private function seedSettings(): void
    {
        $settings = SiteSetting::query()->updateOrCreate(['id' => 1], [
            'chapter_name' => 'SWAN Abuja Chapter',
            'short_name' => 'SWAN Abuja',
            'parent_body' => 'Institute of Chartered Accountants of Nigeria',
            'tagline' => 'Society of Women Accountants of Nigeria, Abuja Chapter',
            'vision' => "To be the world's foremost professional association of female chartered accountants, championing excellence and leadership through empowerment, advocacy and global collaboration.",
            'mission_items' => [
                'To maintain the dignity of the professional female accountant and uphold the ideals of the profession.',
                'To foster growth, visibility and professional development, and serve communities through financial literacy.',
            ],
            'aims' => 'Promoting high standards of professional conduct and supporting ICAN in safeguarding its Charter and female members.',
            'address' => 'Abuja, Federal Capital Territory, Nigeria',
            'phone' => '0803 450 2401',
            'email' => 'contact@swanabujachapter.com',
            'social_links' => [
                ['label' => 'Facebook', 'url' => 'https://facebook.com', 'network' => 'facebook'],
                ['label' => 'X', 'url' => 'https://x.com', 'network' => 'twitter'],
                ['label' => 'Instagram', 'url' => 'https://instagram.com', 'network' => 'instagram'],
                ['label' => 'LinkedIn', 'url' => 'https://linkedin.com', 'network' => 'linkedin'],
            ],
            'chairperson_heading' => 'Welcome to a chapter built for women to thrive.',
            'chairperson_message' => [
                'Our chapter helps women accountants grow in competence, lead with integrity and stay connected to a supportive professional community.',
                'There is a place here for your experience, ambition and contribution through technical programmes, mentorship and service.',
            ],
            'chapter_stats' => [
                ['label' => 'Members on the chapter roll', 'value' => '640+', 'note' => 'Female ICAN members across the FCT'],
                ['label' => 'CPD hours delivered', 'value' => '1,240', 'note' => 'Across seminars, clinics and technical sessions'],
                ['label' => 'Standing committees', 'value' => '8', 'note' => 'Technical, welfare, outreach and mentorship'],
                ['label' => 'Years in the FCT', 'value' => '24', 'note' => 'Serving Abuja and surrounding districts'],
            ],
            'registration_steps' => [
                ['step' => 1, 'title' => 'Pay your dues', 'description' => 'Pay the annual subscription and welfare levy through the portal.'],
                ['step' => 2, 'title' => 'Get confirmed', 'description' => 'The Financial Secretary confirms payment and opens your record.'],
                ['step' => 3, 'title' => 'Attend a meeting', 'description' => 'Meeting attendance completes active registration.'],
            ],
            'member_benefits' => [
                ['title' => 'Member event rates', 'description' => 'Active members receive preferred chapter rates.'],
                ['title' => 'CPD credits', 'description' => 'Qualifying chapter learning is tracked against the ICAN cycle.'],
                ['title' => 'Leadership', 'description' => 'Active members may serve on committees and stand for office.'],
                ['title' => 'Mentorship', 'description' => 'Connect with senior professionals across sectors.'],
                ['title' => 'Welfare support', 'description' => 'Member support through illness, bereavement and major life events.'],
                ['title' => 'Career support', 'description' => 'Licensing guidance, job listings and directory referrals.'],
            ],
            'aims_objectives' => [
                'Promote high standards of effectiveness and professional conduct.',
                'Support ICAN in safeguarding its Charter and members.',
                'Advance women through CPD, technical sessions and mentorship.',
                'Improve the visibility of female chartered accountants in leadership.',
                'Deliver financial literacy and community service across the FCT.',
                'Build a dependable welfare structure for members.',
            ],
            'membership_subscription_fee' => 5000,
            'membership_welfare_fee' => 12000,
        ]);

        $logo = base_path('../frontend/src/assets/logo.png');
        if (! $settings->hasMedia('logo') && is_file($logo)) {
            $settings->addMedia($logo)->preservingOriginal()->toMediaCollection('logo');
        }
    }

    private function seedExecutives(): void
    {
        $executives = [
            ['Patricia Chinwe Ofili', 'ACA', 'Chairperson', true, 'chairperson-cropped.jpeg'],
            ['Dr Maryam Danna Mohammed', 'FCA', 'Vice Chairperson', true, 'Vice-chair-cropped-1024x1024.jpeg'],
            ['Ojoma Blessing Lawal-Adewale', 'ACA', 'General Secretary', true, 'Gen-sec-cropped.jpeg'],
            ['Biola Olawoore', 'FCA', 'Treasurer', true, 'treasurer-cropped.jpeg'],
            ['Ojoma Blessing Olaniran', 'FCA', 'Financial Secretary', true, 'Fin-sec-cropped.jpeg'],
            ['Nsini Bassey', 'FCA', 'Membership Secretary', false, 'Membership-sec-cropped.jpeg'],
            ['Taiye Fasan', 'ACA', 'Welfare Officer', false, 'gray-female-avatar-placeholder-nobg.png'],
            ['Oluwakemi Toluwani', 'FCA', 'Publicity Officer', false, 'Public-Sec-cropped-1024x1024.jpeg'],
            ['Aisha Bello Oroche', 'FCA', 'Assistant General Secretary', false, 'Asst-Gen-sec-cropped.jpeg'],
            ['Ngozi Francisca Ashinze', 'FCA', 'Technical Secretary', false, 'gray-female-avatar-placeholder-nobg.png'],
            ['Monica Chinenye Nosike', 'FCA', 'Immediate Past Chairperson', false, 'gray-female-avatar-placeholder-nobg.png'],
            ['Charity Okongwu', 'FCA', 'Ex-Officio Member', false, 'Ex-officio-cropped-1024x1024.jpeg'],
        ];

        foreach ($executives as $index => [$name, $credential, $position, $principal, $image]) {
            $executive = ExecutiveMember::query()->updateOrCreate(['name' => $name], [
                'credential' => $credential, 'position' => $position,
                'bio' => 'Serves the chapter through the responsibilities of the '.$position.'.',
                'sort_order' => $index + 1, 'is_active' => true, 'is_principal' => $principal,
            ]);
            $this->attachAsset($executive, 'photo', $image);
        }
    }

    private function seedEventsAndNews(): void
    {
        $events = [
            ['demystifying-nigerias-new-tax-reform', "Demystifying Nigeria's new tax reform", 'Seminar', 'Chelsea Hotel, Abuja', '2026-05-22 09:00:00', 6, true, 'SWAN-17-1024x684.jpg'],
            ['annual-technical-seminar-2026', 'Annual technical seminar: sustainability reporting and the assurance gap', 'Seminar', 'Transcorp Hilton, Abuja', '2026-10-09 08:30:00', 7, true, 'DSC06955-1.jpg'],
            ['mentorship-clinic-newly-inducted', 'Mentorship clinic for newly inducted members', 'Training', 'ICAN Abuja Liaison Office', '2026-09-27 10:00:00', 3, false, 'SWAN-ABUJA-PICNIC-085-1024x663.jpg'],
            ['october-general-meeting', 'October general meeting', 'Meeting', 'ICAN Abuja Liaison Office', '2026-10-25 11:00:00', 1, false, null],
            ['visit-to-kwali-orphanage', 'Visit to Kwali orphanage', 'Outreach', 'Kwali Area Council, FCT', '2026-02-14 09:00:00', 0, true, 'SWAN-KWALI-ORPHANAGE-22-1024x683.jpg'],
            ['community-medical-outreach', 'Community medical outreach', 'Outreach', 'Bwari Area Council, FCT', '2026-02-28 08:00:00', 0, true, 'DSC05883-1024x684.jpg'],
        ];

        foreach ($events as [$slug, $title, $category, $venue, $startsAt, $hours, $featured, $image]) {
            $event = Event::query()->updateOrCreate(['slug' => $slug], [
                'title' => $title,
                'summary' => $title.' — a SWAN Abuja Chapter programme.',
                'body' => ['Programme details are published by the chapter.', 'Register through the portal where registration applies.'],
                'category' => $category,
                'location' => $venue,
                'starts_at' => $startsAt,
                'cpd_hours' => $hours,
                'speakers' => [],
                'is_featured' => $featured,
                'status' => 'published',
            ]);

            if (in_array($category, ['Seminar', 'Training'], true)) {
                foreach ([
                    ['Member — attending in person', 'member', 'physical', 30000],
                    ['Member — attending online', 'member', 'virtual', 15000],
                    ['Non-member — attending in person', 'non-member', 'physical', 50000],
                    ['Non-member — attending online', 'non-member', 'virtual', 15000],
                ] as [$label, $audience, $mode, $price]) {
                    $event->ticketTypes()->updateOrCreate(['label' => $label], [
                        'audience' => $audience, 'mode' => $mode, 'price' => $price, 'currency' => 'NGN',
                        'includes' => ['Attendance', 'Delegate materials', 'CPD credit where applicable'],
                    ]);
                }
            }

            if ($image !== null) {
                $this->attachAsset($event, 'cover', $image);
            }
        }

        foreach ([
            ['communique-new-tax-reform', 'Chapter issues communiqué on the new tax reform', 'Advocacy', 'Technical Committee', '2026-06-04', 'SWAN-17-1024x684.jpg'],
            ['second-mentorship-cohort-opens', 'Second mentorship cohort opens with forty pairings', 'Chapter', 'Nsini Bassey, FCA', '2026-05-19', 'SWAN-ABUJA-PICNIC-085-1024x663.jpg'],
            ['annual-accountants-conference-abuja', '56th Annual Accountants’ Conference comes to Abuja', 'ICAN', 'Publicity Committee', '2026-04-30', 'DSC06955-1.jpg'],
            ['women-in-practice-survey', 'Women in practice: chapter survey finds licensing the main barrier', 'Profession', 'Technical Committee', '2026-03-12', 'WhatsApp-Image-2026-07-06-at-16.15.25-967x1024.jpeg'],
            ['outreach-report-welfare-levy', 'Outreach report: what the welfare levy paid for this year', 'Chapter', 'Taiye Fasan, ACA', '2026-02-28', 'SWAN-KWALI-ORPHANAGE-22-1024x683.jpg'],
        ] as [$slug, $title, $category, $author, $date, $image]) {
            $post = NewsPost::query()->updateOrCreate(['slug' => $slug], [
                'title' => $title, 'excerpt' => $title.'.', 'body' => [$title.'.', 'Read the full chapter update.'],
                'category' => $category, 'author' => $author, 'published_at' => $date, 'is_published' => true,
            ]);
            $this->attachAsset($post, 'cover', $image);
        }
    }

    private function seedPublicContent(): void
    {
        $this->upsertOrdered(Announcement::class, 'title', [
            ['2027 subscription and welfare levy — payment window opens 1 November', 'published_on' => '2026-08-28', 'href' => '/members/subscription', 'kind' => 'deadline'],
            ['Reminder: CPD requirement is 120 credit hours over three consecutive years', 'published_on' => '2026-08-14', 'href' => '/cpd', 'kind' => 'notice'],
            ['Discounted hotel rates for the 56th Annual Accountants’ Conference', 'published_on' => '2026-08-02', 'href' => '/news/annual-accountants-conference-abuja', 'kind' => 'circular'],
            ['Notice of chapter elections and call for nominations', 'published_on' => '2026-07-21', 'href' => '/governance', 'kind' => 'notice'],
            ['Practice licence renewal — procedure and closing date', 'published_on' => '2026-07-09', 'href' => '/practice', 'kind' => 'deadline'],
            ['Security notice: phishing messages circulating in the name of the chapter', 'published_on' => '2026-06-18', 'href' => '/announcements', 'kind' => 'notice'],
            ['Members directory refresh — confirm your entry before 30 September', 'published_on' => '2026-06-05', 'href' => '/directory', 'kind' => 'deadline'],
        ]);

        $this->upsertOrdered(ProgrammeEntry::class, 'name', [
            ['Mentorship clinic for newly inducted members', 'date_label' => '27 September 2026', 'venue' => 'ICAN Abuja Liaison Office', 'href' => '/events/mentorship-clinic-newly-inducted'],
            ['Annual technical seminar — sustainability reporting', 'date_label' => '9 October 2026', 'venue' => 'Transcorp Hilton, Maitama', 'href' => '/events/annual-technical-seminar-2026'],
            ['56th Annual Accountants’ Conference', 'date_label' => '18 – 23 October 2026', 'venue' => 'Abuja', 'href' => '/news/annual-accountants-conference-abuja'],
            ['October general meeting', 'date_label' => '25 October 2026', 'venue' => 'ICAN Abuja Liaison Office', 'href' => '/events/october-general-meeting'],
        ]);

        $faqs = [
            ['General', 'What is SWAN, and what does the Abuja Chapter do?'],
            ['Membership', 'Who is eligible to become a member of SWAN Abuja?'],
            ['Events', 'Can non-accountants or students attend chapter events?'],
            ['Payments', 'How does the tiered event pricing work?'],
            ['Membership', 'What are the benefits of being an active member?'],
            ['CPD', 'Do chapter events count toward my ICAN CPD hours?'],
            ['CPD', 'How many CPD hours do I need?'],
            ['Payments', 'What payment methods are accepted?'],
            ['Payments', 'Can I pay for an event at the venue?'],
            ['Membership', 'My dues lapsed. How do I get back on the active roll?'],
        ];
        foreach ($faqs as $index => [$topic, $question]) {
            Faq::query()->updateOrCreate(['question' => $question], [
                'topic' => $topic, 'answer' => 'The chapter team can provide current guidance through the website and member portal.',
                'sort_order' => $index + 1, 'is_active' => true,
            ]);
        }

        $committees = [
            ['Technical and Research', 'technical-and-research', 'Ngozi Francisca Ashinze, FCA'],
            ['Membership and Records', 'membership-and-records', 'Nsini Bassey, FCA'],
            ['Professional Development', 'professional-development', 'Dr Maryam Danna Mohammed, FCA'],
            ['Mentorship and Career', 'mentorship-and-career', 'Aisha Bello Oroche, FCA'],
            ['Women in Practice', 'women-in-practice', 'Charity Okongwu, FCA'],
            ['Welfare', 'welfare', 'Taiye Fasan, ACA'],
            ['Community Outreach', 'community-outreach', 'Oluwakemi Toluwani, FCA'],
            ['Publicity and Communications', 'publicity-and-communications', 'Oluwakemi Toluwani, FCA'],
        ];
        foreach ($committees as $index => [$name, $slug, $chair]) {
            Committee::query()->updateOrCreate(['slug' => $slug], [
                'name' => $name, 'remit' => 'Plans and delivers the chapter work assigned to this committee.',
                'chair' => $chair, 'focus_areas' => ['Planning', 'Member engagement', 'Reporting'],
                'meeting_cadence' => 'Monthly', 'sort_order' => $index + 1, 'is_active' => true,
            ]);
        }

        foreach ([
            ['Sustainability reporting: preparing a first IFRS S1 disclosure', 'SWAN Abuja', 'Hybrid', '2026-09-18', 4, 35000, 20000, 22],
            ['Tax reform workshop for practitioners', 'SWAN Abuja', 'Physical', '2026-09-30', 5, 45000, 25000, 8],
            ['Forensic accounting and fraud risk in public finance', 'Faculty', 'Virtual', '2026-10-14', 3, 25000, 15000, 46],
            ['Professional ethics and the revised code', 'ICAN MPD', 'Virtual', '2026-10-22', 2, 15000, 10000, 60],
            ['Data analytics for the audit file', 'Faculty', 'Hybrid', '2026-11-06', 6, 55000, 32000, 18],
            ['Board readiness for senior finance professionals', 'SWAN Abuja', 'Physical', '2026-11-20', 5, 60000, 35000, 12],
        ] as [$title, $provider, $mode, $date, $hours, $fee, $memberFee, $seats]) {
            Training::query()->updateOrCreate(['title' => $title], [
                'provider' => $provider, 'delivery_mode' => $mode, 'starts_on' => $date, 'cpd_hours' => $hours,
                'fee' => $fee, 'member_fee' => $memberFee, 'seats_available' => $seats, 'is_active' => true,
            ]);
        }

        foreach ([
            ['Chapter membership registration form', 'Form', 'PDF'], ['CPD activity log template', 'Template', 'XLSX'],
            ['Procedure for firm registration', 'Guide', 'PDF'], ['Practice licence renewal — conditions and procedure', 'Guide', 'PDF'],
            ['Practice attachment application form', 'Form', 'DOCX'], ['SWAN Abuja Chapter constitution and standing rules', 'Policy', 'PDF'],
            ['Welfare fund policy', 'Policy', 'PDF'], ['Mentorship programme pack', 'Template', 'DOCX'],
            ['ICAN professional examination syllabus', 'Syllabus', 'PDF'],
        ] as $index => [$title, $category, $format]) {
            ResourceItem::query()->updateOrCreate(['title' => $title], [
                'description' => 'Downloadable chapter resource.', 'category' => $category, 'format' => $format,
                'sort_order' => $index + 1, 'is_active' => true,
            ]);
        }

        foreach ([
            ['Communiqué issued at the close of the 2026 chapter technical seminar', 'Communiqué', '2026-06-04'],
            ['Chapter annual report and financial statements, 2025', 'Report', '2026-03-30'],
            ['The Abuja Ledger — second quarter newsletter', 'Newsletter', '2026-07-15'],
            ['Technical bulletin: applying IFRS S1 and S2', 'Technical', '2026-05-02'],
            ["Chairperson's address at the investiture", 'Address', '2026-01-24'],
            ['Women in practice: survey findings', 'Report', '2026-03-12'],
            ['The Abuja Ledger — first quarter newsletter', 'Newsletter', '2026-04-08'],
            ['Technical bulletin: transitional tax provisions', 'Technical', '2026-06-20'],
        ] as [$title, $category, $date]) {
            Publication::query()->updateOrCreate(['title' => $title], ['category' => $category, 'published_at' => $date]);
        }
    }

    private function seedDirectories(): void
    {
        $members = [
            ['Patricia Chinwe Ofili', 'ACA', 'ICAN/041826', 'Consulting', 2011, 'Chairperson'],
            ['Dr Maryam Danna Mohammed', 'FCA', 'ICAN/022914', 'Academia', 2003, 'Vice Chairperson'],
            ['Ojoma Blessing Lawal-Adewale', 'ACA', 'ICAN/048301', 'Financial services', 2014, 'General Secretary'],
            ['Biola Olawoore', 'FCA', 'ICAN/019477', 'Public practice', 2001, 'Treasurer'],
            ['Ojoma Blessing Olaniran', 'FCA', 'ICAN/026118', 'Public sector', 2005, 'Financial Secretary'],
            ['Nsini Bassey', 'FCA', 'ICAN/023390', 'Industry', 2004, 'Membership Secretary'],
            ['Taiye Fasan', 'ACA', 'ICAN/051204', 'Financial services', 2016, 'Welfare Officer'],
            ['Oluwakemi Toluwani', 'FCA', 'ICAN/028855', 'Consulting', 2006, 'Publicity Officer'],
            ['Aisha Bello Oroche', 'FCA', 'ICAN/027431', 'Public sector', 2006, 'Assistant General Secretary'],
            ['Ngozi Francisca Ashinze', 'FCA', 'ICAN/021760', 'Public practice', 2002, 'Technical Secretary'],
            ['Monica Chinenye Nosike', 'FCA', 'ICAN/018902', 'Consulting', 2000, 'Immediate Past Chairperson'],
            ['Charity Okongwu', 'FCA', 'ICAN/017234', 'Public practice', 1999, 'Ex-Officio Member'],
            ['Halima Sadiq Umar', 'ACA', 'ICAN/056420', 'Financial services', 2019, null],
            ['Grace Iyabo Adeniyi', 'FCA', 'ICAN/024577', 'Industry', 2004, null],
            ['Chiamaka Nwosu', 'ACA', 'ICAN/059013', 'Public practice', 2021, null],
            ['Rukayat Adebola Salami', 'ACA', 'ICAN/054766', 'Public sector', 2018, null],
            ['Esther Terlumun Akaa', 'FCA', 'ICAN/025991', 'Academia', 2005, null],
            ['Zainab Yusuf Bala', 'ACA', 'ICAN/060842', 'Industry', 2022, null],
        ];
        foreach ($members as $index => [$name, $credential, $number, $sector, $year, $role]) {
            $email = $index === 0 ? 'member@swanabujachapter.com' : 'member'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT).'@example.test';
            $user = User::query()->updateOrCreate(['email' => $email], [
                'name' => $name, 'password' => Hash::make('password'), 'role' => 'member',
            ]);
            $user->memberProfile()->updateOrCreate([], [
                'membership_number' => $number, 'credential' => $credential, 'membership_status' => 'active',
                'sector' => $sector, 'specialisation' => 'Professional accountancy', 'year_admitted' => $year,
                'chapter_role' => $role, 'cpd_target' => 120, 'is_directory_listed' => true, 'joined_at' => $year.'-01-01',
            ]);
        }

        foreach ([
            ['Olawoore & Co. (Chartered Accountants)', 'Biola Olawoore, FCA', 'PL/2009/0431', ['Audit', 'Assurance'], 'Wuse II', 'Active'],
            ['Ashinze Forensic Partners', 'Ngozi Francisca Ashinze, FCA', 'PL/2012/0788', ['Forensic accounting', 'Investigations'], 'Garki', 'Active'],
            ['Okongwu Restructuring Advisers', 'Charity Okongwu, FCA', 'PL/2007/0219', ['Insolvency', 'Restructuring'], 'Maitama', 'Active'],
            ['Nwosu Tax & Compliance', 'Chiamaka Nwosu, ACA', 'PL/2023/1904', ['Tax compliance', 'Transfer pricing'], 'Jabi', 'Renewal due'],
            ['Adeniyi & Associates', 'Grace Iyabo Adeniyi, FCA', 'PL/2010/0552', ['Bookkeeping', 'Payroll'], 'Gwarinpa', 'Active'],
            ['Sadiq Umar Consulting', 'Halima Sadiq Umar, ACA', 'PL/2022/1671', ['Treasury advisory', 'Financial modelling'], 'CBD', 'Active'],
        ] as [$name, $principal, $licence, $services, $area, $status]) {
            Firm::query()->updateOrCreate(['licence_number' => $licence], [
                'name' => $name, 'principal' => $principal, 'services' => $services, 'area' => $area,
                'licence_status' => $status, 'is_active' => true,
            ]);
        }

        foreach ([
            ['Head of Internal Audit', 'Federal agency, FCT', 'Full-time', 'Executive', '2026-08-26', '2026-09-20'],
            ['Financial Reporting Manager', 'Commercial bank', 'Full-time', 'Senior', '2026-08-19', '2026-09-15'],
            ['Tax Advisory Associate', 'Mid-tier practice', 'Full-time', 'Mid', '2026-08-14', '2026-09-12'],
            ['Grant Finance Officer', 'Development partner', 'Contract', 'Mid', '2026-08-08', '2026-09-05'],
            ['Audit Senior', 'Chartered accountants, Wuse', 'Full-time', 'Mid', '2026-07-30', '2026-09-01'],
            ['Lecturer, Accounting', 'Private university, FCT', 'Part-time', 'Mid', '2026-07-22', '2026-09-30'],
        ] as [$title, $organisation, $type, $level, $postedAt, $closesAt]) {
            JobListing::query()->updateOrCreate(['title' => $title, 'organisation' => $organisation], [
                'location' => 'Abuja', 'employment_type' => $type, 'seniority_level' => $level,
                'posted_at' => $postedAt, 'closes_at' => $closesAt,
                'summary' => 'Professional opportunity for qualified candidates.', 'is_active' => true,
            ]);
        }

        foreach ([
            ['Annual seminar, plenary session', 'Technical seminars', 'SWAN-17-1024x684.jpg'],
            ['Delegates at the chapter seminar', 'Technical seminars', 'DSC06955-1.jpg'],
            ['Kwali orphanage outreach visit', 'Community outreach', 'SWAN-KWALI-ORPHANAGE-22-1024x683.jpg'],
            ['Medical screening day', 'Community outreach', 'DSC05883-1024x684.jpg'],
            ['Chapter picnic', 'Chapter life', 'SWAN-ABUJA-PICNIC-085-1024x663.jpg'],
            ['Members at the general meeting', 'Chapter life', 'WhatsApp-Image-2026-07-06-at-16.15.25-967x1024.jpeg'],
        ] as $index => [$caption, $album, $image]) {
            $record = GalleryImage::query()->updateOrCreate(['caption' => $caption], [
                'album' => $album, 'year' => 2026, 'sort_order' => $index + 1,
            ]);
            $this->attachAsset($record, 'image', $image);
        }

        foreach ([
            ['Institute of Chartered Accountants of Nigeria', 'https://icanig.org/ican/', 'Parent body'],
            ['SWAN National', '/about', 'Parent body'],
            ['Association of Accountancy Bodies in West Africa', 'https://abwa.org.ng/', 'Affiliate'],
            ['Pan African Federation of Accountants', 'https://www.pafa.org.za/', 'Affiliate'],
            ['International Federation of Accountants', 'https://www.ifac.org/', 'Affiliate'],
            ['Chartered Accountants Worldwide', 'https://charteredaccountantsworldwide.com/', 'Affiliate'],
        ] as $index => [$name, $url, $scope]) {
            Partner::query()->updateOrCreate(['name' => $name], ['url' => $url, 'scope' => $scope, 'sort_order' => $index + 1]);
        }
    }

    private function seedMemberArea(): void
    {
        $user = User::query()->where('email', 'member@swanabujachapter.com')->firstOrFail();
        foreach ([
            ['Chapter technical seminar — tax reform', '2026-05-22', 6, 'Structured', true],
            ['Forensic accounting webinar', '2026-04-11', 3, 'Structured', true],
            ['Professional ethics refresher', '2026-03-07', 2, 'Structured', true],
            ['IFRS S1 and S2 technical reading', '2026-02-19', 4, 'Unstructured', false],
            ['Mentorship sessions delivered', '2026-01-30', 5, 'Unstructured', true],
            ['55th Annual Accountants’ Conference', '2025-10-16', 18, 'Structured', true],
            ['Data analytics for the audit file', '2025-08-14', 6, 'Structured', true],
            ['Public financial management masterclass', '2025-05-29', 7, 'Structured', true],
            ['Sustainability assurance seminar', '2024-10-03', 7, 'Structured', true],
            ['Board readiness programme', '2024-06-20', 5, 'Structured', true],
        ] as [$activity, $date, $hours, $type, $verified]) {
            CpdRecord::query()->updateOrCreate(['user_id' => $user->id, 'activity' => $activity], [
                'activity_date' => $date, 'hours' => $hours, 'activity_type' => $type, 'is_verified' => $verified,
            ]);
        }

        foreach ([
            [2027, 5000, 12000, 'outstanding', null, null],
            [2026, 5000, 12000, 'paid', '2026-01-18', 'SWN-2026-004182'],
            [2025, 5000, 12000, 'paid', '2025-02-02', 'SWN-2025-003914'],
            [2024, 5000, 10000, 'paid', '2024-01-27', 'SWN-2024-003501'],
        ] as [$year, $subscription, $welfare, $status, $paidAt, $reference]) {
            Subscription::query()->updateOrCreate(['user_id' => $user->id, 'year' => $year], [
                'subscription_amount' => $subscription, 'welfare_amount' => $welfare, 'status' => $status,
                'paid_at' => $paidAt, 'reference' => $reference,
            ]);
        }

        foreach ([
            ['annual-technical-seminar-2026', 'Member — attending in person', 30000, 'TKT-9F42-AB18', 'paid', '2026-08-21'],
            ['mentorship-clinic-newly-inducted', 'Member — attending online', 15000, 'TKT-7C10-DD03', 'pending', '2026-08-29'],
            ['demystifying-nigerias-new-tax-reform', 'Member — attending in person', 30000, 'TKT-2A77-90BE', 'paid', '2026-05-02'],
        ] as [$eventSlug, $tierLabel, $amount, $reference, $status, $issuedAt]) {
            $event = Event::query()->where('slug', $eventSlug)->firstOrFail();
            $tier = $event->ticketTypes()->where('label', $tierLabel)->firstOrFail();
            $event->registrations()->updateOrCreate(['reference' => $reference], [
                'event_ticket_type_id' => $tier->id, 'user_id' => $user->id, 'name' => $user->name,
                'email' => $user->email, 'payment_status' => $status, 'amount' => $amount, 'issued_at' => $issuedAt,
            ]);
        }
    }

    /** @param class-string<Model> $model */
    private function upsertOrdered(string $model, string $key, array $rows): void
    {
        foreach ($rows as $index => $row) {
            $value = array_shift($row);
            $model::query()->updateOrCreate([$key => $value], $row + ['sort_order' => $index + 1, 'is_active' => true]);
        }
    }

    private function attachAsset(Model&HasMedia $model, string $collection, string $filename): void
    {
        $path = base_path('../frontend/src/assets/images/'.$filename);

        if (! $model->hasMedia($collection) && is_file($path)) {
            $model->addMedia($path)->preservingOriginal()->toMediaCollection($collection);
        }
    }
}
