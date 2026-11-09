import { useMemo, useState, type ReactNode } from 'react'
import { Link, useSearchParams } from 'react-router-dom'
import { motion } from 'framer-motion'
import { Check, Download, Images, Play, Share2 } from 'lucide-react'
import { getGallery } from '@/api/content'
import { LazyImage } from '@/components/common/LazyImage'
import { EmptyState, PageHeader, Section, SectionHeading, StatusTag } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import { useApiData } from '@/hooks/useApiData'
import { containerVariants, itemVariants } from '@/lib/animations'
import { formatDate } from '@/lib/format'
import { cn } from '@/lib/utils'
import type { GalleryItem } from '@/types'

/** The album for items with neither an event nor an activity name. */
const GENERAL = 'General'
/** The type-filter entry covering every album that isn't an event. */
const OTHER_ACTIVITIES = 'Other activities'

type Album = {
  key: string
  title: string
  eventSlug: string | null
  eventType: string | null
  eventDate: string | null
  year: number
  items: GalleryItem[]
}

function typeOf(album: Album): string {
  return album.eventType ?? OTHER_ACTIVITIES
}

function isGeneral(album: Album): boolean {
  return !album.eventSlug && album.title === GENERAL
}

function buildAlbums(gallery: GalleryItem[]): Album[] {
  const byKey = new Map<string, Album>()
  for (const item of gallery) {
    const key = item.eventSlug ?? `activity:${item.album}`
    const album = byKey.get(key) ?? {
      key,
      title: item.eventTitle ?? item.album,
      eventSlug: item.eventSlug,
      eventType: item.eventType,
      eventDate: item.eventDate,
      year: item.year,
      items: [],
    }
    album.items.push(item)
    byKey.set(key, album)
  }
  // Events first (newest first, as the API sends them), then activities, then General.
  const rank = (album: Album) => (isGeneral(album) ? 2 : album.eventSlug ? 0 : 1)
  return Array.from(byKey.values()).sort((a, b) => rank(a) - rank(b))
}

function albumPath(album: Album): string {
  return album.eventSlug
    ? `/gallery?event=${encodeURIComponent(album.eventSlug)}`
    : `/gallery?activity=${encodeURIComponent(album.title)}`
}

function photosOf(album: Album): GalleryItem[] {
  return album.items.filter((item) => item.mediaType === 'image')
}

function videosOf(album: Album): GalleryItem[] {
  return album.items.filter((item) => item.mediaType === 'video')
}

function countLabel(album: Album): string {
  const photos = photosOf(album).length
  const videos = videosOf(album).length
  return [
    photos > 0 && `${photos} ${photos === 1 ? 'photo' : 'photos'}`,
    videos > 0 && `${videos} ${videos === 1 ? 'video' : 'videos'}`,
  ]
    .filter(Boolean)
    .join(' · ')
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

/** Opens the device's share sheet where there is one, and copies the link everywhere else. */
function ShareButton({ title, url, label = 'Share' }: { title: string; url: string; label?: string }) {
  const [copied, setCopied] = useState(false)

  async function share() {
    if (typeof navigator.share === 'function') {
      try {
        await navigator.share({ title, url })
        return
      } catch (error) {
        if (error instanceof DOMException && error.name === 'AbortError') return
      }
    }

    try {
      await navigator.clipboard.writeText(url)
      setCopied(true)
      window.setTimeout(() => setCopied(false), 2000)
    } catch {
      window.open(url, '_blank', 'noopener')
    }
  }

  return (
    <Button type="button" variant="outline" size="sm" onClick={share}>
      {copied ? <Check aria-hidden="true" className="h-4 w-4" /> : <Share2 aria-hidden="true" className="h-4 w-4" />}
      <span aria-live="polite">{copied ? 'Link copied' : label}</span>
    </Button>
  )
}

export default function Gallery() {
  const { data: gallery, isLoading } = useApiData(getGallery, [] as GalleryItem[])
  const [params] = useSearchParams()
  const eventSlug = params.get('event')
  const activity = params.get('activity')
  const albums = useMemo(() => buildAlbums(gallery), [gallery])

  if (!eventSlug && !activity) {
    return (
      <>
        <PageHeader
          breadcrumb={[{ label: 'Home', to: '/' }, { label: 'Gallery' }]}
          title="Gallery"
          intro="Photos and videos from chapter events and activities. Choose an album to see, download or share its pictures."
        />
        <Section>
          <AlbumGrid albums={albums} isLoading={isLoading} />
        </Section>
      </>
    )
  }

  const album = albums.find((candidate) =>
    eventSlug ? candidate.eventSlug === eventSlug : !candidate.eventSlug && candidate.title === activity,
  )
  const title = album?.title ?? activity ?? 'Album'

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'Gallery', to: '/gallery' }, { label: title }]}
        title={title}
        intro={album ? `${countLabel(album)}. Download or share the ones you want to keep.` : undefined}
      />
      <Section>
        {isLoading ? (
          <LoadingGrid />
        ) : album ? (
          <AlbumView album={album} />
        ) : (
          <EmptyState
            title="This album is empty"
            body="There are no photos or videos here yet."
            action={
              <Button variant="outline" asChild>
                <Link to="/gallery">Back to all albums</Link>
              </Button>
            }
          />
        )}
      </Section>
    </>
  )
}

