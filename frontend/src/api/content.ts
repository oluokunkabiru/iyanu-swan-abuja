import { api } from '@/api/client'
import { containsCpd, withoutCpdHtml } from '@/lib/contentVisibility'
import type {
  Announcement,
  ChapterEvent,
  Committee,
  CoreValue,
  DirectoryMember,
  ExecutiveMember,
  Faq,
  Firm,
  GalleryImage,
  JobListing,
  MemberSpotlight,
  MembershipLevel,
  NewsPost,
  Partner,
  ProgrammeEntry,
  Publication,
  ResourceItem,
  SiteSettings,
  TicketVerificationResult,
  Training,
} from '@/types'

export const getSettings = () => api.get<SiteSettings>('/settings').then(({ data }) => ({
  ...data,
  // CPD settings content is hidden alongside the commented-out CPD routes and menus.
  tagline: containsCpd(data.tagline) ? '' : data.tagline,
  aims: containsCpd(data.aims) ? '' : data.aims,
  vision: containsCpd(data.vision) ? '' : data.vision,
  mission: (data.mission ?? []).filter((item) => !containsCpd(item)),
  chapterStats: data.chapterStats?.filter((item) => !containsCpd(item)),
  memberBenefits: data.memberBenefits?.filter((item) => !containsCpd(item)),
  aimsAndObjectives: data.aimsAndObjectives?.filter((item) => !containsCpd(item)),
  registrationSteps: data.registrationSteps?.filter((item) => !containsCpd(item)),
  chairpersonWelcome: data.chairpersonWelcome && {
    heading: containsCpd(data.chairpersonWelcome.heading) ? 'A word from the chapter' : data.chairpersonWelcome.heading,
    paragraphs: data.chairpersonWelcome.paragraphs.filter((item) => !containsCpd(item)),
  },
}))

function visibleEvent(event: ChapterEvent): ChapterEvent | null {
  if (containsCpd(event.title, event.slug)) return null
  return {
    ...event,
    summary: containsCpd(event.summary) ? '' : event.summary,
    body: withoutCpdHtml(event.body),
    ticketTiers: event.ticketTiers.map((tier) => ({
      ...tier,
      includes: tier.includes.filter((item) => !containsCpd(item)),
    })),
  }
}

function visibleNewsPost(post: NewsPost): NewsPost | null {
  if (containsCpd(post.title, post.slug, post.excerpt)) return null
  return { ...post, body: withoutCpdHtml(post.body) }
}

export const getMembershipLevels = () =>
  api.get<MembershipLevel[]>('/membership-levels').then((r) => r.data)

export const getCoreValues = () => api.get<CoreValue[]>('/core-values').then((r) => r.data)

export const getExecutives = () => api.get<ExecutiveMember[]>('/executives').then((r) => r.data)

export const getChairperson = () => api.get<ExecutiveMember | null>('/chairperson').then((r) => r.data)

export const getPastChairpersons = () =>
  api.get<ExecutiveMember[]>('/past-chairpersons').then((r) => r.data)

export const getEvents = (when: 'upcoming' | 'past' = 'upcoming') =>
  api.get<ChapterEvent[]>('/events', { params: { when } }).then((r) =>
    r.data.map(visibleEvent).filter((event) => event !== null))

export const getEvent = (slug: string) => api.get<ChapterEvent>(`/events/${slug}`).then((r) => visibleEvent(r.data))

export const registerForEvent = async (
  slug: string,
  payload: {
    event_ticket_type_id: number
    name: string
    email: string
    phone?: string
    notes?: string
  },
) => {
  return api
    .post<{ payment_status: 'paid' | 'pending'; reference: string; authorizationUrl?: string }>(
      `/events/${slug}/register`,
      payload,
    )
    .then((r) => r.data)
}

export const verifyTicket = (reference: string) =>
  api
    .get<TicketVerificationResult>(`/tickets/${reference}/verify`)
    .then((r) => r.data)
    .catch((error) => {
      if (error?.response?.data) return error.response.data as TicketVerificationResult
      throw error
    })

export const checkInTicket = (reference: string) =>
  api.post<TicketVerificationResult>(`/tickets/${reference}/check-in`).then((r) => r.data)

export const getNews = () => api.get<NewsPost[]>('/news').then((r) =>
  r.data.map(visibleNewsPost).filter((post) => post !== null))

export const getNewsPost = (slug: string) => api.get<NewsPost>(`/news/${slug}`).then((r) => visibleNewsPost(r.data))

export const getGallery = () => api.get<GalleryImage[]>('/gallery').then((r) => r.data)

export const getSpotlights = () => api.get<MemberSpotlight[]>('/spotlights').then((r) => r.data)

export const getPartners = () => api.get<Partner[]>('/partners').then((r) => r.data)

export const getPublications = () => api.get<Publication[]>('/publications').then((r) =>
  r.data.filter((item) => !containsCpd(item)))

export const getFaqs = () => api.get<Faq[]>('/faqs').then((r) =>
  r.data.filter((item) => !containsCpd(item)))

export const getAnnouncements = () => api.get<Announcement[]>('/announcements').then((r) =>
  r.data.filter((item) => !containsCpd(item)))

export const getProgramme = () => api.get<ProgrammeEntry[]>('/programme').then((r) =>
  r.data.filter((item) => !containsCpd(item)))

export const getCommittees = () => api.get<Committee[]>('/committees').then((r) =>
  r.data.filter((item) => !containsCpd(item)))

export const getCommittee = (slug: string) =>
  api.get<Committee>(`/committees/${slug}`).then((r) => containsCpd(r.data) ? null : r.data)

export const getTrainings = () => api.get<Training[]>('/trainings').then((r) => r.data)

export const getDirectoryMembers = () =>
  api.get<DirectoryMember[]>('/directory/members').then((r) => r.data)

export const getFirms = () => api.get<Firm[]>('/directory/firms').then((r) => r.data)

export const getJobs = (status: 'open' | 'closed' = 'open') =>
  api.get<JobListing[]>('/jobs', { params: { status } }).then((r) => r.data)

export const getResources = () => api.get<ResourceItem[]>('/resources').then((r) =>
  r.data.filter((item) => !containsCpd(item)))

export const verifyPayment = (reference: string) =>
  api
    .get<{ type: 'subscription' | 'event_registration'; status: string }>(`/payments/verify/${reference}`)
    .then((r) => r.data)

export const submitContact = async (payload: {
  name: string
  email: string
  phone?: string
  subject?: string
  message: string
}) => {
  return api.post('/contact', payload).then((r) => r.data)
}
