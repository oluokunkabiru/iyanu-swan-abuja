export interface SiteSettings {
  id: number
  chapter_name: string
  tagline: string | null
  mission: string | null
  vision: string | null
  address: string | null
  phone: string | null
  email: string | null
  facebook_url: string | null
  instagram_url: string | null
  twitter_url: string | null
  hero_video_url: string | null
  membership_subscription_fee: number
  membership_welfare_fee: number
  logo_url: string | null
}

export interface CoreValue {
  id: number
  title: string
  description: string | null
  sort_order: number
}

export interface ExecutiveMember {
  id: number
  name: string
  credential: string | null
  position: string
  bio: string | null
  sort_order: number
  is_active: boolean
  photo_url: string | null
}

export interface EventTicketType {
  id: number
  event_id: number
  label: string
  price: number
  currency: string
}

export interface EventRegistration {
  id: number
  event_id: number
  event_ticket_type_id: number
  name: string
  email: string
  phone: string | null
  payment_status: 'pending' | 'confirmed'
  amount: number
  notes: string | null
  created_at: string
  event?: SwanEvent
  ticket_type?: EventTicketType
}

export interface SwanEvent {
  id: number
  title: string
  slug: string
  description: string | null
  location: string | null
  video_url: string | null
  starts_at: string
  ends_at: string | null
  is_featured: boolean
  status: 'draft' | 'published'
  cover_url: string | null
  ticket_types?: EventTicketType[]
  gallery_images?: GalleryImage[]
}

export interface NewsPost {
  id: number
  title: string
  slug: string
  excerpt: string | null
  body: string | null
  published_at: string | null
  is_published: boolean
  cover_url: string | null
}

export interface GalleryImage {
  id: number
  caption: string | null
  event_id: number | null
  sort_order: number
  image_url: string | null
}

export interface MemberSpotlight {
  id: number
  name: string
  quote: string | null
  sort_order: number
  photo_url: string | null
}

export interface Partner {
  id: number
  name: string
  url: string | null
  sort_order: number
  logo_url: string | null
}

export interface Publication {
  id: number
  title: string
  category: string | null
  published_at: string | null
  file_url: string | null
}

export interface MemberProfile {
  id: number
  user_id: number
  membership_number: string | null
  membership_status: 'pending' | 'active' | 'expired'
  phone: string | null
  joined_at: string | null
}

export interface AuthUser {
  id: number
  name: string
  email: string
  role: 'admin' | 'member'
  member_profile: MemberProfile | null
}
