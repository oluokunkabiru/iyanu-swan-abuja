import type { ReactNode } from 'react'
import { Link } from 'react-router-dom'
import { cn } from '@/lib/utils'

/** Page-level header used at the top of every interior page. */
export function PageHeader({
  title,
  intro,
  breadcrumb,
  aside,
}: {
  title: string
  intro?: string
  breadcrumb?: { label: string; to?: string }[]
  aside?: ReactNode
}) {
  return (
    <header className="border-b border-rule bg-plum-900 text-plum-50">
      <div className="mx-auto max-w-6xl px-4 py-12 md:py-16">
        {breadcrumb && breadcrumb.length > 0 && (
          <nav aria-label="Breadcrumb" className="mb-5 text-sm text-plum-200">
            <ol className="flex flex-wrap items-center gap-x-2 gap-y-1">
              {breadcrumb.map((crumb, i) => (
                <li key={crumb.label} className="flex items-center gap-2">
                  {i > 0 && <span aria-hidden="true" className="text-gold-300/60">/</span>}
                  {crumb.to ? (
                    <Link to={crumb.to} className="underline-offset-4 hover:underline">
                      {crumb.label}
                    </Link>
                  ) : (
                    <span className="text-plum-50">{crumb.label}</span>
                  )}
                </li>
              ))}
            </ol>
          </nav>
        )}

        <div className="grid gap-6 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
          <div>
            <h1 className="max-w-3xl text-3xl leading-tight text-white md:text-[2.6rem]">
              {title}
            </h1>
            {intro && (
              <p className="mt-4 max-w-2xl text-[0.975rem] leading-relaxed text-plum-200">
                {intro}
              </p>
            )}
          </div>
          {aside && <div className="shrink-0">{aside}</div>}
        </div>
      </div>
      <div className="h-[3px] bg-gradient-to-r from-gold-500 via-gold-300 to-transparent" />
    </header>
  )
}

/**
 * Section heading with the gold-tipped rule. The rule is the site's one
 * structural signature — it marks where a new body of content begins.
 */
export function SectionHeading({
  title,
  lede,
  action,
  id,
  className,
}: {
  title: string
  lede?: string
  action?: ReactNode
  id?: string
  className?: string
}) {
  return (
    <div id={id} className={cn('scroll-mt-24', className)}>
      <div className="rule-gold" />
      <div className="mt-5 flex flex-wrap items-end justify-between gap-4">
        <div className="max-w-2xl">
          <h2 className="text-2xl leading-tight md:text-[1.75rem]">{title}</h2>
          {lede && <p className="mt-2 text-[0.95rem] leading-relaxed text-muted-foreground">{lede}</p>}
        </div>
        {action}
      </div>
    </div>
  )
}

/** Standard vertical rhythm for page sections. */
export function Section({
  children,
  className,
  tone = 'default',
}: {
  children: ReactNode
  className?: string
  tone?: 'default' | 'tinted'
}) {
  return (
    <section className={cn(tone === 'tinted' && 'bg-secondary/50', className)}>
      <div className="mx-auto max-w-6xl px-4 py-14 md:py-18">{children}</div>
    </section>
  )
}

/** A single figure with its label. Used for chapter statistics. */
export function Stat({ value, label, note }: { value: string; label: string; note?: string }) {
  return (
    <div className="border-l-2 border-gold-500 pl-4">
      <p className="tnum font-heading text-3xl leading-none text-plum-700 dark:text-primary">
        {value}
      </p>
      <p className="mt-2 text-sm font-medium">{label}</p>
      {note && <p className="mt-1 text-[0.8rem] leading-snug text-muted-foreground">{note}</p>}
    </div>
  )
}

export function EmptyState({ title, body, action }: { title: string; body: string; action?: ReactNode }) {
  return (
    <div className="border border-dashed border-rule bg-card px-6 py-12 text-center">
      <h3 className="text-lg">{title}</h3>
      <p className="mx-auto mt-2 max-w-md text-sm text-muted-foreground">{body}</p>
      {action && <div className="mt-5">{action}</div>}
    </div>
  )
}

/** Small status pill. Colour is carried by tone, never by colour alone. */
export function StatusTag({
  children,
  tone = 'neutral',
}: {
  children: ReactNode
  tone?: 'neutral' | 'gold' | 'positive' | 'warning'
}) {
  const tones = {
    neutral: 'bg-secondary text-secondary-foreground',
    gold: 'bg-accent text-accent-foreground',
    positive: 'bg-success/12 text-success',
    warning: 'bg-destructive/10 text-destructive',
  } as const

  return (
    <span
      className={cn(
        'inline-flex items-center rounded-sm px-2 py-0.5 text-[0.72rem] font-semibold tracking-tight',
        tones[tone],
      )}
    >
      {children}
    </span>
  )
}