function LoadingGrid() {
  return (
    <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      {Array.from({ length: 6 }).map((_, i) => (
        <Skeleton key={i} className="aspect-[4/3]" />
      ))}
    </div>
  )
}

function AlbumGrid({ albums, isLoading }: { albums: Album[]; isLoading: boolean }) {
  const [params, setParams] = useSearchParams()
  const type = params.get('type') ?? 'All'

  const types = useMemo(() => {
    const found = Array.from(new Set(albums.map(typeOf))).sort()
    return found.includes(OTHER_ACTIVITIES) ? [...found.filter((t) => t !== OTHER_ACTIVITIES), OTHER_ACTIVITIES] : found
  }, [albums])

  function selectType(next: string) {
    const merged = new URLSearchParams(params)
    if (next === 'All') merged.delete('type')
    else merged.set('type', next)
    setParams(merged)
  }

  if (isLoading) return <LoadingGrid />

  if (albums.length === 0) {
    return <EmptyState title="No albums yet" body="Photos and videos appear here once they are added to an event or activity." />
  }

  const shown = type === 'All' ? albums : albums.filter((album) => typeOf(album) === type)

  return (
    <>
      {types.length > 1 && (
        <div className="mb-8">
          <SectionHeading title="Filter by type" className="mb-4" />
          <div className="flex flex-wrap gap-2">
            {['All', ...types].map((t) => (
              <FilterChip key={t} active={type === t} onClick={() => selectType(t)}>
                {t === 'All' ? 'All albums' : t}
              </FilterChip>
            ))}
          </div>
        </div>
      )}

      {shown.length === 0 ? (
        <EmptyState title="No albums of this type" body="Choose another type to see more albums." />
      ) : (
        <motion.ul
          className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
          initial="initial"
          animate="animate"
          variants={containerVariants}
        >
          {shown.map((album) => (
            <AlbumCard key={album.key} album={album} />
          ))}
        </motion.ul>
      )}
    </>
  )
}

