import { api } from '@/api/client'
import type {
  CoreValue,
  ExecutiveMember,
  GalleryImage,
  MemberSpotlight,
  NewsPost,
  Partner,
  Publication,
  SiteSettings,
  SwanEvent,
} from '@/types'

export const getSettings = () => api.get<SiteSettings>('/settings').then((r) => r.data)

export const getCoreValues = () => api.get<CoreValue[]>('/core-values').then((r) => r.data)

export const getExecutives = () => api.get<ExecutiveMember[]>('/executives').then((r) => r.data)

export const getEvents = (when: 'upcoming' | 'past' = 'upcoming') =>
  api.get<SwanEvent[]>('/events', { params: { when } }).then((r) => r.data)

export const getEvent = (slug: string) => api.get<SwanEvent>(`/events/${slug}`).then((r) => r.data)

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

export const submitContact = (payload: {
  name: string
  email: string
  phone?: string
  subject?: string
  message: string
}) => api.post('/contact', payload).then((r) => r.data)
