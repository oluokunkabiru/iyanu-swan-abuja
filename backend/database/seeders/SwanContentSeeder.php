<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Committee;
use App\Models\CoreValue;
use App\Models\CpdRecord;
use App\Models\Event;
use App\Models\ExecutiveMember;
use App\Models\Faq;
use App\Models\Firm;
use App\Models\GalleryImage;
use App\Models\JobListing;
use App\Models\MembershipLevel;
use App\Models\MemberSpotlight;
use App\Models\NewsPost;
use App\Models\Partner;
use App\Models\ProgrammeEntry;
use App\Models\Publication;
use App\Models\ResourceItem;
use App\Models\SiteSetting;
use App\Models\Slider;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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
                ['step' => 1, 'title' => 'Pay your dues', 'description' => 'Choose your membership level and pay the year\'s subscription and welfare levy — by card, transfer or USSD through the portal, or by bank transfer with evidence for admin review.'],
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
        ]);

        MembershipLevel::firstOrCreate(['name' => 'Standard Membership'], [
            'description' => 'The chapter\'s regular annual subscription and welfare levy.',
            'subscription_amount' => 5000,
            'welfare_amount' => 12000,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $values = [
            ['title' => 'Integrity', 'description' => 'Upholding honesty and strong moral principles in all we do.'],
            ['title' => 'Professionalism', 'description' => 'Maintaining the highest standards of the accountancy profession.'],
            ['title' => 'Passion', 'description' => 'Driven by a genuine commitment to our members and community.'],
            ['title' => 'Impact', 'description' => 'Creating measurable, lasting change through our work.'],
            ['title' => 'Accountability', 'description' => 'Taking ownership and responsibility for our actions and outcomes.'],
        ];

        foreach ($values as $value) {
            CoreValue::query()->updateOrCreate(['title' => $value['title']], $value);
        }

        $imagesPath = base_path('../frontend/src/assets/images');

        $executives = [
            ['name' => 'Patricia Chinwe Ofili', 'credential' => 'ACA', 'position' => 'Chairperson', 'photo' => 'chairperson-cropped.jpeg'],
            ['name' => 'Dr Maryam Danna Mohammed', 'credential' => 'FCA', 'position' => 'Vice Chairperson', 'photo' => 'Vice-chair-cropped-1024x1024.jpeg'],
            ['name' => 'Ojoma Blessing Lawal-Adewale', 'credential' => 'ACA', 'position' => 'General Secretary', 'photo' => 'Gen-sec-cropped.jpeg'],
            ['name' => 'Biola Olawoore', 'credential' => 'FCA', 'position' => 'Treasurer', 'photo' => 'treasurer-cropped.jpeg'],
            ['name' => 'Ojoma Blessing Olaniran', 'credential' => 'FCA', 'position' => 'Financial Secretary', 'photo' => 'Fin-sec-cropped.jpeg'],
        ];

        foreach ($executives as $executive) {
            $photo = $executive['photo'];
            unset($executive['photo']);

            $isPrincipal = in_array($executive['position'], ['Chairperson', 'Vice Chairperson', 'General Secretary', 'Treasurer', 'Financial Secretary'], true);

            $member = ExecutiveMember::query()->updateOrCreate(
                ['name' => $executive['name']],
                $executive + ['is_active' => true, 'is_principal' => $isPrincipal]
            );

            $file = "{$imagesPath}/{$photo}";
            if (! $member->hasMedia('photo') && is_file($file)) {
                $member->addMedia($file)->preservingOriginal()->toMediaCollection('photo');
            }
        }

        $pastChairpersons = [
            ['name' => 'Funmilayo Adeyemi', 'credential' => 'FCA', 'position' => 'Chairperson', 'term_start_year' => 2022, 'term_end_year' => 2024],
            ['name' => 'Halima Bello-Osagie', 'credential' => 'ACA', 'position' => 'Chairperson', 'term_start_year' => 2020, 'term_end_year' => 2022],
        ];

        foreach ($pastChairpersons as $chairperson) {
            ExecutiveMember::query()->updateOrCreate(
                ['name' => $chairperson['name']],
                $chairperson + ['is_active' => false, 'is_principal' => false]
            );
        }

        $partners = [
            ['name' => 'ICAN', 'url' => 'https://icanig.org', 'scope' => 'Parent body'],
            ['name' => 'SWAN National', 'url' => '/about', 'scope' => 'Parent body'],
            ['name' => 'Association of Accountancy Bodies in West Africa', 'url' => 'https://abwa.org.ng/', 'scope' => 'Affiliate'],
            ['name' => 'Pan African Federation of Accountants', 'url' => 'https://www.pafa.org.za/', 'scope' => 'Affiliate'],
            ['name' => 'International Federation of Accountants', 'url' => 'https://www.ifac.org/', 'scope' => 'Affiliate'],
            ['name' => 'Chartered Accountants Worldwide', 'url' => 'https://charteredaccountantsworldwide.com/', 'scope' => 'Affiliate'],
        ];

        foreach ($partners as $partner) {
            Partner::query()->updateOrCreate(['name' => $partner['name']], $partner);
        }

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
                'image' => "{$imagesPath}/SWAN-17-1024x684.jpg",
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
                'image' => "{$imagesPath}/DSC06955-1.jpg",
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
                'image' => "{$imagesPath}/SWAN-ABUJA-PICNIC-085-1024x663.jpg",
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

        $this->seedNews($imagesPath);
        $this->seedAnnouncements();
        $this->seedProgramme();
        $this->seedCommittees();
        $this->seedFaqs();
        $this->seedTrainings();
        $this->seedFirms();
        $this->seedJobListings();
        $this->seedGallery($imagesPath);
        $this->seedMemberSpotlights($imagesPath);
        $this->seedPublicationsAndResources();
        $this->seedConstitution();
        $this->seedDirectoryMembers();
        $this->seedMemberPortalDemoData();
    }

    private function seedNews(string $imagesPath): void
    {
        $posts = [
            [
                'title' => 'SWAN Abuja hosts annual technical seminar on IFRS updates',
                'category' => 'Chapter',
                'author' => 'Publicity Committee',
                'excerpt' => 'Over 300 members attended the chapter\'s flagship technical seminar, covering recent updates to IFRS and their implications for public and private sector reporting.',
                'body' => [
                    'The Abuja Chapter of the Society of Women Accountants of Nigeria held its annual technical seminar at the Congress Hall, Transcorp Hilton, drawing over 300 members and guests from across the Federal Capital Territory.',
                    'Sessions covered recent updates to IFRS, practical case studies in public sector reporting, and a panel discussion on the future of the accountancy profession in Nigeria.',
                    'The seminar carried 7 hours of structured CPD credit for attendees who registered with their ICAN membership details.',
                ],
                'image' => 'DSC06955-1.jpg',
                'daysAgo' => 12,
            ],
            [
                'title' => 'Chapter welfare fund supports three members through medical emergencies',
                'category' => 'Chapter',
                'author' => 'Welfare Committee',
                'excerpt' => 'The chapter welfare fund disbursed support to three members facing medical emergencies this quarter, reaffirming the Society\'s commitment to member welfare.',
                'body' => [
                    'The Welfare Committee reported that the chapter welfare fund supported three members through medical emergencies in the past quarter, drawing on contributions from the annual welfare levy.',
                    'The Welfare Officer reminded members that the fund is accounted for in the annual statements presented at the general meeting, and encouraged members with current dues to reach out when they need support.',
                ],
                'image' => 'DSC05883-1024x684.jpg',
                'daysAgo' => 25,
            ],
            [
                'title' => 'ICAN releases 2026 continuing professional development guidelines',
                'category' => 'ICAN',
                'author' => 'Technical Committee',
                'excerpt' => 'The Institute of Chartered Accountants of Nigeria has released updated CPD guidelines for the 2026 cycle, with new provisions for verified virtual attendance.',
                'body' => [
                    'ICAN has released its updated Continuing Professional Development guidelines for the 2026 cycle, introducing new provisions for verified virtual attendance at accredited sessions.',
                    'The chapter\'s Technical Committee has reviewed the guidelines and confirmed that all SWAN Abuja technical sessions remain fully accredited under the new framework.',
                ],
                'image' => null,
                'daysAgo' => 40,
            ],
            [
                'title' => 'Chapter marks Kwali community outreach with medical screening exercise',
                'category' => 'Chapter',
                'author' => 'Outreach Committee',
                'excerpt' => 'Chapter members visited Kwali to conduct a free medical screening exercise for residents, part of the Society\'s ongoing community service commitments.',
                'body' => [
                    'Members of the Abuja Chapter travelled to Kwali Area Council for a free medical screening exercise, in partnership with local health volunteers.',
                    'The outreach forms part of the chapter\'s annual community service calendar, which also includes financial literacy sessions in public secondary schools across the FCT.',
                ],
                'image' => 'SWAN-KWALI-ORPHANAGE-22-1024x683.jpg',
                'daysAgo' => 55,
            ],
            [
                'title' => 'Council statement on the exposure draft on sustainability reporting',
                'category' => 'Advocacy',
                'author' => 'General Secretary',
                'excerpt' => 'The chapter council has submitted its position on the exposure draft on sustainability reporting standards through ICAN\'s Technical and Education directorate.',
                'body' => [
                    'The chapter council reviewed the exposure draft on sustainability reporting standards and submitted its comments through ICAN\'s Technical and Education directorate.',
                    'The submission emphasised the need for proportionate requirements for small and medium practices, an area the chapter\'s Women in Practice committee has raised repeatedly.',
                ],
                'image' => null,
                'daysAgo' => 70,
            ],
            [
                'title' => 'Five women, five careers: what the profession looks like from the FCT',
                'category' => 'Profession',
                'author' => 'Editorial Desk',
                'excerpt' => 'A feature on five chapter members working across public practice, industry, academia and the public sector, on what a career in accountancy has given them.',
                'body' => [
                    'This feature profiles five chapter members working across public practice, industry, academia, financial services and the public sector.',
                    'Each shares what drew them to the profession, the mentors who shaped their path, and the advice they would give a newly inducted member starting out today.',
                ],
                'image' => 'WhatsApp-Image-2026-07-06-at-16.15.25-967x1024.jpeg',
                'daysAgo' => 90,
            ],
        ];

        foreach ($posts as $post) {
            $image = $post['image'];
            $daysAgo = $post['daysAgo'];
            unset($post['image'], $post['daysAgo']);

            $post['body'] = collect($post['body'])->map(fn ($paragraph) => "<p>{$paragraph}</p>")->implode('');

            $newsPost = NewsPost::query()->updateOrCreate(
                ['slug' => Str::slug($post['title'])],
                $post + [
                    'slug' => Str::slug($post['title']),
                    'published_at' => now()->subDays($daysAgo),
                    'is_published' => true,
                ]
            );

            $file = $image ? "{$imagesPath}/{$image}" : null;
            if ($file && ! $newsPost->hasMedia('cover') && is_file($file)) {
                $newsPost->addMedia($file)->preservingOriginal()->toMediaCollection('cover');
            }
        }
    }

    private function seedAnnouncements(): void
    {
        $announcements = [
            ['title' => '2026 annual dues are now due', 'kind' => 'deadline', 'daysAgo' => 2],
            ['title' => 'Notice of the 2026 Annual General Meeting', 'kind' => 'notice', 'daysAgo' => 5],
            ['title' => 'Circular: updated CPD verification process', 'kind' => 'circular', 'daysAgo' => 10],
            ['title' => 'Call for nominations to chapter council', 'kind' => 'notice', 'daysAgo' => 18],
            ['title' => 'Deadline for practice licence renewal extended', 'kind' => 'deadline', 'daysAgo' => 22],
            ['title' => 'Circular: new bank details for subscription payment', 'kind' => 'circular', 'daysAgo' => 30],
        ];

        foreach ($announcements as $announcement) {
            Announcement::query()->updateOrCreate(
                ['title' => $announcement['title']],
                [
                    'title' => $announcement['title'],
                    'kind' => $announcement['kind'],
                    'published_on' => now()->subDays($announcement['daysAgo'])->toDateString(),
                    'href' => '/announcements',
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedProgramme(): void
    {
        $event = Event::query()->orderBy('starts_at')->first();

        $entries = [
            ['name' => 'Monthly technical clinic', 'venue' => 'Chapter Secretariat, Wuse II', 'daysFromNow' => 14],
            ['name' => 'Committee heads meeting', 'venue' => 'Chapter Secretariat, Wuse II', 'daysFromNow' => 21],
            ['name' => 'Mentorship circle: public sector track', 'venue' => 'Virtual', 'daysFromNow' => 35],
            ['name' => 'Quarterly welfare review meeting', 'venue' => 'Chapter Secretariat, Wuse II', 'daysFromNow' => 50],
        ];

        foreach ($entries as $entry) {
            $startsAt = now()->addDays($entry['daysFromNow']);

            ProgrammeEntry::query()->updateOrCreate(
                ['name' => $entry['name']],
                [
                    'name' => $entry['name'],
                    'date_label' => $startsAt->format('j F Y'),
                    'starts_at' => $startsAt,
                    'venue' => $entry['venue'],
                    'href' => $event ? "/events/{$event->slug}" : '/events',
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedCommittees(): void
    {
        $committees = [
            [
                'name' => 'Technical Committee',
                'chair' => 'Dr Maryam Danna Mohammed',
                'remit' => 'Sets the chapter\'s CPD calendar, reviews exposure drafts and coordinates the chapter\'s technical positions submitted through ICAN.',
                'focus_areas' => ['CPD programme', 'Exposure drafts', 'Technical bulletins'],
                'meeting_cadence' => 'Monthly',
            ],
            [
                'name' => 'Welfare Committee',
                'chair' => 'Ngozi Adebayo',
                'remit' => 'Administers the welfare fund, supporting members through bereavement, illness and other major life events.',
                'focus_areas' => ['Welfare fund', 'Member support', 'Annual welfare report'],
                'meeting_cadence' => 'Monthly',
            ],
            [
                'name' => 'Membership Committee',
                'chair' => 'Ojoma Blessing Lawal-Adewale',
                'remit' => 'Reviews new membership registrations, maintains the directory, and runs the induction process for newly admitted members.',
                'focus_areas' => ['Registration', 'Directory', 'Induction'],
                'meeting_cadence' => 'Monthly',
            ],
            [
                'name' => 'Mentorship and Career Committee',
                'chair' => 'Biola Olawoore',
                'remit' => 'Runs the chapter mentorship programme and the job centre, connecting members with senior professionals and career opportunities.',
                'focus_areas' => ['Mentorship pairing', 'Job centre', 'Student outreach'],
                'meeting_cadence' => 'Bi-monthly',
            ],
            [
                'name' => 'Women in Practice Committee',
                'chair' => 'Patricia Chinwe Ofili',
                'remit' => 'Supports members setting up or running licensed practices, through the licensing clinic and the practice attachment register.',
                'focus_areas' => ['Licensing clinic', 'Practice attachment', 'Licence renewal'],
                'meeting_cadence' => 'Quarterly',
            ],
            [
                'name' => 'Outreach and Publicity Committee',
                'chair' => 'Ojoma Blessing Olaniran',
                'remit' => 'Plans community outreach programmes and manages the chapter\'s public communications and media presence.',
                'focus_areas' => ['Community outreach', 'Media relations', 'Chapter publications'],
                'meeting_cadence' => 'Monthly',
            ],
            [
                'name' => 'Finance and Fundraising Committee',
                'chair' => 'Biola Olawoore',
                'remit' => 'Oversees chapter finances, prepares annual accounts, and coordinates fundraising for chapter programmes.',
                'focus_areas' => ['Annual accounts', 'Fundraising', 'Budget oversight'],
                'meeting_cadence' => 'Monthly',
            ],
            [
                'name' => 'Ethics and Disciplinary Committee',
                'chair' => 'Dr Maryam Danna Mohammed',
                'remit' => 'Handles conduct matters referred by council and advises members on professional ethics questions.',
                'focus_areas' => ['Conduct matters', 'Ethics advisory'],
                'meeting_cadence' => 'As required',
            ],
        ];

        foreach ($committees as $committee) {
            Committee::query()->updateOrCreate(
                ['name' => $committee['name']],
                $committee + [
                    'slug' => Str::slug($committee['name']),
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedFaqs(): void
    {
        $faqs = [
            ['topic' => 'Membership', 'question' => 'Who is eligible to join SWAN?', 'answer' => 'Every female member of the Institute of Chartered Accountants of Nigeria is automatically a member of the Society of Women Accountants of Nigeria. There is no separate admission examination.'],
            ['topic' => 'Membership', 'question' => 'How do I become an active member of the Abuja Chapter?', 'answer' => 'Pay your annual subscription and welfare levy, get confirmed by the Financial Secretary, and attend a scheduled chapter meeting. That completes your registration.'],
            ['topic' => 'Payments', 'question' => 'What are the annual dues?', 'answer' => 'Annual dues depend on your membership level — each level has its own subscription and welfare levy, shown when you register or renew. Card, bank transfer and USSD are all accepted through the portal.'],
            ['topic' => 'Payments', 'question' => 'How do I get a receipt for my payment?', 'answer' => 'Receipts are issued automatically once the Financial Secretary confirms your payment. You can also find your payment history under Subscription in the members area.'],
            ['topic' => 'Events', 'question' => 'How do member rates work at chapter events?', 'answer' => 'Sign in before you check out on any event page. If your membership is active, the member rate is applied automatically — usually about 40% off the public rate.'],
            ['topic' => 'Events', 'question' => 'Can I get a refund if I cannot attend an event I paid for?', 'answer' => 'Refund requests are handled by the Financial Secretary on a case-by-case basis. Contact the chapter at least 48 hours before the event.'],
            ['topic' => 'CPD', 'question' => 'How many CPD hours do I need?', 'answer' => 'The Institute requires 120 credit hours over three consecutive years. Chapter technical sessions are accredited, so hours earned here post directly to your ICAN record.'],
            ['topic' => 'CPD', 'question' => 'What counts as unstructured CPD?', 'answer' => 'Unstructured activity is professional reading, research and preparation you do to teach or mentor others. The Institute expects the greater share of your hours to be structured.'],
            ['topic' => 'General', 'question' => 'How do I contact the right office at the chapter?', 'answer' => 'Use the Contact page to route your message. The Financial Secretary handles dues and payments, the Membership Secretary handles registration, and the General Secretary handles everything else.'],
            ['topic' => 'General', 'question' => 'Is my information in the members directory public?', 'answer' => 'Only what you choose to show. Members opt in to the directory, and only name, sector and specialisation are published — contact details are never shown.'],
        ];

        foreach ($faqs as $faq) {
            Faq::query()->updateOrCreate(
                ['question' => $faq['question']],
                $faq + ['is_active' => true]
            );
        }
    }

    private function seedTrainings(): void
    {
        $trainings = [
            ['title' => 'IFRS 17 for Insurance Practitioners', 'provider' => 'ICAN MPD', 'delivery_mode' => 'Hybrid', 'days' => 20, 'cpd_hours' => 6],
            ['title' => 'Forensic Accounting and Fraud Investigation', 'provider' => 'SWAN Abuja', 'delivery_mode' => 'Physical', 'days' => 35, 'cpd_hours' => 7],
            ['title' => 'Public Sector Financial Reporting Clinic', 'provider' => 'Faculty', 'delivery_mode' => 'Virtual', 'days' => 50, 'cpd_hours' => 4],
            ['title' => 'Leadership for Women in Finance', 'provider' => 'SWAN Abuja', 'delivery_mode' => 'Physical', 'days' => 65, 'cpd_hours' => 5],
            ['title' => 'Tax Compliance Update Workshop', 'provider' => 'ICAN MPD', 'delivery_mode' => 'Hybrid', 'days' => 80, 'cpd_hours' => 6],
        ];

        foreach ($trainings as $training) {
            Training::query()->updateOrCreate(
                ['title' => $training['title']],
                [
                    'title' => $training['title'],
                    'provider' => $training['provider'],
                    'delivery_mode' => $training['delivery_mode'],
                    'starts_on' => now()->addDays($training['days']),
                    'cpd_hours' => $training['cpd_hours'],
                    'fee' => 45000,
                    'member_fee' => 25000,
                    'seats_available' => 40,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedFirms(): void
    {
        $firms = [
            ['name' => 'Ofili & Associates', 'principal' => 'Patricia Chinwe Ofili', 'area' => 'Maitama', 'services' => ['Audit', 'Tax']],
            ['name' => 'Danna Mohammed Chartered Accountants', 'principal' => 'Dr Maryam Danna Mohammed', 'area' => 'Wuse II', 'services' => ['Advisory', 'Forensic accounting']],
            ['name' => 'Lawal-Adewale & Co', 'principal' => 'Ojoma Blessing Lawal-Adewale', 'area' => 'Garki', 'services' => ['Audit', 'Advisory']],
            ['name' => 'Olawoore Professional Services', 'principal' => 'Biola Olawoore', 'area' => 'Jabi', 'services' => ['Tax', 'Forensic accounting']],
            ['name' => 'Olaniran & Partners', 'principal' => 'Ojoma Blessing Olaniran', 'area' => 'Central Business District', 'services' => ['Audit', 'Tax', 'Advisory']],
        ];

        foreach ($firms as $firm) {
            Firm::query()->updateOrCreate(
                ['name' => $firm['name']],
                $firm + [
                    'licence_number' => 'PL/'.fake()->unique()->numberBetween(1000, 9999).'/'.now()->year,
                    'licence_status' => 'Active',
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedJobListings(): void
    {
        $jobs = [
            ['title' => 'Financial Reporting Manager', 'organisation' => 'Federal Ministry of Finance', 'location' => 'Abuja', 'type' => 'Full-time', 'level' => 'Senior'],
            ['title' => 'Internal Auditor', 'organisation' => 'Union Bank of Nigeria', 'location' => 'Abuja', 'type' => 'Full-time', 'level' => 'Mid'],
            ['title' => 'Tax Associate', 'organisation' => 'KPMG Nigeria', 'location' => 'Abuja', 'type' => 'Full-time', 'level' => 'Entry'],
            ['title' => 'Finance Director', 'organisation' => 'Abuja Electricity Distribution Company', 'location' => 'Abuja', 'type' => 'Full-time', 'level' => 'Executive'],
            ['title' => 'Grants Accountant', 'organisation' => 'UNDP Nigeria', 'location' => 'Abuja', 'type' => 'Contract', 'level' => 'Mid'],
        ];

        foreach ($jobs as $index => $job) {
            JobListing::query()->updateOrCreate(
                ['title' => $job['title'], 'organisation' => $job['organisation']],
                [
                    'title' => $job['title'],
                    'organisation' => $job['organisation'],
                    'location' => $job['location'],
                    'employment_type' => $job['type'],
                    'seniority_level' => $job['level'],
                    'posted_at' => now()->subDays($index * 3),
                    'closes_at' => now()->addDays(30 - $index * 2),
                    'summary' => 'Shared with the chapter by a member organisation. See the application link for full requirements and how to apply.',
                    'application_url' => null,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedGallery(string $imagesPath): void
    {
        $images = [
            ['caption' => 'Delegates at the annual technical seminar', 'album' => 'Seminars', 'file' => 'DSC06955-1.jpg'],
            ['caption' => 'Panel discussion on the future of the profession', 'album' => 'Seminars', 'file' => 'DSC05883-1024x684.jpg'],
            ['caption' => 'Medical screening day in Kwali', 'album' => 'Community outreach', 'file' => 'SWAN-KWALI-ORPHANAGE-22-1024x683.jpg'],
            ['caption' => 'Chapter picnic', 'album' => 'Chapter life', 'file' => 'SWAN-ABUJA-PICNIC-085-1024x663.jpg'],
            ['caption' => 'Members at the general meeting', 'album' => 'Chapter life', 'file' => 'SWAN-17-1024x684.jpg'],
            ['caption' => 'Newly inducted members at chapter induction', 'album' => 'Chapter life', 'file' => 'WhatsApp-Image-2026-07-06-at-16.15.25-967x1024.jpeg'],
        ];

        foreach ($images as $image) {
            $galleryImage = GalleryImage::query()->updateOrCreate(
                ['caption' => $image['caption']],
                [
                    'caption' => $image['caption'],
                    'album' => $image['album'],
                    'year' => now()->year,
                ]
            );

            $file = "{$imagesPath}/{$image['file']}";
            if (! $galleryImage->hasMedia('image') && is_file($file)) {
                $galleryImage->addMedia($file)->preservingOriginal()->toMediaCollection('image');
            }
        }
    }

    private function seedMemberSpotlights(string $imagesPath): void
    {
        $spotlights = [
            ['name' => 'Amaka Eze', 'quote' => 'SWAN gave me a technical community I could not find anywhere else in my first years as a chartered accountant.'],
            ['name' => 'Halima Suleiman', 'quote' => 'The mentorship programme paired me with a woman who had already done what I was trying to do. That changed everything.'],
            ['name' => 'Blessing Okoro', 'quote' => 'Serving on the Welfare Committee taught me as much about leadership as any course I have taken.'],
        ];

        $photo = "{$imagesPath}/gray-female-avatar-placeholder-nobg.png";

        foreach ($spotlights as $spotlight) {
            $memberSpotlight = MemberSpotlight::query()->updateOrCreate(
                ['name' => $spotlight['name']],
                $spotlight
            );

            if (! $memberSpotlight->hasMedia('photo') && is_file($photo)) {
                $memberSpotlight->addMedia($photo)->preservingOriginal()->toMediaCollection('photo');
            }
        }
    }

    private function seedPublicationsAndResources(): void
    {
        $pdf = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Resources<</Font<</F1 4 0 R>>>>/Contents 5 0 R>>endobj\n4 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj\n5 0 obj<</Length 58>>stream\nBT /F1 20 Tf 72 700 Td (SWAN Abuja Chapter - Sample document) Tj ET\nendstream\nendobj\ntrailer<</Size 6/Root 1 0 R>>\n%%EOF";

        $publications = [
            ['title' => '2025 Annual Report', 'category' => 'Report'],
            ['title' => 'Q3 2026 Chapter Newsletter', 'category' => 'Newsletter'],
            ['title' => 'Communiqué: Annual Technical Seminar', 'category' => 'Communiqué'],
            ['title' => 'Chairperson\'s Address at Induction', 'category' => 'Address'],
            ['title' => 'Technical Bulletin: IFRS 17 Adoption', 'category' => 'Technical'],
        ];

        foreach ($publications as $index => $publication) {
            $model = Publication::query()->updateOrCreate(
                ['title' => $publication['title']],
                $publication + ['published_at' => now()->subDays($index * 15)]
            );

            if (! $model->hasMedia('file')) {
                $model->addMediaFromString($pdf)
                    ->usingFileName(Str::slug($publication['title']).'.pdf')
                    ->toMediaCollection('file');
            }
        }

        $resources = [
            ['title' => 'Membership Registration Form', 'category' => 'Form', 'format' => 'PDF'],
            ['title' => 'Practice Licence Application Guide', 'category' => 'Guide', 'format' => 'PDF'],
            ['title' => 'Chapter Welfare Policy', 'category' => 'Policy', 'format' => 'PDF'],
            ['title' => 'Event Sponsorship Template', 'category' => 'Template', 'format' => 'DOCX'],
            ['title' => 'CPD Self-Declaration Form', 'category' => 'Form', 'format' => 'XLSX'],
        ];

        foreach ($resources as $resource) {
            $model = ResourceItem::query()->updateOrCreate(
                ['title' => $resource['title']],
                $resource + ['description' => 'Download the current version of this document.', 'is_active' => true]
            );

            if (! $model->hasMedia('file')) {
                $model->addMediaFromString($pdf)
                    ->usingFileName(Str::slug($resource['title']).'.pdf')
                    ->toMediaCollection('file');
            }
        }
    }

    private function seedConstitution(): void
    {
        $setting = SiteSetting::current();

        $setting->update(['constitution_label' => 'Adopted 2024']);

        if ($setting->hasMedia('constitution')) {
            return;
        }

        $text = 'SWAN Abuja Chapter - Constitution (placeholder text pending the adopted document)';
        $stream = "BT /F1 14 Tf 72 720 Td ({$text}) Tj ET";

        $pdf = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Resources<</Font<</F1 4 0 R>>>>/Contents 5 0 R>>endobj\n4 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj\n5 0 obj<</Length ".strlen($stream).">>stream\n{$stream}\nendstream\nendobj\ntrailer<</Size 6/Root 1 0 R>>\n%%EOF";

        $setting->addMediaFromString($pdf)
            ->usingFileName('swan-abuja-chapter-constitution.pdf')
            ->toMediaCollection('constitution');
    }

    private function seedDirectoryMembers(): void
    {
        $members = [
            ['name' => 'Amaka Eze', 'sector' => 'Public practice', 'specialisation' => 'External audit', 'year' => 2016],
            ['name' => 'Halima Suleiman', 'sector' => 'Financial services', 'specialisation' => 'Risk management', 'year' => 2014],
            ['name' => 'Blessing Okoro', 'sector' => 'Public sector', 'specialisation' => 'Public financial management', 'year' => 2018],
            ['name' => 'Funmilayo Adeyemi', 'sector' => 'Industry', 'specialisation' => 'Management accounting', 'year' => 2012],
            ['name' => 'Chidinma Nwosu', 'sector' => 'Academia', 'specialisation' => 'Accounting education', 'year' => 2010],
            ['name' => 'Fatima Bello', 'sector' => 'Consulting', 'specialisation' => 'Tax advisory', 'year' => 2019],
        ];

        foreach ($members as $index => $member) {
            $email = Str::slug($member['name']).'@example.com';

            $user = User::query()->updateOrCreate(
                ['email' => $email],
                ['name' => $member['name'], 'password' => bcrypt('password'), 'role' => 'member']
            );

            $user->memberProfile()->updateOrCreate([], [
                'membership_number' => 'ICAN/'.str_pad((string) (20000 + $index), 6, '0', STR_PAD_LEFT),
                'credential' => $index % 2 === 0 ? 'ACA' : 'FCA',
                'membership_status' => 'active',
                'sector' => $member['sector'],
                'specialisation' => $member['specialisation'],
                'year_admitted' => $member['year'],
                'cpd_target' => 120,
                'is_directory_listed' => true,
                'joined_at' => now()->subYears(now()->year - $member['year']),
            ]);
        }
    }

    private function seedMemberPortalDemoData(): void
    {
        $member = User::where('email', 'testmember@example.com')->first();

        if (! $member) {
            return;
        }

        $member->memberProfile()->updateOrCreate([], [
            'membership_number' => 'ICAN/020199',
            'credential' => 'ACA',
            'membership_status' => 'active',
            'sector' => 'Public practice',
            'specialisation' => 'External audit',
            'year_admitted' => 2017,
            'cpd_target' => 120,
            'is_directory_listed' => true,
            'joined_at' => now()->subYears(3),
        ]);

        $cpdRecords = [
            ['activity' => 'SWAN Abuja annual technical seminar', 'type' => 'Structured', 'hours' => 7, 'daysAgo' => 12, 'verified' => true],
            ['activity' => 'ICAN MPD tax compliance workshop', 'type' => 'Structured', 'hours' => 6, 'daysAgo' => 60, 'verified' => true],
            ['activity' => 'Technical reading and research', 'type' => 'Unstructured', 'hours' => 4, 'daysAgo' => 90, 'verified' => false],
            ['activity' => 'Mentorship session facilitation', 'type' => 'Unstructured', 'hours' => 3, 'daysAgo' => 150, 'verified' => false],
        ];

        foreach ($cpdRecords as $record) {
            CpdRecord::query()->updateOrCreate(
                ['user_id' => $member->id, 'activity' => $record['activity']],
                [
                    'activity_date' => now()->subDays($record['daysAgo']),
                    'hours' => $record['hours'],
                    'activity_type' => $record['type'],
                    'is_verified' => $record['verified'],
                ]
            );
        }

        Subscription::query()->updateOrCreate(
            ['user_id' => $member->id, 'year' => now()->year - 1],
            [
                'subscription_amount' => 5000,
                'welfare_amount' => 12000,
                'status' => 'paid',
                'paid_at' => now()->subMonths(10),
                'reference' => 'SUB-'.strtoupper(Str::random(8)),
            ]
        );

        Subscription::query()->updateOrCreate(
            ['user_id' => $member->id, 'year' => now()->year],
            [
                'subscription_amount' => 5000,
                'welfare_amount' => 12000,
                'status' => 'outstanding',
                'paid_at' => null,
                'reference' => null,
            ]
        );

        $event = Event::query()->orderBy('starts_at')->first();
        if ($event && $ticketType = $event->ticketTypes()->first()) {
            $event->registrations()->updateOrCreate(
                ['user_id' => $member->id, 'event_ticket_type_id' => $ticketType->id],
                [
                    'name' => $member->name,
                    'email' => $member->email,
                    'amount' => $ticketType->price,
                    'payment_status' => 'paid',
                    'reference' => 'TKT-'.strtoupper(Str::random(10)),
                    'issued_at' => now()->subDays(5),
                ]
            );
        }
    }
}
