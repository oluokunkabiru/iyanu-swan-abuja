import { useMemo, type ReactNode } from 'react'
import { Link, useSearchParams } from 'react-router-dom'
import { motion } from 'framer-motion'
import { getGallery } from '@/api/content'
import { LazyImage } from '@/components/common/LazyImage'
import { EmptyState, PageHeader, Section, SectionHeading, StatusTag } from '@/components/common/Primitives'
import { Skeleton } from '@/components/ui/skeleton'
import { useApiData } from '@/hooks/useApiData'
import { containerVariants, itemVariants } from '@/lib/animations'
import { formatDate } from '@/lib/format'
import { cn } from '@/lib/utils'
import type { GalleryItem } from '@/types'

/** Items not linked to any event — one album, and one entry in the type filter. */
const GENERAL = 'General'

type Album = {
  key: string
  title: string
  eventSlug: string | null
  eventType: string | null
  eventDate: string | null
  items: GalleryItem[]
}

function typeOf(item: GalleryItem): string {
  return item.eventType ?? GENERAL
}

/** YouTube and Vimeo links play inline; anything else opens in a new tab. */
function embedUrl(url: string): string | null {
  const youtube = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([\w-]{6,})/)
  if (youtube) return `https://www.youtube-nocookie.com/embed/${youtube[1]}`
  const vimeo = url.match(/vimeo\.com\/(?:video\/)?(\d+)/)
  if (vimeo) return `https://player.vimeo.com/video/${vimeo[1]}`
  return null
}

function FilterChip({ active, onClick, children }: { active: boolean; onClick: () => void; children: ReactNode }) {
  return (
    <button
      type="button"
      onClick={onClick}
      aria-pressed={active}
      className={cn(
        'rounded-sm border px-3 py-1.5 text-[0.84rem] font-medium transition-colors',
        active
          ? 'border-plum-700 bg-plum-700 text-white dark:border-primary dark:bg-primary dark:text-primary-foreground'
          : 'border-border bg-card text-muted-foreground hover:text-foreground',
      )}
    >
      {children}
    </button>
  )
}

