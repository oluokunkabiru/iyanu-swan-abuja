import type { GalleryImage, Partner } from '@/types'

const uploads = 'https://swanabujachapter.com/wp-content/uploads/2026/07'

export const gallery: GalleryImage[] = [
  { id: 'g1', caption: 'Annual seminar, plenary session', album: 'Technical seminars', year: 2026, imageUrl: `${uploads}/SWAN-17-1024x684.jpg` },
  { id: 'g2', caption: 'Delegates at the chapter seminar', album: 'Technical seminars', year: 2026, imageUrl: `${uploads}/DSC06955-1.jpg` },
  { id: 'g3', caption: 'Kwali orphanage outreach visit', album: 'Community outreach', year: 2026, imageUrl: `${uploads}/SWAN-KWALI-ORPHANAGE-22-1024x683.jpg` },
  { id: 'g4', caption: 'Medical screening day', album: 'Community outreach', year: 2026, imageUrl: `${uploads}/DSC05883-1024x684.jpg` },
  { id: 'g5', caption: 'Chapter picnic', album: 'Chapter life', year: 2026, imageUrl: `${uploads}/SWAN-ABUJA-PICNIC-085-1024x663.jpg` },
  { id: 'g6', caption: 'Members at the general meeting', album: 'Chapter life', year: 2026, imageUrl: `${uploads}/WhatsApp-Image-2026-07-06-at-16.15.25-967x1024.jpeg` },
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