function AlbumCard({ album }: { album: Album }) {
  const cover = photosOf(album)[0]

  return (
    <motion.li variants={itemVariants}>
      <Link
        to={albumPath(album)}
        className="group flex h-full flex-col border border-border bg-card transition-colors hover:bg-secondary"
      >
        <div className="relative overflow-hidden bg-secondary">
          {cover ? (
            <LazyImage
              src={cover.url}
              alt=""
              className="aspect-[4/3] w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
              hoverZoom={false}
            />
          ) : (
            <div className="flex aspect-[4/3] items-center justify-center text-primary">
              <Play aria-hidden="true" className="h-12 w-12 stroke-1" />
            </div>
          )}
          <span className="tnum absolute right-2 bottom-2 inline-flex items-center gap-1.5 rounded-sm bg-plum-900 px-2 py-1 text-[0.75rem] font-semibold text-white">
            <Images aria-hidden="true" className="h-3.5 w-3.5" />
            {album.items.length}
          </span>
        </div>
        <div className="flex flex-1 flex-col p-4">
          <h2 className="text-[1.05rem] leading-snug">{album.title}</h2>
          <div className="mt-2 flex flex-wrap items-center gap-2 text-[0.8rem] text-muted-foreground">
            {album.eventType && <StatusTag tone="neutral">{album.eventType}</StatusTag>}
            {album.eventDate ? (
              <span className="tnum">{formatDate(album.eventDate)}</span>
            ) : (
              !isGeneral(album) && <span className="tnum">{album.year}</span>
            )}
          </div>
          <p className="tnum mt-auto pt-3 text-[0.82rem] text-muted-foreground">{countLabel(album)}</p>
        </div>
      </Link>
    </motion.li>
  )
}

function AlbumView({ album }: { album: Album }) {
  const [params, setParams] = useSearchParams()
  const photos = photosOf(album)
  const videos = videosOf(album)
  const tab = params.get('tab') === 'videos' || photos.length === 0 ? 'videos' : 'photos'
  const items = tab === 'videos' ? videos : photos

  function selectTab(next: 'photos' | 'videos') {
    const merged = new URLSearchParams(params)
    if (next === 'photos') merged.delete('tab')
    else merged.set('tab', next)
    setParams(merged)
  }

  return (
    <>
      <div className="flex flex-wrap items-center justify-between gap-4">
        {photos.length > 0 && videos.length > 0 ? (
          <div role="tablist" aria-label="Media type" className="inline-flex border border-border bg-card p-1">
            {(['photos', 'videos'] as const).map((t) => (
              <button
                key={t}
                type="button"
                role="tab"
                aria-selected={tab === t}
                onClick={() => selectTab(t)}
                className={cn(
                  'rounded-sm px-4 py-1.5 text-[0.88rem] font-medium transition-colors',
                  tab === t
                    ? 'bg-plum-700 text-white dark:bg-primary dark:text-primary-foreground'
                    : 'text-muted-foreground hover:text-foreground',
                )}
              >
                {t === 'photos' ? 'Photos' : 'Videos'}
                <span className="tnum ml-1.5 opacity-70">{t === 'photos' ? photos.length : videos.length}</span>
              </button>
            ))}
          </div>
        ) : (
          <span />
        )}
        <div className="flex flex-wrap items-center gap-4 text-[0.85rem] font-semibold">
          {album.eventSlug && (
            <Link to={`/events/${album.eventSlug}`} className="text-plum-700 underline-offset-4 hover:underline dark:text-primary">
              About this event
            </Link>
          )}
          <Link to="/gallery" className="text-plum-700 underline-offset-4 hover:underline dark:text-primary">
            All albums
          </Link>
          <ShareButton title={album.title} url={`${window.location.origin}${albumPath(album)}`} label="Share album" />
        </div>
      </div>

      <motion.ul
        key={tab}
        className="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
        initial="initial"
        animate="animate"
        variants={containerVariants}
      >
        {items.map((item) => (
          <motion.li key={item.id} className="flex flex-col border border-border bg-card" variants={itemVariants}>
            {item.mediaType === 'video' ? (
              <GalleryVideo item={item} title={album.title} />
            ) : (
              <LazyImage
                src={item.url}
                alt={item.caption || album.title}
                className="aspect-[4/3] w-full bg-secondary object-contain"
                hoverZoom={false}
              />
            )}
            {item.caption && <p className="px-4 pt-4 text-[0.9rem] leading-snug">{item.caption}</p>}
            <div className="mt-auto flex flex-wrap gap-2 p-4">
              {item.downloadUrl && (
                <Button variant="outline" size="sm" asChild>
                  <a href={item.downloadUrl}>
                    <Download aria-hidden="true" className="h-4 w-4" />
                    Download
                  </a>
                </Button>
              )}
              <ShareButton title={item.caption || album.title} url={item.url} />
            </div>
          </motion.li>
        ))}
      </motion.ul>
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
