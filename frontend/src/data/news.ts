import type { Announcement, NewsPost, ProgrammeEntry } from '@/types'
import {
  annualSeminarImage,
  chapterPicnicImage,
  conferenceImage,
  generalMeetingImage,
  orphanageOutreachImage,
} from '@/assets/images'

export const news: NewsPost[] = [
  {
    id: 'news-tax-communique',
    title: 'Chapter issues communiqué on the new tax reform',
    slug: 'communique-new-tax-reform',
    excerpt:
      'Following the May seminar, SWAN Abuja has published its position on transitional relief, filing timelines and the advisory obligations the reform places on practitioners.',
    body: [
      'The communiqué, adopted at the close of the May technical seminar, sets out five recommendations from the chapter to the revenue authorities and to members in practice.',
      'The first concerns the transitional window. Members reported that the current timetable leaves preparers with overlapping obligations in the same quarter, and the chapter has asked for clarification on which basis applies where a financial year straddles the commencement date.',
      'The remaining recommendations deal with documentation standards for reliefs claimed, the treatment of small companies below the turnover threshold, and the professional judgement expected of an accountant advising on structures whose principal effect is a reduction in liability.',
      'The full text has been forwarded to the ICAN Technical and Education directorate and is available in the publications library.',
    ],
    category: 'Advocacy',
    publishedAt: '2026-06-04',
    author: 'Technical Committee',
    coverUrl: annualSeminarImage,
  },
  {
    id: 'news-mentorship-cohort',
    title: 'Second mentorship cohort opens with forty pairings',
    slug: 'second-mentorship-cohort-opens',
    excerpt:
      'Forty newly inducted members have been matched with senior members across practice, the public sector and financial services for the twelve-month programme.',
    body: [
      'The chapter mentorship programme has doubled in size since its first cohort. Pairings are made on sector and specialisation rather than seniority alone, and each pair sets its own meeting rhythm within a light structure the chapter provides.',
      'Mentors commit to six documented sessions across the year. The Membership Secretary reviews progress at the midpoint and rematches where a pairing has not taken.',
      'Applications for the third cohort open in January.',
    ],
    category: 'Chapter',
    publishedAt: '2026-05-19',
    author: 'Nsini Bassey, FCA',
    coverUrl: chapterPicnicImage,
  },
  {
    id: 'news-ican-conference',
    title: '56th Annual Accountants’ Conference comes to Abuja',
    slug: 'annual-accountants-conference-abuja',
    excerpt:
      'ICAN holds its 56th Annual Accountants’ Conference in Abuja from 18 to 23 October. The chapter is coordinating accommodation and a members’ reception.',
    body: [
      'With the conference in the Federal Capital Territory this year, SWAN Abuja is coordinating a block booking at the discounted delegate rate and hosting a reception for female delegates on the second evening.',
      'Members intending to attend should indicate through the portal by the end of September so the chapter can confirm numbers.',
    ],
    category: 'ICAN',
    publishedAt: '2026-04-30',
    author: 'Publicity Committee',
    coverUrl: conferenceImage,
  },
  {
    id: 'news-women-in-practice',
    title: 'Women in practice: chapter survey finds licensing the main barrier',
    slug: 'women-in-practice-survey',
    excerpt:
      'A chapter survey of 180 members finds that the practice licence route, rather than technical confidence, is where most women stall on the way to founding a firm.',
    body: [
      'Respondents were near-unanimous on technical readiness. The obstacles they named were the practice attachment requirement, the cost of the licence in the first two years, and the absence of a clear route back after a career break.',
      'The chapter has taken three actions in response: a standing licensing clinic, a directory of members willing to host practice attachments, and a submission to the Professional Practice directorate on re-entry after a break in service.',
    ],
    category: 'Profession',
    publishedAt: '2026-03-12',
    author: 'Technical Committee',
    coverUrl: generalMeetingImage,
  },
  {
    id: 'news-outreach-report',
    title: 'Outreach report: what the welfare levy paid for this year',
    slug: 'outreach-report-welfare-levy',
    excerpt:
      'Two community visits, a medical screening day, and two secondary school placements. The chapter publishes the outreach account in full.',
    body: [
      'The chapter publishes outreach spending annually because members fund it directly through the welfare levy.',
      'This year the levy covered the Kwali visit, the Bwari medical screening day, provisions for both, and two full secondary school placements including fees, uniforms and materials.',
      'The detailed statement forms part of the annual accounts presented at the general meeting.',
    ],
    category: 'Chapter',
    publishedAt: '2026-02-28',
    author: 'Taiye Fasan, ACA',
    coverUrl: orphanageOutreachImage,
  },
]

export const announcements: Announcement[] = [
  {
    id: 'ann-subs-2027',
    title: '2027 subscription and welfare levy — payment window opens 1 November',
    date: '2026-08-28',
    href: '/members/subscription',
    kind: 'deadline',
  },
  {
    id: 'ann-cpd-hours',
    title: 'Reminder: CPD requirement is 120 credit hours over three consecutive years',
    date: '2026-08-14',
    href: '/cpd',
    kind: 'notice',
  },
  {
    id: 'ann-conference-hotels',
    title: 'Discounted hotel rates for the 56th Annual Accountants’ Conference',
    date: '2026-08-02',
    href: '/news/annual-accountants-conference-abuja',
    kind: 'circular',
  },
  {
    id: 'ann-elections',
    title: 'Notice of chapter elections and call for nominations',
    date: '2026-07-21',
    href: '/governance',
    kind: 'notice',
  },
  {
    id: 'ann-licence-renewal',
    title: 'Practice licence renewal — procedure and closing date',
    date: '2026-07-09',
    href: '/practice',
    kind: 'deadline',
  },
  {
    id: 'ann-phishing',
    title: 'Security notice: phishing messages circulating in the name of the chapter',
    date: '2026-06-18',
    href: '/announcements',
    kind: 'notice',
  },
  {
    id: 'ann-directory-update',
    title: 'Members directory refresh — confirm your entry before 30 September',
    date: '2026-06-05',
    href: '/directory',
    kind: 'deadline',
  },
]

export const programme: ProgrammeEntry[] = [
  {
    id: 'prog-mentorship',
    name: 'Mentorship clinic for newly inducted members',
    date: '27 September 2026',
    venue: 'ICAN Abuja Liaison Office, Wuse II',
    href: '/events/mentorship-clinic-newly-inducted',
  },
  {
    id: 'prog-seminar',
    name: 'Annual technical seminar — sustainability reporting',
    date: '9 October 2026',
    venue: 'Transcorp Hilton, Maitama',
    href: '/events/annual-technical-seminar-2026',
  },
  {
    id: 'prog-aac',
    name: '56th Annual Accountants’ Conference',
    date: '18 – 23 October 2026',
    venue: 'Abuja',
    href: '/news/annual-accountants-conference-abuja',
  },
  {
    id: 'prog-gm',
    name: 'October general meeting',
    date: '25 October 2026',
    venue: 'ICAN Abuja Liaison Office, Wuse II',
    href: '/events/october-general-meeting',
  },
]

export function findNews(slug: string): NewsPost | undefined {
  return news.find((n) => n.slug === slug)
}
