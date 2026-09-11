import { useEffect, useRef, useState, type TouchEvent } from 'react'
import { Link } from 'react-router-dom'
import { ChevronLeft, ChevronRight } from 'lucide-react'
import { LazyImage } from '@/components/common/LazyImage'
import { Button } from '@/components/ui/button'
import { cn } from '@/lib/utils'
import type { HomeSlide } from '@/types'

const AUTOPLAY_DELAY = 6000

export function HomeSlider({ slides }: { slides: HomeSlide[] }) {
  const [selectedIndex, setSelectedIndex] = useState(0)
  const [isPaused, setIsPaused] = useState(false)
  const touchStartX = useRef<number | null>(null)

  useEffect(() => {
    if (slides.length < 2 || isPaused || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      return
    }

    const interval = window.setInterval(() => {
      setSelectedIndex((current) => (current + 1) % slides.length)
    }, AUTOPLAY_DELAY)

    return () => window.clearInterval(interval)
  }, [isPaused, slides.length])

  if (slides.length === 0) return null
  const activeIndex = selectedIndex % slides.length
  const activeSlide = slides[activeIndex]

  function goTo(index: number) {
    setSelectedIndex((index + slides.length) % slides.length)
  }

  function handleTouchEnd(event: TouchEvent<HTMLElement>) {
    if (touchStartX.current === null) return
    const touch = event.changedTouches.item(0)
    if (!touch) return
    const distance = touch.clientX - touchStartX.current
    touchStartX.current = null

    if (Math.abs(distance) < 45) return
    goTo(selectedIndex + (distance < 0 ? 1 : -1))
  }

  return (
    <section
      aria-label="Chapter highlights"
      aria-roledescription="carousel"
      className="relative overflow-hidden border-b border-rule bg-plum-900"
      onMouseEnter={() => setIsPaused(true)}
      onMouseLeave={() => setIsPaused(false)}
      onFocusCapture={() => setIsPaused(true)}
      onBlurCapture={(event) => {
        if (!event.currentTarget.contains(event.relatedTarget)) setIsPaused(false)
      }}
      onTouchStart={(event) => {
        touchStartX.current = event.touches.item(0)?.clientX ?? null
      }}
      onTouchEnd={handleTouchEnd}
    >
      <article
        key={activeSlide.id}
        aria-label={`${activeIndex + 1} of ${slides.length}`}
        className="grid min-w-0 lg:grid-cols-[minmax(0,0.92fr)_minmax(0,1.08fr)]"
      >
        <div className="order-2 flex min-h-[25rem] min-w-0 items-center bg-plum-900 px-4 pt-14 pb-24 text-plum-200 lg:order-1 lg:min-h-[38rem] lg:py-20">
          <div className="ml-auto w-full min-w-0 max-w-[36rem] lg:pl-4 lg:pr-14">
            <p className="text-[0.78rem] font-semibold tracking-[0.16em] text-gold-300 uppercase">
              {activeSlide.badge}
            </p>
            <h1 className="mt-5 max-w-2xl text-4xl leading-[1.06] text-white sm:text-5xl lg:text-[3.7rem]">
              {activeSlide.title}
            </h1>
            <p className="mt-6 max-w-xl text-[1rem] leading-relaxed text-plum-200/80">
              {activeSlide.description}
            </p>
            <div className="mt-8 flex flex-wrap gap-3">
              <Button
                size="lg"
                asChild
                className="bg-gold-500 text-plum-900 hover:bg-gold-500/85"
              >
                <Link to={activeSlide.ctaLink}>{activeSlide.ctaLabel}</Link>
              </Button>
              {activeSlide.secondaryCtaLink && activeSlide.secondaryCtaLabel && (
                <Button
                  size="lg"
                  variant="outline"
                  asChild
                  className="border-white/40 bg-transparent text-white hover:bg-white hover:text-plum-900"
                >
                  <Link to={activeSlide.secondaryCtaLink}>{activeSlide.secondaryCtaLabel}</Link>
                </Button>
              )}
            </div>
          </div>
        </div>

        <div className="order-1 h-72 min-w-0 overflow-hidden bg-secondary lg:order-2 lg:h-auto lg:min-h-[38rem]">
          <LazyImage
            src={activeSlide.image}
            alt={activeSlide.imageAlt}
            className="h-full w-full object-cover"
            style={{ objectPosition: activeSlide.imagePosition }}
            loading={activeIndex === 0 ? 'eager' : 'lazy'}
          />
        </div>
      </article>

      {slides.length > 1 && (
        <div className="absolute right-4 bottom-4 left-4 flex items-center justify-between gap-5 lg:right-8 lg:bottom-7 lg:left-auto lg:justify-end">
          <div className="flex items-center gap-2" aria-label="Choose a slide">
            {slides.map((slide, index) => (
              <button
                key={slide.id}
                type="button"
                aria-label={`Show slide ${index + 1}: ${slide.title}`}
                aria-current={index === selectedIndex ? 'true' : undefined}
                onClick={() => goTo(index)}
                className={cn(
                  'h-2.5 rounded-full border border-white transition-[width,background-color] duration-300',
                  index === activeIndex ? 'w-8 bg-gold-500' : 'w-2.5 bg-white/50',
                )}
              />
            ))}
          </div>
          <div className="flex gap-2">
            <button
              type="button"
              onClick={() => goTo(selectedIndex - 1)}
              aria-label="Previous slide"
              className="flex h-10 w-10 items-center justify-center rounded-full border border-white/50 bg-white text-plum-900 transition-colors hover:bg-gold-500"
            >
              <ChevronLeft aria-hidden="true" className="h-5 w-5" />
            </button>
            <button
              type="button"
              onClick={() => goTo(selectedIndex + 1)}
              aria-label="Next slide"
              className="flex h-10 w-10 items-center justify-center rounded-full border border-white/50 bg-white text-plum-900 transition-colors hover:bg-gold-500"
            >
              <ChevronRight aria-hidden="true" className="h-5 w-5" />
            </button>
          </div>
        </div>
      )}

      <p className="sr-only" aria-live="polite">
        Slide {activeIndex + 1} of {slides.length}: {activeSlide.title}
      </p>
    </section>
  )
}
