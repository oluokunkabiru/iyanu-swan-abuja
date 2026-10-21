<?php

namespace Database\Seeders;

use App\Models\Committee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * The chapter's standing committees, transcribed from the
 * "Committees Mandate" document. Any committee not listed here is
 * removed so the site matches the mandate exactly.
 */
class CommitteeMandateSeeder extends Seeder
{
    public function run(): void
    {
        $slugs = [];

        foreach ($this->mandates() as $mandate) {
            $slug = Str::slug($mandate['name']);
            $slugs[] = $slug;

            Committee::query()->updateOrCreate(
                ['slug' => $slug],
                $mandate + ['subtitle' => null, 'note' => null, 'chair' => null, 'is_active' => true],
            );
        }

        Committee::query()->whereNotIn('slug', $slugs)->delete();
    }

    /** @return array<int, array<string, mixed>> */
    private function mandates(): array
    {
        return [
            [
                'name' => 'Health Committee',
                'purpose' => 'The Health or Medical Outreach Committee promotes members\' physical, mental, and emotional well-being and the broader community through coordinated medical initiatives, health education, and outreach programs.',
                'objectives' => [
                    'To plan, organise, and implement periodic health and medical outreach programs, including screenings and general wellness services.',
                    'To raise awareness on critical health issues through seminars, webinars, and information campaigns.',
                    'Foster partnerships with healthcare providers, government agencies, NGOs, and community leaders to ensure the success of outreach initiatives.',
                    'To mobilise medical and non-medical volunteers to participate in outreach activities.',
                    'To ensure proper documentation, evaluation, and follow-up on all outreach programs to assess impact and improve future initiatives.',
                    'To support members during medical emergencies or health crises by offering advice, referral, and possible intervention where necessary.',
                    'To advise the executive body on health-related matters affecting the organisation or community.',
                ],
                'structure' => 'The committee shall consist of medical professionals who are members of SWAN and other interested individuals dedicated to community health. It shall be chaired by a qualified health professional or a suitably competent member as deemed appropriate.',
                'meetings' => 'The committee shall meet regularly (monthly or quarterly) and as needed to ensure the timely planning and execution of its mandate.',
                'reporting' => 'The committee shall report its activities, outcomes, and challenges to the Exco and the members at every meeting.',
            ],
            [
                'name' => 'Women\'s Empowerment Committee',
                'purpose' => 'The Women Empowerment Committee has been established to promote women\'s social, economic, professional, and personal advancement (Members and others) through targeted programs, advocacy, capacity-building initiatives, and community engagement.',
                'objectives' => [
                    'To design programs empowering women with knowledge, skills, and resources to excel in careers, businesses, and personal lives.',
                    'To organise training, mentorship, and networking events that build confidence, leadership, and financial independence.',
                    'To support women-owned businesses and promote entrepreneurship via grants, exhibitions, and platforms.',
                    'To collaborate with the government, NGOs, development agencies, and corporate partners on women-focused initiatives.',
                    'To celebrate outstanding women role models in society and the community.',
                    'To implement outreach programs to uplift vulnerable women, especially in rural or underserved areas.',
                    'To offer support systems, including health awareness, legal aid, and psychological services.',
                ],
                'structure' => 'The committee shall consist of dynamic and passionate members committed to gender inclusion and female empowerment. Its chair shall be a member with a strong track record in advocacy, development work, or women-focused programming.',
                'meetings' => 'The committee shall meet regularly (monthly or quarterly) to plan and execute its mandate effectively, and as necessary, to respond to emerging opportunities or issues.',
                'reporting' => 'The committee shall provide periodic updates to the Exco and/or the general house at monthly meetings, including reports on activities, impact assessments, and plans.',
            ],
            [
                'name' => 'Orphanage Committee',
                'purpose' => 'The Orphanage Committee extends compassion, care, and support to orphans and vulnerable children through organised visits, donations, mentoring, and long-term empowerment initiatives, aligning with the organisation\'s vision of social responsibility and community impact.',
                'objectives' => [
                    'Identify, assess, and build relationships with registered orphanages needing support.',
                    'Organise regular visits for material, financial, and emotional aid.',
                    'Lead fundraising, donation drives, and sponsorships to address the needs of orphaned children.',
                    'Mobilise members and partners to contribute time, expertise, and resources.',
                    'Ensure transparency and accountability in distributing resources.',
                    'Document outreach activities, assess impact, and suggest strategies for ongoing support.',
                    'Collaborate with government and child welfare agencies to improve impact and compliance.',
                ],
                'structure' => 'The committee shall consist of passionate, empathetic, and service-driven individuals committed to child welfare and humanitarian service. Its chair shall be a member with demonstrated compassion, organisational ability, and integrity.',
                'meetings' => 'The committee shall meet regularly to plan upcoming visits and activities, review outcomes of previous outreaches, and discuss new opportunities for engagement and partnership.',
                'reporting' => 'The committee shall submit detailed reports to the executive committee after each outreach or program, including activity summaries, expenditures, photographs, and testimonials where appropriate.',
            ],
            [
                'name' => 'Annual Accountants Conference Table Committee',
                'purpose' => 'The Annual Accountants Conference Table Committee is established to ensure the effective coordination, reservation, and management of designated seating arrangements, branding, and representation at the Annual Accountants Conference. The committee shall work to promote order, visibility, and a strong presence of the organisation during the event.',
                'objectives' => [
                    'To secure and manage official table bookings for the organisation at the Annual Accountants Conference.',
                    'To coordinate the branding, decoration, and presentation of the organisation\'s table(s) in line with the theme and professional standards of the event.',
                    'Maintain accurate records of registered attendees at the table and coordinate with the event organisers on updates or changes.',
                    'To promote unity, excellence, and representation of the organisation through well-coordinated dress codes, table gifts, and conduct.',
                    'To liaise with the welfare and protocol teams to support members.',
                    'To evaluate participation and recommend improved coordination at subsequent conferences.',
                ],
                'structure' => 'The committee shall consist of dependable, organised, and detail-oriented members with experience in event coordination or conference participation. A member with strong planning and communication skills shall chair it.',
                'meetings' => 'The committee shall meet as necessary in the weeks before the conference to ensure adequate preparation and afterwards for debriefing and reporting.',
                'reporting' => 'The committee shall furnish the executive committee with reports concerning the budget, outcomes, participation status, logistical arrangements, and post-event evaluations. Additionally, it shall present a report to the general house at the subsequent monthly meeting following the AAC.',
            ],
            [
                'name' => 'Picnic Committee',
                'purpose' => 'The Picnic Committee is established to plan, organise, and execute fun, engaging, and well-coordinated picnic events that promote relaxation, bonding, and social interaction among members.',
                'objectives' => [
                    'Plan and coordinate all logistics for the society\'s picnic, including the date, venue, transportation, food, activities, and entertainment.',
                    'To create an inclusive and enjoyable atmosphere that fosters camaraderie, stress relief, and social networking among members.',
                    'To develop and execute a well-structured program of activities, including games, competitions, music, and other recreational engagements.',
                    'To handle all vendor and supplier arrangements related to catering, venue setup, rentals, security, and medical support.',
                    'To ensure safety, orderliness, and conduct throughout the event.',
                    'To publicise the picnic within the society and encourage active participation by all members.',
                    'To manage the picnic budget efficiently and provide accountability for funds disbursed.',
                    'To document the event through photos, videos, and reports for record-keeping and post-event evaluation.',
                ],
                'structure' => 'The committee shall consist of enthusiastic, creative, and organised members passionate about social engagement and event planning. The executive shall appoint the Chairperson and shall lead in coordination and decision-making.',
                'meetings' => 'The committee shall hold regular meetings leading up to the event to ensure timely planning, effective communication, and seamless execution.',
                'reporting' => 'The committee shall submit detailed progress updates before the picnic and a comprehensive report (including financials and attendance feedback) after the event to the executive council.',
            ],
            [
                'name' => 'Seminar Committee',
                'purpose' => 'The Seminar Committee is established to plan, coordinate, and deliver impactful seminars, workshops, and knowledge-sharing sessions that enhance members\' personal, professional, and intellectual development and the wider audience.',
                'objectives' => [
                    'To identify relevant and timely topics aligned with the Society\'s goals, values, and strategic interests.',
                    'Organise seminars promoting learning, professional growth, and capacity building.',
                    'To source and engage competent resource persons, facilitators, and guest speakers with expertise in the chosen subject areas.',
                    'To handle logistics related to venue, virtual platforms, publicity, registration, materials, and participant engagement.',
                    'To foster collaborations with institutions, industry experts, and partner organisations for enriched content and greater reach.',
                    'To ensure high-quality program delivery, active participation, and effective knowledge transfer during seminars.',
                    'To evaluate each seminar\'s effectiveness through participant feedback and recommend improvements for future events.',
                    'To maintain records, documentation, and reports of all seminar activities for accountability and strategic planning.',
                ],
                'structure' => 'The committee shall consist of proactive, knowledgeable, and committed individuals passionate about education and capacity building. The Chairperson shall lead coordination efforts and report directly to the executive body.',
                'meetings' => 'The committee shall meet as often as necessary, particularly during the seminar\'s planning stages, to ensure that all aspects of the program are addressed.',
                'reporting' => 'The committee shall provide timely updates and submit a comprehensive report after each seminar, including attendance data, feedback analysis, challenges faced, and recommendations.',
            ],
            [
                'name' => 'End-of-Year Events Committee',
                'subtitle' => 'Muslim and Christian Thanksgiving',
                'purpose' => 'The End-of-Year Events Committee plans and coordinates all activities related to the organisation\'s annual end-of-year celebrations, focusing on inclusive Thanksgiving events for Muslim and Christian members. The committee aims to promote unity, gratitude, reflection, and fellowship as the year ends.',
                'objectives' => [
                    'To plan and organise an inclusive and well-coordinated end-of-year event incorporating Muslim and Christian Thanksgiving elements.',
                    'To ensure spiritual, cultural, and social balance in the program, so that members of both faiths can express gratitude according to their beliefs.',
                    'To handle logistics, including venue selection, catering, sound, and general event coordination.',
                    'To coordinate the participation of clerics, speakers, or spiritual leaders from both faiths as necessary.',
                    'To foster unity, mutual respect, and a sense of belonging among all members, regardless of religious background.',
                    'To manage fundraising or donations (where applicable) and ensure transparency and accountability in the use of resources.',
                    'To capture the event through appropriate documentation (photos/videos) and provide a comprehensive report including feedback and recommendations.',
                ],
                'structure' => 'The committee shall consist of members from both faiths and reflect inclusivity, respect, and commitment to excellence. It shall be chaired or co-chaired by individuals known for fairness, event coordination skills, and good interpersonal relations.',
                'meetings' => 'The committee shall meet, especially in the weeks before the event, to ensure effective planning, coordination, and execution.',
                'reporting' => 'The committee shall submit periodic updates to the executive leadership and present a final report detailing attendance, outcomes, finances, and areas for improvement.',
            ],
            [
                'name' => 'Catch Them Young Committee',
                'subtitle' => 'Early Starters Initiative — in partnership with relevant agencies and sponsors',
                'purpose' => 'The Early Starters Initiative Committee has been established to design and implement quiz competitions to discover, nurture, and inspire aspiring secondary school or university accountants. The committee endeavours to promote early interest in accounting, taxation, and financial literacy through strategic partnerships with other agencies.',
                'objectives' => [
                    'To organise annual or periodic quiz competitions that target secondary school or university students within the FCT interested in accounting, commerce, and related subjects.',
                    'To promote accounting/tax awareness, financial discipline, and professional ethics among young learners as future accountants.',
                    'Collaborate with school Heads and other stakeholders to develop quiz content that integrates elements of accounting/taxation, financial responsibility, and career awareness.',
                    'To liaise with education boards, school authorities, and examination bodies to ensure broad participation and curriculum alignment.',
                    'To identify and reward outstanding students through prizes, sponsorship in ATS/ICAN examinations and mentorship opportunities.',
                    'To foster early engagement with the accounting profession and its role in nation-building.',
                    'To create a platform for schools and students to showcase academic excellence in finance-related subjects.',
                    'Ensure effective planning, logistics, sponsorship coordination, and media coverage of all quiz events.',
                    'To document each edition of the quiz competition, assess its impact, and recommend future improvements.',
                ],
                'structure' => 'The committee shall consist of dedicated educationists, professional accountants, and members interested in youth development and education. It shall collaborate closely with agency representatives and be chaired by an individual with substantial experience in project coordination who demonstrates passion.',
                'meetings' => 'The committee shall meet regularly — especially during the quiz competition\'s planning and execution phases — to ensure that all activities are on track.',
                'reporting' => 'The committee shall submit periodic progress reports and a final post-event report to the executive body and FIRS, outlining participation statistics, financials, feedback, and recommendations.',
            ],
            [
                'name' => 'Award Committee',
                'subtitle' => 'End of Tenure Awards',
                'purpose' => 'The End of Tenure Award Committee is established to identify, evaluate, and recognise individuals or groups whose contributions, service, or support have significantly impacted the organisation during a given tenure. The awards promote a culture of appreciation, excellence, and institutional memory.',
                'objectives' => [
                    'To plan and coordinate all award-related activities for the end-of-tenure celebration.',
                    'To design fair and transparent criteria for nominating and selecting award recipients across various categories (e.g., leadership, volunteerism, innovation, loyalty, partnership, etc.).',
                    'To compile and review nominations or recommendations submitted by members, committees, or executive leadership.',
                    'To liaise with relevant committees or external parties in the procurement of award plaques, certificates, and souvenirs.',
                    'To ensure that the selection and presentation of awards reflect integrity, equity, and inclusiveness.',
                    'Work closely with the planning committee to ensure the award segment is well-coordinated, timely, and impactful.',
                    'To document all awardees and their citations for organisational records and future reference.',
                    'To recommend strategies for institutionalising award recognition in future tenures.',
                ],
                'structure' => 'The committee shall comprise members of proven integrity, sound judgment, and objectivity. It shall be chaired by a senior or respected SWAN member who is not a direct beneficiary of the awards being considered.',
                'meetings' => 'The committee shall meet weeks before the award ceremony date to review nominations, finalise award categories, and coordinate logistics for the award presentation.',
                'reporting' => 'The committee shall provide a comprehensive report to the executive leadership, including the list of awardees, justification for selections, budget utilised, and recommendations for future improvement.',
            ],
            [
                'name' => 'Correctional Facility Committee',
                'purpose' => 'The Correctional Facility Committee is established to extend humanitarian, emotional, spiritual, and rehabilitative support to inmates in correctional centres. It is focused on addressing the unique needs and challenges of female inmates. It serves as a bridge of compassion, advocacy, and empowerment for incarcerated individuals, especially women, to foster dignity, hope, and reintegration into society.',
                'objectives' => [
                    'To organise regular outreach programs to correctional facilities, prioritising visits to female wards or women\'s correctional centres.',
                    'To provide essential supplies, including toiletries, sanitary products, clothing, food items, and other welfare materials, particularly needed by female inmates.',
                    'To offer spiritual, psychological, and emotional support through prayer sessions, counselling, mentorship, and life-skills training.',
                    'To collaborate with correctional facility authorities and relevant agencies to support educational programs for female inmates.',
                    'To design and facilitate empowerment workshops that promote personal development, literacy, vocational skills, and reintegration readiness.',
                    'To mobilise members, partners, and stakeholders to contribute meaningfully toward the upliftment of incarcerated women.',
                    'Document and report all outreach activities, including needs assessments, feedback from authorities, and measurable impact.',
                ],
                'structure' => 'The committee shall consist of members who are compassionate, empathetic, and committed to social justice, especially for vulnerable women. A member with demonstrated experience or interest in community service, prison ministry, or gender advocacy shall chair it.',
                'meetings' => 'The committee shall meet regularly to plan outreach activities, coordinate budget and resources, and assess ongoing needs and opportunities for support.',
                'reporting' => 'The committee shall submit detailed reports of its activities to the executive body, including budgets, outreach outcomes, challenges, and recommendations for sustainability and growth.',
                'note' => 'Nevertheless, the general house shall decide whether or not to include male inmates if necessary.',
            ],
        ];
    }
}
