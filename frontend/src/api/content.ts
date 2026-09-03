import { api } from '@/api/client'
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
  NewsPost,
  Partner,
  ProgrammeEntry,
  Publication,
  ResourceItem,
  SiteSettings,
  Training,
} from '@/types'

export const getSettings = () => api.get<SiteSettings>('/settings').then((r) => r.data)

export const getCoreValues = () => api.get<CoreValue[]>('/core-values').then((r) => r.data)

export const getExecutives = () => api.get<ExecutiveMember[]>('/executives').then((r) => r.data)

export const getEvents = (when: 'upcoming' | 'past' = 'upcoming') =>
  api.get<ChapterEvent[]>('/events', { params: { when } }).then((r) => r.data)

export const getEvent = (slug: string) => api.get<ChapterEvent>(`/events/${slug}`).then((r) => r.data)

export const registerForEvent = (
  slug: string,
  payload: {
    event_ticket_type_id: number
    name: string
    email: string
    phone?: string
    notes?: string
  },
) => api.post(`/events/${slug}/register`, payload).then((r) => r.data)

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

export const submitContact = (payload: {
  name: string
  email: string
  phone?: string
  subject?: string
  message: string
}) => api.post('/contact', payload).then((r) => r.data)
