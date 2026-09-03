import type {
  ChapterStat,
  CoreValue,
  MemberBenefit,
  RegistrationStep,
  SiteSettings,
} from '@/types'

const env = import.meta.env

export const site: SiteSettings = {
  chapterName: env.VITE_APP_NAME || 'SWAN Abuja Chapter',
  shortName: 'SWAN Abuja',
  parentBody: 'Institute of Chartered Accountants of Nigeria',
  tagline:
    env.VITE_SITE_TAGLINE || 'Society of Women Accountants of Nigeria, Abuja Chapter',
  vision:
    "To be the world's foremost professional association of female chartered accountants, championing excellence and leadership through empowerment, advocacy and global collaboration.",
  mission: [
    'To maintain the dignity of the professional female accountant, to be relevant and impactful, and to empower, mentor and uphold the ideals of the accounting profession.',
    'To foster growth, visibility and professional development, and to serve our communities through financial literacy.',
  ],
  aims: 'Promoting and upholding high standards of effectiveness and professional conduct without discrimination, and supporting ICAN in safeguarding its Charter, the status of the profession, and the interests of its female members.',
  address: env.VITE_CONTACT_ADDRESS || 'Abuja, Federal Capital Territory, Nigeria',
  phone: env.VITE_CONTACT_PHONE || '0803 450 2401',
  email: env.VITE_CONTACT_EMAIL || 'contact@swanabujachapter.com',
  socials: [
    { label: 'Facebook', url: 'https://facebook.com', network: 'facebook' },
    { label: 'X', url: 'https://x.com', network: 'twitter' },
    { label: 'Instagram', url: 'https://instagram.com', network: 'instagram' },
    { label: 'LinkedIn', url: 'https://linkedin.com', network: 'linkedin' },
  ],
  subscriptionFee: 5000,
  welfareFee: 12000,
}

export const coreValues: CoreValue[] = [
  {
    title: 'Impact',
    description:
      'We measure the chapter by what changes because of it — in boardrooms, in public finances, and in the communities we visit.',
  },
  {
    title: 'Passion',
    description:
      'Members give their time to mentoring, outreach and technical work because the profession is worth the effort.',
  },
  {
    title: 'Integrity',
    description:
      'The chartered qualification rests on trust. We hold each other to the standard the Charter assumes.',
  },
  {
    title: 'Accountability',
    description:
      'We publish what we do, account for what we collect, and answer to the members who fund the chapter.',
  },
  {
    title: 'Professionalism',
    description:
      'Technical competence maintained through continuing development, and conduct that reflects well on every woman who follows.',
  },
]

export const chapterStats: ChapterStat[] = [
  { label: 'Members on the chapter roll', value: '640+', note: 'Female ICAN members across the FCT' },
  { label: 'CPD hours delivered', value: '1,240', note: 'Across seminars, clinics and technical sessions' },
  { label: 'Standing committees', value: '8', note: 'Technical, welfare, outreach and mentorship' },
  { label: 'Years in the FCT', value: '24', note: 'Serving Abuja and the surrounding districts' },
]

export const registrationSteps: RegistrationStep[] = [
  {
    step: 1,
    title: 'Pay your dues',
    description:
      'Pay ₦17,000 for the year — ₦5,000 subscription and ₦12,000 welfare. Card, transfer and USSD are all accepted through the portal.',
  },
  {
    step: 2,
    title: 'Get confirmed',
    description:
      'Once the Financial Secretary confirms your payment, you are formally admitted to the Society and your record is opened.',
  },
  {
    step: 3,
    title: 'Attend a meeting',
    description:
      'Show up at any scheduled chapter meeting. Attendance completes your registration and puts you on the active roll.',
  },
]

export const memberBenefits: MemberBenefit[] = [
  {
    title: 'Member rates on every event',
    description:
      'Sign in before you check out and the member price applies automatically — roughly 40% off the public rate on chapter seminars.',
  },
  {
    title: 'CPD credits that count',
    description:
      'Technical sessions, workshops and the annual seminar qualify for ICAN Continuing Professional Development credit. Register with your ICAN details so attendance is tracked.',
  },
  {
    title: 'Committee and leadership roles',
    description:
      'Active members are eligible to join standing committees and to stand for executive office within the chapter.',
  },
  {
    title: 'Mentorship and networking',
    description:
      'Direct access to senior female professionals, executive directors and decision-makers across the public and private sectors in Abuja.',
  },
  {
    title: 'Welfare support',
    description:
      'The welfare fund stands behind members through bereavement, illness and major life events, administered by the Welfare Officer.',
  },
  {
    title: 'Practice and career support',
    description:
      'Firm registration guidance, the chapter job board, and referrals through the members directory.',
  },
]

export const aimsAndObjectives: string[] = [
  'Promote and uphold high standards of effectiveness and professional conduct among female members, without discrimination.',
  'Support ICAN in safeguarding its Charter, the status of the profession, and the interests of its female members.',
  'Advance the professional development of women in accountancy through structured CPD, technical sessions and mentorship.',
  'Improve the visibility of female chartered accountants in leadership, governance and public financial management.',
  'Deliver financial literacy and community service across the Federal Capital Territory.',
  'Build a welfare structure that supports members through the whole of their professional lives.',
]
