import type { GalleryImage, Partner } from '@/types'
import {
  annualSeminarImage,
  chapterPicnicImage,
  conferenceImage,
  generalMeetingImage,
  medicalOutreachImage,
  orphanageOutreachImage,
} from '@/assets/images'

export const gallery: GalleryImage[] = [
  { id: 'g1', caption: 'Annual seminar, plenary session', album: 'Technical seminars', year: 2026, imageUrl: annualSeminarImage },
  { id: 'g2', caption: 'Delegates at the chapter seminar', album: 'Technical seminars', year: 2026, imageUrl: conferenceImage },
  { id: 'g3', caption: 'Kwali orphanage outreach visit', album: 'Community outreach', year: 2026, imageUrl: orphanageOutreachImage },
  { id: 'g4', caption: 'Medical screening day', album: 'Community outreach', year: 2026, imageUrl: medicalOutreachImage },
  { id: 'g5', caption: 'Chapter picnic', album: 'Chapter life', year: 2026, imageUrl: chapterPicnicImage },
  { id: 'g6', caption: 'Members at the general meeting', album: 'Chapter life', year: 2026, imageUrl: generalMeetingImage },
]

export const galleryAlbums = Array.from(new Set(gallery.map((g) => g.album)))

export const partners: Partner[] = [
  { id: 'p-ican', name: 'Institute of Chartered Accountants of Nigeria', url: 'https://icanig.org/ican/', scope: 'Parent body' },
  { id: 'p-swan', name: 'SWAN National', url: 'https://icanig.org/ican/', scope: 'Parent body' },
  { id: 'p-abwa', name: 'Association of Accountancy Bodies in West Africa', url: 'https://abwa.org.ng/', scope: 'Affiliate' },
  { id: 'p-pafa', name: 'Pan African Federation of Accountants', url: 'https://www.pafa.org.za/', scope: 'Affiliate' },
  { id: 'p-ifac', name: 'International Federation of Accountants', url: 'https://www.ifac.org/', scope: 'Affiliate' },
  { id: 'p-caw', name: 'Chartered Accountants Worldwide', url: 'https://charteredaccountantsworldwide.com/', scope: 'Affiliate' },
]
