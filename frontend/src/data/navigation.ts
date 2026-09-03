import type { NavSection } from '@/types'

/**
 * Top-level navigation, modelled on the ICAN portal: a small number of
 * sections, each opening a multi-column panel of destinations.
 */
export const navigation: NavSection[] = [
  { label: 'Home', to: '/' },
  {
    label: 'About',
    columns: [
      {
        heading: 'The chapter',
        items: [
          { label: 'Who we are', to: '/about', description: 'History, aims and objectives' },
          { label: 'Vision, mission and values', to: '/about#vision' },
          { label: 'Governance', to: '/governance', description: 'Council, elections and standing rules' },
          { label: 'Executive committee', to: '/governance#executives' },
        ],
      },
      {
        heading: 'Structure',
        items: [
          { label: 'Standing committees', to: '/committees', description: 'Eight committees and their remits' },
          { label: 'Relationship with ICAN', to: '/about#ican' },
          { label: 'Affiliates and partners', to: '/about#partners' },
        ],
      },
      {
        heading: 'Get in touch',
        items: [
          { label: 'Contact the chapter', to: '/contact' },
          { label: 'Frequently asked questions', to: '/faqs' },
        ],
      },
    ],
    feature: {
      title: 'A body of women who hold the Charter',
      body: 'Every female member of ICAN is a member of SWAN. The Abuja Chapter is where that membership becomes local, practical and useful.',
      to: '/about',
      cta: 'Read about the chapter',
    },
  },
  {
    label: 'Membership',
    columns: [
      {
        heading: 'Join',
        items: [
          { label: 'Become a member', to: '/membership', description: 'Eligibility and the three-step procedure' },
          { label: 'Registration and payment', to: '/membership/register' },
          { label: 'Member benefits', to: '/membership#benefits' },
        ],
      },
      {
        heading: 'Manage',
        items: [
          { label: 'Subscription and welfare levy', to: '/members/subscription' },
          { label: 'CPD record', to: '/members/cpd' },
          { label: 'My event tickets', to: '/members/tickets' },
          { label: 'Profile and preferences', to: '/members/profile' },
        ],
      },
      {
        heading: 'Support',
        items: [
          { label: 'Welfare fund', to: '/membership#welfare' },
          { label: 'Mentorship programme', to: '/mentorship' },
          { label: 'Membership questions', to: '/faqs' },
        ],
      },
    ],
    feature: {
      title: '₦17,000 a year',
      body: 'A ₦5,000 subscription and a ₦12,000 welfare levy. Pay, get confirmed, attend a meeting — that is the whole process.',
      to: '/membership/register',
      cta: 'Start your registration',
    },
  },
  {
    label: 'Development',
    columns: [
      {
        heading: 'Continuing development',
        items: [
          { label: 'CPD overview', to: '/cpd', description: '120 hours across three years' },
          { label: 'Training calendar', to: '/cpd/trainings' },
          { label: 'Committees and technical work', to: '/committees' },
        ],
      },
      {
        heading: 'Practice',
        items: [
          { label: 'Women in practice', to: '/practice' },
          { label: 'Firm registration', to: '/practice#registration' },
          { label: 'Licence renewal', to: '/practice#renewal' },
          { label: 'Practice attachment register', to: '/practice#attachment' },
        ],
      },
      {
        heading: 'Career',
        items: [
          { label: 'Mentorship programme', to: '/mentorship' },
          { label: 'Job centre', to: '/jobs' },
          { label: 'Students and prospective members', to: '/students' },
        ],
      },
    ],
    feature: {
      title: 'CPD that actually posts',
      body: 'Chapter sessions are accredited and tracked. Register with your ICAN details and hours land on your record.',
      to: '/cpd',
      cta: 'See the CPD calendar',
    },
  },
  {
    label: 'Events',
    columns: [
      {
        heading: 'Attend',
        items: [
          { label: 'All events', to: '/events' },
          { label: 'Upcoming', to: '/events?when=upcoming' },
          { label: 'Past events', to: '/events?when=past' },
          { label: 'Training calendar', to: '/cpd/trainings' },
        ],
      },
      {
        heading: 'Pricing',
        items: [
          { label: 'Event pricing tiers', to: '/events#pricing', description: 'Member and non-member rates' },
          { label: 'How tiered pricing works', to: '/faqs' },
        ],
      },
      {
        heading: 'Look back',
        items: [
          { label: 'Gallery', to: '/gallery' },
          { label: 'Communiqués', to: '/publications' },
        ],
      },
    ],
  },
  {
    label: 'Directories',
    columns: [
      {
        heading: 'Search',
        items: [
          { label: 'Members directory', to: '/directory', description: 'Search by sector and specialisation' },
          { label: 'Registered firms', to: '/directory/firms' },
          { label: 'Job centre', to: '/jobs' },
        ],
      },
      {
        heading: 'Library',
        items: [
          { label: 'Publications', to: '/publications', description: 'Communiqués, bulletins and reports' },
          { label: 'Forms and downloads', to: '/resources' },
          { label: 'Announcements', to: '/announcements' },
        ],
      },
      {
        heading: 'News',
        items: [
          { label: 'Chapter news', to: '/news' },
          { label: 'Gallery', to: '/gallery' },
        ],
      },
    ],
  },
  { label: 'News', to: '/news' },
  { label: 'Contact', to: '/contact' },
]

/** Compact list used by the mobile drawer and the footer. */
export const footerLinks = [
  {
    heading: 'The chapter',
    items: [
      { label: 'About', to: '/about' },
      { label: 'Governance', to: '/governance' },
      { label: 'Committees', to: '/committees' },
      { label: 'Contact', to: '/contact' },
    ],
  },
  {
    heading: 'Membership',
    items: [
      { label: 'Become a member', to: '/membership' },
      { label: 'Register and pay', to: '/membership/register' },
      { label: 'Members area', to: '/members' },
      { label: 'Mentorship', to: '/mentorship' },
    ],
  },
  {
    heading: 'Professional',
    items: [
      { label: 'CPD', to: '/cpd' },
      { label: 'Training calendar', to: '/cpd/trainings' },
      { label: 'Women in practice', to: '/practice' },
      { label: 'Job centre', to: '/jobs' },
    ],
  },
  {
    heading: 'Library',
    items: [
      { label: 'Publications', to: '/publications' },
      { label: 'Forms and downloads', to: '/resources' },
      { label: 'Announcements', to: '/announcements' },
      { label: 'Gallery', to: '/gallery' },
    ],
  },
]
