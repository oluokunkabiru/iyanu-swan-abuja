import type { ChapterEvent, TicketTier } from '@/types'
import {
  annualSeminarImage,
  chapterPicnicImage,
  conferenceImage,
  medicalOutreachImage,
  orphanageOutreachImage,
} from '@/assets/images'

/** The four-tier structure the chapter uses for paid technical events. */
export const standardTiers: TicketTier[] = [
  {
    id: 'tier-member-physical',
    audience: 'member',
    mode: 'physical',
    label: 'Member — attending in person',
    price: 30000,
    includes: [
      'Full-day attendance',
      'CPD credit logged to your ICAN record',
      'Delegate pack and refreshments',
      'Access to the speaker materials',
    ],
  },
  {
    id: 'tier-member-virtual',
    audience: 'member',
    mode: 'virtual',
    label: 'Member — attending online',
    price: 15000,
    includes: [
      'Live stream access',
      'CPD credit logged to your ICAN record',
      'Digital delegate pack',
      'Session recording for 30 days',
    ],
  },
  {
    id: 'tier-nonmember-physical',
    audience: 'non-member',
    mode: 'physical',
    label: 'Non-member — attending in person',
    price: 50000,
    includes: [
      'Full-day attendance',
      'Certificate of attendance',
      'Delegate pack and refreshments',
      'Access to the speaker materials',
    ],
  },
  {
    id: 'tier-nonmember-virtual',
    audience: 'non-member',
    mode: 'virtual',
    label: 'Non-member — attending online',
    price: 15000,
    includes: [
      'Live stream access',
      'Certificate of attendance',
      'Digital delegate pack',
      'Session recording for 30 days',
    ],
  },
]

