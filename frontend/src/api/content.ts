import { api, ensureCsrfCookie } from '@/api/client'
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

export const getSettings = () => api.get<SiteSettings>('/settings').then((r) => r.data)

export const getMembershipLevels = () =>
  api.get<MembershipLevel[]>('/membership-levels').then((r) => r.data)

export const getCoreValues = () => api.get<CoreValue[]>('/core-values').then((r) => r.data)

export const getExecutives = () => api.get<ExecutiveMember[]>('/executives').then((r) => r.data)

export const getPastChairpersons = () =>
  api.get<ExecutiveMember[]>('/past-chairpersons').then((r) => r.data)

export const getEvents = (when: 'upcoming' | 'past' = 'upcoming') =>
  api.get<ChapterEvent[]>('/events', { params: { when } }).then((r) => r.data)

export const getEvent = (slug: string) => api.get<ChapterEvent>(`/events/${slug}`).then((r) => r.data)

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
  await ensureCsrfCookie()
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

export const getNews = () => api.get<NewsPost[]>('/news').then((r) => r.data)

export const getNewsPost = (slug: string) => api.get<NewsPost>(`/news/${slug}`).then((r) => r.data)

export const getGallery = () => api.get<GalleryImage[]>('/gallery').then((r) => r.data)

export const getSpotlights = () => api.get<MemberSpotlight[]>('/spotlights').then((r) => r.data)

export const getPartners = () => api.get<Partner[]>('/partners').then((r) => r.data)

export const getPublications = () => api.get<Publication[]>('/publications').then((r) => r.data)

export const getFaqs = () => api.get<Faq[]>('/faqs').then((r) => r.data)

export const getAnnouncements = () => api.get<Announcement[]>('/announcements').then((r) => r.data)

export const getProgramme = () => api.get<ProgrammeEntry[]>('/programme').then((r) => r.data)

export const getCommittees = () => api.get<Committee[]>('/committees').then((r) => r.data)

export const getCommittee = (slug: string) =>
  api.get<Committee>(`/committees/${slug}`).then((r) => r.data)

export const getTrainings = () => api.get<Training[]>('/trainings').then((r) => r.data)

export const getDirectoryMembers = () =>
  api.get<DirectoryMember[]>('/directory/members').then((r) => r.data)

export const getFirms = () => api.get<Firm[]>('/directory/firms').then((r) => r.data)

export const getJobs = () => api.get<JobListing[]>('/jobs').then((r) => r.data)

export const getResources = () => api.get<ResourceItem[]>('/resources').then((r) => r.data)

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
  await ensureCsrfCookie()
  return api.post('/contact', payload).then((r) => r.data)
}
