import {
  annualSeminarImage,
  chapterPicnicImage,
  conferenceImage,
} from '@/assets/images'
import type { HomeSlide } from '@/types'

/**
 * Local fallback for the homepage carousel when the API is unavailable or no
 * managed slides have been published yet.
 */
export const homeSlides: HomeSlide[] = [
  {
    id: 'professional-community',
    badge: 'SWAN Abuja Chapter',
    title: 'Women who hold the Charter and hold each other to it.',
    description:
      'A professional community for technical growth, leadership, mentorship and meaningful service across the Federal Capital Territory.',
    ctaLabel: 'Join the chapter',
    ctaLink: '/membership/register',
    secondaryCtaLabel: 'Discover SWAN',
    secondaryCtaLink: '/about',
    image: annualSeminarImage,
    imageAlt: 'SWAN Abuja members at a professional seminar',
    imagePosition: 'center',
  },
  {
    id: 'professional-development',
    badge: 'Professional development',
    title: 'Learning that keeps women at the front of the profession.',
    description:
      'Earn relevant CPD hours through technical seminars, practical workshops and leadership conversations designed for today’s accountant.',
    ctaLabel: 'View upcoming events',
    ctaLink: '/events',
    secondaryCtaLabel: 'Explore CPD',
    secondaryCtaLink: '/cpd',
    image: conferenceImage,
    imageAlt: 'Delegates attending an accountancy conference',
    imagePosition: 'center',
  },
  {
    id: 'sisterhood-and-service',
    badge: 'Connection and service',
    title: 'A network that grows careers and strengthens communities.',
    description:
      'Build trusted professional relationships, find mentors and join chapter programmes that turn expertise into lasting impact.',
    ctaLabel: 'Meet the community',
    ctaLink: '/governance',
    secondaryCtaLabel: 'See chapter life',
    secondaryCtaLink: '/gallery',
    image: chapterPicnicImage,
    imageAlt: 'Members of SWAN Abuja spending time together at the chapter picnic',
    imagePosition: 'center',
  },
]