export default function Gallery() {
  const { data: gallery, isLoading } = useApiData(getGallery, [] as GalleryItem[])
  const [params, setParams] = useSearchParams()
  const tab = params.get('tab') === 'videos' ? 'videos' : 'photos'
  const type = params.get('type') ?? 'All'
  const eventSlug = params.get('event')

  function update(next: Record<string, string | null>) {
    const merged = new URLSearchParams(params)
    for (const [key, value] of Object.entries(next)) {
      if (value === null) merged.delete(key)
      else merged.set(key, value)
    }
    setParams(merged)
  }

  const inScope = useMemo(
    () => (eventSlug ? gallery.filter((g) => g.eventSlug === eventSlug) : gallery),
    [gallery, eventSlug],
  )
  const photoCount = inScope.filter((g) => g.mediaType === 'image').length
  const videoCount = inScope.filter((g) => g.mediaType === 'video').length
  const ofTab = inScope.filter((g) => g.mediaType === (tab === 'videos' ? 'video' : 'image'))

  const types = useMemo(() => {
    const found = Array.from(new Set(ofTab.map(typeOf))).sort()
    return found.includes(GENERAL) ? [...found.filter((t) => t !== GENERAL), GENERAL] : found
  }, [ofTab])

  const albums = useMemo(() => {
    const byKey = new Map<string, Album>()
    for (const item of ofTab) {
      if (type !== 'All' && typeOf(item) !== type) continue
      const key = item.eventSlug ?? 'general'
      const album = byKey.get(key) ?? {
        key,
        title: item.eventTitle ?? GENERAL,
        eventSlug: item.eventSlug,
        eventType: item.eventType,
        eventDate: item.eventDate,
        items: [],
      }
      album.items.push(item)
      byKey.set(key, album)
    }
    // General goes last, after the event albums.
    return Array.from(byKey.values()).sort((a, b) => Number(a.key === 'general') - Number(b.key === 'general'))
  }, [ofTab, type])

  const scopedEvent = eventSlug ? inScope[0] : undefined

  return (
    <>
      <PageHeader
        breadcrumb={
          eventSlug
            ? [{ label: 'Home', to: '/' }, { label: 'Gallery', to: '/gallery' }, { label: scopedEvent?.eventTitle ?? 'Event' }]
            : [{ label: 'Home', to: '/' }, { label: 'Gallery' }]
        }
        title={eventSlug && scopedEvent?.eventTitle ? scopedEvent.eventTitle : 'Gallery'}
        intro={
          eventSlug
            ? 'Photos and videos from this event.'
            : 'Photos and videos from chapter events — seminars, outreach and chapter life, grouped by event.'
        }
      />

      <Section>
        <div className="flex flex-wrap items-center justify-between gap-4">
          <div role="tablist" aria-label="Media type" className="inline-flex border border-border bg-card p-1">
            {(['photos', 'videos'] as const).map((t) => (
              <button
                key={t}
                type="button"
                role="tab"
                aria-selected={tab === t}
                onClick={() => update({ tab: t === 'photos' ? null : t, type: null })}
                className={cn(
                  'rounded-sm px-4 py-1.5 text-[0.88rem] font-medium transition-colors',
                  tab === t
                    ? 'bg-plum-700 text-white dark:bg-primary dark:text-primary-foreground'
                    : 'text-muted-foreground hover:text-foreground',
                )}
              >
                {t === 'photos' ? 'Photos' : 'Videos'}
                <span className="tnum ml-1.5 opacity-70">{isLoading ? '' : t === 'photos' ? photoCount : videoCount}</span>
              </button>
            ))}
          </div>
          {eventSlug && (
            <div className="flex flex-wrap gap-4 text-[0.85rem] font-semibold">
              <Link to={`/events/${eventSlug}`} className="text-plum-700 underline-offset-4 hover:underline dark:text-primary">
                About this event
              </Link>
              <Link to="/gallery" className="text-plum-700 underline-offset-4 hover:underline dark:text-primary">
                All albums
              </Link>
            </div>
          )}
        </div>

        {!eventSlug && types.length > 1 && (
          <div className="mt-6">
            <SectionHeading title="Event type" className="mb-4" />
            <div className="flex flex-wrap gap-2">
              {['All', ...types].map((t) => (
                <FilterChip key={t} active={type === t} onClick={() => update({ type: t === 'All' ? null : t })}>
                  {t === 'All' ? 'All types' : t}
                </FilterChip>
              ))}
            </div>
          </div>
        )}

        {isLoading ? (
          <div className="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {Array.from({ length: 6 }).map((_, i) => (
              <Skeleton key={i} className="aspect-[4/3]" />
            ))}
          </div>
        ) : albums.length === 0 ? (
          <div className="mt-8">
            <EmptyState
              title={tab === 'videos' ? 'No videos here yet' : 'No photos here yet'}
              body={
                tab === 'videos'
                  ? 'Event videos appear here once they are added to an event.'
                  : 'Event photos appear here once they are added to an event.'
              }
            />
          </div>
        ) : (
          <div className="mt-10 space-y-14">
            {albums.map((album) => (
              <section key={album.key} aria-labelledby={`album-${album.key}`}>
                <div className="flex flex-wrap items-baseline justify-between gap-3 border-b border-border pb-3">
                  <div>
                    <h2 id={`album-${album.key}`} className="text-[1.2rem]">
                      {album.eventSlug && !eventSlug ? (
                        <Link to={`/gallery?event=${album.eventSlug}${tab === 'videos' ? '&tab=videos' : ''}`} className="hover:text-plum-700 dark:hover:text-primary">
                          {album.title}
                        </Link>
                      ) : (
                        album.title
                      )}
                    </h2>
                    <div className="mt-1.5 flex flex-wrap items-center gap-2 text-[0.8rem] text-muted-foreground">
                      {album.eventType && <StatusTag tone="neutral">{album.eventType}</StatusTag>}
                      {album.eventDate && <span className="tnum">{formatDate(album.eventDate)}</span>}
                      <span className="tnum">
                        · {album.items.length} {tab === 'videos' ? (album.items.length === 1 ? 'video' : 'videos') : album.items.length === 1 ? 'photo' : 'photos'}
                      </span>
                    </div>
                  </div>
                  {album.eventSlug && !eventSlug && (
                    <Link to={`/events/${album.eventSlug}`} className="text-[0.84rem] font-semibold text-plum-700 underline-offset-4 hover:underline dark:text-primary">
                      View event
                    </Link>
                  )}
                </div>

                <motion.ul
                  className="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
                  initial="initial"
                  whileInView="animate"
                  viewport={{ once: true, amount: 0.1 }}
                  variants={containerVariants}
                >
                  {album.items.map((item) => (
                    <motion.li key={item.id} className="group border border-border bg-card" variants={itemVariants}>
                      {item.mediaType === 'video' ? <GalleryVideo item={item} title={album.title} /> : (
                        <div className="overflow-hidden">
                          <LazyImage
                            src={item.url}
                            alt={item.caption || album.title}
                            className="aspect-[4/3] w-full object-cover transition-transform duration-500 ease-out group-hover:scale-110"
                            hoverZoom={false}
                          />
                        </div>
                      )}
                      {item.caption && (
                        <p className="p-4 text-[0.9rem] leading-snug">{item.caption}</p>
                      )}
                    </motion.li>
                  ))}
                </motion.ul>
              </section>
            ))}
          </div>
        )}
      </Section>
    </>
  )
}

function GalleryVideo({ item, title }: { item: GalleryItem; title: string }) {
  if (!item.isExternal) {
    return (
      <video controls preload="metadata" className="aspect-video w-full bg-black" aria-label={item.caption || title}>
        <source src={item.url} type={item.mimeType ?? undefined} />
      </video>
    )
  }

  const embed = embedUrl(item.url)
  if (embed) {
    return (
      <iframe
        src={embed}
        title={item.caption || title}
        className="aspect-video w-full"
        loading="lazy"
        allow="accelerometer; encrypted-media; gyroscope; picture-in-picture; fullscreen"
        allowFullScreen
      />
    )
  }

  return (
    <a
      href={item.url}
      target="_blank"
      rel="noreferrer"
      className="flex aspect-video w-full items-center justify-center bg-muted text-[0.9rem] font-semibold text-plum-700 underline-offset-4 hover:underline dark:text-primary"
    >
      Watch the video ↗
    </a>
  )
}
