/** Shared content model. All data is static and lives under `src/data`. */

export interface SocialLink {
  label: string
  url: string
  network: 'facebook' | 'twitter' | 'instagram' | 'linkedin' | 'youtube'
}

export interface SiteSettings {
  chapterName: string
  shortName: string
  parentBody: string
  tagline: string
  vision: string
  mission: string[]
  aims: string
  address: string
  phone: string
  email: string
  socials: SocialLink[]
  subscriptionFee: number
  welfareFee: number
  logoUrl?: string | null
  chairpersonWelcome?: { heading: string; paragraphs: string[] }
  chapterStats?: ChapterStat[]
  registrationSteps?: RegistrationStep[]
  memberBenefits?: MemberBenefit[]
  aimsAndObjectives?: string[]
}

export interface HomeSlide {
  id: string
  badge: string
  title: string
  description: string
  ctaLabel: string
  ctaLink: string
  secondaryCtaLabel?: string
  secondaryCtaLink?: string
  image: string
  imageAlt: string
  imagePosition?: string
}

export interface CoreValue {
  title: string
  description: string
}

export interface ExecutiveMember {
  id: string
  name: string
  credential: 'ACA' | 'FCA'
  position: string
  bio: string
  photoUrl: string | null
  isPrincipal: boolean
}

export interface MemberSpotlight {
  id: string
  name: string
  quote: string
  photoUrl: string | null
}

export interface TicketTier {
  id: string
  audience: 'member' | 'non-member'
  mode: 'physical' | 'virtual'
  label: string
  price: number
  includes: string[]
}

export interface ChapterEvent {
  id: string
  title: string
  slug: string
  summary: string
  body: string[]
  category: 'Seminar' | 'Outreach' | 'Training' | 'Meeting' | 'Conference'
  venue: string
  startsAt: string
  endsAt: string | null
  cpdHours: number
  isFeatured: boolean
  status: 'upcoming' | 'past'
  coverUrl: string | null
  ticketTiers: TicketTier[]
  speakers: { name: string; role: string }[]
}

export interface NewsPost {
  id: string
  title: string
  slug: string
  excerpt: string
  body: string[]
  category: 'Chapter' | 'ICAN' | 'Profession' | 'Advocacy'
  publishedAt: string
  author: string
  coverUrl: string | null
}

export interface Announcement {
  id: string
  title: string
  date: string
  href: string
  kind: 'notice' | 'circular' | 'deadline'
}

export interface ProgrammeEntry {
  id: string
  name: string
  date: string
  venue: string
  href: string
}

export interface Faq {
  id: string
  question: string
  answer: string
  topic: 'Membership' | 'Events' | 'Payments' | 'CPD' | 'General'
}

export interface GalleryImage {
  id: string
  caption: string
  album: string
  year: number
  imageUrl: string
}

export interface Partner {
  id: string
  name: string
  url: string
  scope: 'Parent body' | 'Affiliate' | 'Sponsor'
}

export interface Publication {
  id: string
  title: string
  category: 'Communiqué' | 'Newsletter' | 'Technical' | 'Report' | 'Address'
  publishedAt: string
  fileUrl: string
  sizeLabel: string
}

export interface Committee {
  id: string
  name: string
  slug: string
  remit: string
  chair: string
  focusAreas: string[]
  meetingCadence: string
}

export interface Training {
  id: string
  title: string
  provider: 'SWAN Abuja' | 'ICAN MPD' | 'Faculty'
  deliveryMode: 'Physical' | 'Virtual' | 'Hybrid'
  date: string
  cpdHours: number
  fee: number
  memberFee: number
  seatsLeft: number
}

export type Sector =
  | 'Public practice'
  | 'Public sector'
  | 'Financial services'
  | 'Industry'
  | 'Academia'
  | 'Consulting'

export interface DirectoryMember {
  id: string
  name: string
  credential: 'ACA' | 'FCA'
  membershipNumber: string
  sector: Sector
  specialisation: string
  yearAdmitted: number
  chapterRole: string | null
}

export interface Firm {
  id: string
  name: string
  principal: string
  licenceNumber: string
  services: string[]
  area: string
  licenceStatus: 'Active' | 'Renewal due'
}

export interface JobListing {
  id: string
  title: string
  organisation: string
  location: string
  type: 'Full-time' | 'Contract' | 'Part-time'
  level: 'Entry' | 'Mid' | 'Senior' | 'Executive'
  postedAt: string
  closesAt: string
  summary: string
  applicationUrl?: string | null
}

export interface ResourceItem {
  id: string
  title: string
  description: string
  category: 'Form' | 'Guide' | 'Policy' | 'Template' | 'Syllabus'
  fileUrl: string
  format: 'PDF' | 'DOCX' | 'XLSX'
}

export interface MemberBenefit {
  title: string
  description: string
}

export interface RegistrationStep {
  step: number
  title: string
  description: string
}

export interface ChapterStat {
  label: string
  value: string
  note: string
}

/* ── Member area ─────────────────────────────────────────────────────────── */

export interface CpdRecord {
  id: string
  activity: string
  date: string
  hours: number
  type: 'Structured' | 'Unstructured'
  verified: boolean
}

export interface SubscriptionRecord {
  id: string
  year: number
  subscription: number
  welfare: number
  status: 'Paid' | 'Outstanding'
  paidOn: string | null
  reference: string | null
}

export interface TicketRecord {
  id: string
  eventTitle: string
  eventSlug: string
  tier: string
  amount: number
  reference: string
  status: 'Confirmed' | 'Pending'
  issuedAt: string
}

export interface AuthUser {
  id: string
  name: string
  email: string
  credential: 'ACA' | 'FCA'
  membershipNumber: string
  membershipStatus: 'active' | 'pending' | 'expired'
  role: 'member' | 'admin'
  joinedAt: string
  cpdTarget: number
}

/* ── Navigation ──────────────────────────────────────────────────────────── */

export interface NavLeaf {
  label: string
  to: string
  description?: string
  external?: boolean
}

export interface NavColumn {
  heading: string
  items: NavLeaf[]
}

export interface NavSection {
  label: string
  to?: string
  columns?: NavColumn[]
  feature?: { title: string; body: string; to: string; cta: string }
}
