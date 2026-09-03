import { Link } from 'react-router-dom'
import { CalendarDays, Clock, Download, MapPin } from 'lucide-react'
import { StatusTag } from '@/components/common/Primitives'
import { dateParts, formatDate, formatNaira, formatShortDate, formatTimeRange } from '@/lib/format'
import { cn } from '@/lib/utils'
import type { ChapterEvent, NewsPost, Publication, ResourceItem, Training } from '@/types'

export function EventCard({ event, compact = false }: { event: ChapterEvent; compact?: boolean }) {
  const { day, month, year } = dateParts(event.startsAt)
  const lowest = event.ticketTiers.length
    ? Math.min(...event.ticketTiers.map((t) => t.price))
    : null

  return (
    <article className="group flex h-full flex-col border border-border bg-card">
      {event.coverUrl && !compact && (
        <Link to={`/events/${event.slug}`} className="block overflow-hidden">
          <img
            src={event.coverUrl}
            alt=""
            loading="lazy"
            className="aspect-[16/10] w-full object-cover"
          />
        </Link>
      )}

      <div className="flex flex-1 flex-col p-5">
        <div className="flex items-start gap-4">
          <div className="shrink-0 border border-gold-500/50 bg-accent px-2.5 py-1.5 text-center">
            <span className="tnum block font-heading text-xl leading-none text-accent-foreground">{day}</span>
            <span className="mt-0.5 block text-[0.68rem] font-semibold text-accent-foreground">{month}</span>
            <span className="tnum block text-[0.62rem] text-accent-foreground/70">{year}</span>
          </div>

          <div className="min-w-0">
            <div className="mb-1.5 flex flex-wrap items-center gap-2">
              <StatusTag tone="neutral">{event.category}</StatusTag>
              {event.cpdHours > 0 && (
                <StatusTag tone="gold">{event.cpdHours} CPD hours</StatusTag>
              )}
            </div>
            <h3 className="text-[1.05rem] leading-snug">
              <Link
                to={`/events/${event.slug}`}
                className="hover:text-plum-700 dark:hover:text-primary"
              >
                {event.title}
              </Link>
            </h3>
          </div>
        </div>

        <p className="mt-3 line-clamp-3 text-[0.88rem] leading-relaxed text-muted-foreground">
          {event.summary}
        </p>

        <dl className="mt-4 space-y-1.5 text-[0.82rem] text-muted-foreground">
          <div className="flex items-start gap-2">
            <dt className="sr-only">Venue</dt>
            <MapPin aria-hidden="true" className="mt-0.5 h-3.5 w-3.5 shrink-0 text-primary" />
            <dd>{event.venue}</dd>
          </div>
          <div className="flex items-start gap-2">
            <dt className="sr-only">Time</dt>
            <Clock aria-hidden="true" className="mt-0.5 h-3.5 w-3.5 shrink-0 text-primary" />
            <dd className="tnum">{formatTimeRange(event.startsAt, event.endsAt)}</dd>
          </div>
        </dl>

        <div className="mt-auto flex items-center justify-between gap-3 pt-5">
          <span className="text-[0.82rem] text-muted-foreground">
            {lowest === null
              ? 'Open to members, no charge'
              : `From ${formatNaira(lowest)}`}
          </span>
          <Link
            to={`/events/${event.slug}`}
            className="border-b border-plum-700 pb-0.5 text-[0.85rem] font-semibold text-plum-700 dark:border-primary dark:text-primary"
          >
            {event.status === 'upcoming' ? 'Register' : 'Read the record'}
          </Link>
        </div>
      </div>
    </article>
  )
}

