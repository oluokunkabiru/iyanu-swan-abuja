import type { HomeSlide } from '@/types'

function isHomeSlide(value: unknown): value is HomeSlide {
  if (typeof value !== 'object' || value === null) return false

  const slide = value as Partial<HomeSlide>

  return (
    typeof slide.id === 'string' &&
    typeof slide.badge === 'string' &&
    typeof slide.title === 'string' &&
    typeof slide.description === 'string' &&
    typeof slide.ctaLabel === 'string' &&
    typeof slide.ctaLink === 'string' &&
    typeof slide.image === 'string' &&
    slide.image.length > 0 &&
    typeof slide.imageAlt === 'string'
  )
}

export async function getSliders(signal?: AbortSignal): Promise<HomeSlide[]> {
  const baseUrl = (import.meta.env.VITE_API_URL ?? '').replace(/\/$/, '')
  const response = await fetch(`${baseUrl}/api/sliders`, {
    headers: { Accept: 'application/json' },
    signal,
  })

  if (!response.ok) {
    throw new Error(`Slider API returned ${response.status}`)
  }

  const payload: unknown = await response.json()

  return Array.isArray(payload) ? payload.filter(isHomeSlide) : []
}