export const events: ChapterEvent[] = [
  {
    id: 'evt-tax-reform-2026',
    title: "Demystifying Nigeria's new tax reform: implications, opportunities, and the role of professional accountants",
    slug: 'demystifying-nigerias-new-tax-reform',
    summary:
      'The chapter seminar unpacking what the reform package changes for corporate reporting, compliance calendars and advisory practice.',
    body: [
      'The reform package reaches further than the headline rates. This session works through the compliance consequences line by line: what changes in the filing calendar, which reliefs survive, and where the transitional provisions leave a genuine planning decision rather than an administrative one.',
      'The afternoon block is built around worked examples brought by members in practice, so bring a live scenario if you have one. Speakers take questions on the record and the chapter publishes a communiqué afterwards.',
      'The session qualifies for structured CPD credit. Register with the ICAN details on your membership record so attendance posts correctly.',
    ],
    category: 'Seminar',
    venue: 'Chelsea Hotel, Central Business District, Abuja',
    startsAt: '2026-05-22T09:00:00+01:00',
    endsAt: '2026-05-22T16:30:00+01:00',
    cpdHours: 6,
    isFeatured: true,
    status: 'past',
    coverUrl: annualSeminarImage,
    ticketTiers: standardTiers,
    speakers: [
      { name: 'Dr Maryam Danna Mohammed, FCA', role: 'Session chair' },
      { name: 'Ngozi Francisca Ashinze, FCA', role: 'Technical lead' },
    ],
  },
  {
    id: 'evt-annual-seminar-2026',
    title: 'Annual technical seminar: sustainability reporting and the assurance gap',
    slug: 'annual-technical-seminar-2026',
    summary:
      'IFRS S1 and S2 have moved from consultation to practice. A full day on what preparers and assurance providers now have to evidence.',
    body: [
      'Sustainability disclosure has arrived at the point where the question is no longer whether to report but what an assurance provider will accept as evidence. This seminar takes the preparer view in the morning and the assurance view after lunch.',
      'Sessions cover scoping and materiality judgements, the data controls that survive review, and how a small practice can take on this work without a specialist team.',
      'Delegates receive the chapter workbook and the assurance checklist developed by the Technical Committee.',
    ],
    category: 'Seminar',
    venue: 'Transcorp Hilton, Maitama, Abuja',
    startsAt: '2026-10-09T08:30:00+01:00',
    endsAt: '2026-10-09T17:00:00+01:00',
    cpdHours: 7,
    isFeatured: true,
    status: 'upcoming',
    coverUrl: conferenceImage,
    ticketTiers: standardTiers,
    speakers: [
      { name: 'Patricia Chinwe Ofili, ACA', role: 'Opening remarks' },
      { name: 'Ngozi Francisca Ashinze, FCA', role: 'Technical lead' },
      { name: 'Charity Okongwu, FCA', role: 'Panel moderator' },
    ],
  },
  {
    id: 'evt-mentorship-clinic',
    title: 'Mentorship clinic for newly inducted members',
    slug: 'mentorship-clinic-newly-inducted',
    summary:
      'A half-day for members inducted in the last eighteen months: career mapping, practice options and finding a mentor within the chapter.',
    body: [
      'The first two years after induction shape a great deal of what follows. This clinic pairs newly inducted members with senior members across practice, the public sector and financial services for structured one-to-one sessions.',
      'The chapter runs the clinic twice a year. Places are limited to keep the pairing ratio useful.',
    ],
    category: 'Training',
    venue: 'ICAN Abuja Liaison Office, Wuse II',
    startsAt: '2026-09-27T10:00:00+01:00',
    endsAt: '2026-09-27T14:00:00+01:00',
    cpdHours: 3,
    isFeatured: false,
    status: 'upcoming',
    coverUrl: chapterPicnicImage,
    ticketTiers: [standardTiers[1], standardTiers[0]],
    speakers: [{ name: 'Nsini Bassey, FCA', role: 'Clinic convener' }],
  },
  {
    id: 'evt-monthly-meeting-oct',
    title: 'October general meeting',
    slug: 'october-general-meeting',
    summary:
      'The monthly chapter meeting: treasurer’s report, committee updates, and a short technical briefing before close.',
    body: [
      'General meetings are open to all financial members. The agenda goes out a week ahead through the members portal.',
      'Attendance at a general meeting completes registration for prospective members who have paid their dues.',
    ],
    category: 'Meeting',
    venue: 'ICAN Abuja Liaison Office, Wuse II',
    startsAt: '2026-10-25T11:00:00+01:00',
    endsAt: '2026-10-25T13:30:00+01:00',
    cpdHours: 1,
    isFeatured: false,
    status: 'upcoming',
    coverUrl: null,
    ticketTiers: [],
    speakers: [],
  },
  {
    id: 'evt-kwali-orphanage',
    title: 'Visit to Kwali orphanage',
    slug: 'visit-to-kwali-orphanage',
    summary:
      'The chapter community outreach visit: provisions, a financial literacy session for the older children, and a facility needs assessment.',
    body: [
      'Members spent the day at the Kwali home with provisions gathered through the welfare committee, and ran a short money-skills session with the older residents.',
      'The chapter has committed to a termly return visit and is funding two secondary school placements from the outreach budget.',
    ],
    category: 'Outreach',
    venue: 'Kwali Area Council, FCT',
    startsAt: '2026-02-14T09:00:00+01:00',
    endsAt: '2026-02-14T15:00:00+01:00',
    cpdHours: 0,
    isFeatured: true,
    status: 'past',
    coverUrl: orphanageOutreachImage,
    ticketTiers: [],
    speakers: [],
  },
  {
    id: 'evt-medical-outreach',
    title: 'Community medical outreach',
    slug: 'community-medical-outreach',
    summary:
      'Free screening and consultations delivered with partner clinicians for an underserved community in the FCT.',
    body: [
      'The chapter funded and staffed a screening day covering blood pressure, blood sugar and basic consultations, with referrals arranged for cases needing follow-up.',
      'Outreach of this kind is funded from the welfare levy and member contributions, and accounted for in the annual report.',
    ],
    category: 'Outreach',
    venue: 'Bwari Area Council, FCT',
    startsAt: '2026-02-28T08:00:00+01:00',
    endsAt: '2026-02-28T16:00:00+01:00',
    cpdHours: 0,
    isFeatured: true,
    status: 'past',
    coverUrl: medicalOutreachImage,
    ticketTiers: [],
    speakers: [],
  },
]

export const featuredEvents = events.filter((e) => e.isFeatured)
export const upcomingEvents = events.filter((e) => e.status === 'upcoming')
export const pastEvents = events.filter((e) => e.status === 'past')

export function findEvent(slug: string): ChapterEvent | undefined {
  return events.find((e) => e.slug === slug)
}