export function NewsCard({ post, featured = false }: { post: NewsPost; featured?: boolean }) {
  return (
    <article
      className={cn(
        'flex h-full flex-col border border-border bg-card',
        featured && 'md:flex-row',
      )}
    >
      {post.coverUrl && (
        <Link
          to={`/news/${post.slug}`}
          className={cn('block overflow-hidden', featured && 'md:w-1/2 md:shrink-0')}
        >
          <img
            src={post.coverUrl}
            alt=""
            loading="lazy"
            className={cn('w-full object-cover', featured ? 'h-full min-h-56' : 'aspect-[16/9]')}
          />
        </Link>
      )}
      <div className="flex flex-1 flex-col p-5">
        <div className="flex items-center gap-3 text-[0.78rem] text-muted-foreground">
          <StatusTag tone="neutral">{post.category}</StatusTag>
          <time dateTime={post.publishedAt}>{formatDate(post.publishedAt)}</time>
        </div>
        <h3 className={cn('mt-3 leading-snug', featured ? 'text-xl' : 'text-[1.05rem]')}>
          <Link to={`/news/${post.slug}`} className="hover:text-plum-700 dark:hover:text-primary">
            {post.title}
          </Link>
        </h3>
        <p className="mt-2.5 line-clamp-3 text-[0.88rem] leading-relaxed text-muted-foreground">
          {post.excerpt}
        </p>
        <div className="mt-auto pt-4 text-[0.8rem] text-muted-foreground">{post.author}</div>
      </div>
    </article>
  )
}

export function TrainingRow({ training }: { training: Training }) {
  return (
    <tr className="border-b border-border last:border-0">
      <td className="py-3.5 pr-4 align-top">
        <span className="block font-medium leading-snug">{training.title}</span>
        <span className="mt-1 block text-[0.8rem] text-muted-foreground">
          {training.provider} · {training.deliveryMode}
        </span>
      </td>
      <td className="tnum whitespace-nowrap py-3.5 pr-4 align-top text-[0.86rem]">
        {formatShortDate(training.date)}
      </td>
      <td className="tnum py-3.5 pr-4 align-top text-[0.86rem]">{training.cpdHours}</td>
      <td className="tnum whitespace-nowrap py-3.5 pr-4 align-top text-[0.86rem]">
        <span className="font-semibold text-plum-700 dark:text-primary">
          {formatNaira(training.memberFee)}
        </span>
        <span className="ml-2 text-muted-foreground line-through">
          {formatNaira(training.fee)}
        </span>
      </td>
      <td className="py-3.5 align-top text-[0.86rem]">
        {training.seatsLeft <= 12 ? (
          <StatusTag tone="warning">{training.seatsLeft} seats left</StatusTag>
        ) : (
          <span className="tnum text-muted-foreground">{training.seatsLeft} seats</span>
        )}
      </td>
    </tr>
  )
}

export function DocumentRow({ item }: { item: Publication | ResourceItem }) {
  const isPublication = 'publishedAt' in item
  return (
    <li className="border-b border-border last:border-0">
      <a
        href={item.fileUrl}
        className="group flex items-start justify-between gap-4 py-4 hover:bg-secondary/40"
      >
        <div className="min-w-0">
          <span className="block font-medium leading-snug group-hover:text-plum-700 dark:group-hover:text-primary">
            {item.title}
          </span>
          <span className="mt-1 block text-[0.82rem] text-muted-foreground">
            {isPublication
              ? `${item.category} · ${formatDate(item.publishedAt)} · ${item.sizeLabel}`
              : item.description}
          </span>
        </div>
        <span className="flex shrink-0 items-center gap-2 pt-0.5 text-[0.78rem] font-semibold text-accent-foreground">
          {isPublication ? 'PDF' : item.format}
          <Download aria-hidden="true" className="h-4 w-4" />
        </span>
      </a>
    </li>
  )
}

export function AnnouncementRow({
  title,
  date,
  href,
  kind,
}: {
  title: string
  date: string
  href: string
  kind: 'notice' | 'circular' | 'deadline'
}) {
  const tone = kind === 'deadline' ? 'warning' : kind === 'circular' ? 'gold' : 'neutral'
  return (
    <li className="border-b border-border last:border-0">
      <Link to={href} className="group flex items-start gap-4 py-3.5">
        <CalendarDays aria-hidden="true" className="mt-1 h-4 w-4 shrink-0 text-primary" />
        <span className="min-w-0 flex-1">
          <span className="block text-[0.92rem] leading-snug group-hover:text-plum-700 dark:group-hover:text-primary">
            {title}
          </span>
          <span className="mt-1 flex items-center gap-2.5 text-[0.78rem] text-muted-foreground">
            <time dateTime={date}>{formatShortDate(date)}</time>
            <StatusTag tone={tone}>{kind}</StatusTag>
          </span>
        </span>
      </Link>
    </li>
  )
}
