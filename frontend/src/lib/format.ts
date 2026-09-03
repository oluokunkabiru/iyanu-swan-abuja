/** Shared formatters. Naira and dates are used everywhere, so they live here. */

const naira = new Intl.NumberFormat('en-NG', {
  style: 'currency',
  currency: 'NGN',
  maximumFractionDigits: 0,
})

export function formatNaira(value: number): string {
  return naira.format(value)
}

export function formatDate(value: string, opts?: Intl.DateTimeFormatOptions): string {
  return new Date(value).toLocaleDateString('en-NG', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    ...opts,
  })
}

export function formatShortDate(value: string): string {
  return new Date(value).toLocaleDateString('en-NG', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  })
}

export function formatTimeRange(startsAt: string, endsAt: string | null): string {
  const opts: Intl.DateTimeFormatOptions = { hour: 'numeric', minute: '2-digit', hour12: true }
  const start = new Date(startsAt).toLocaleTimeString('en-NG', opts)
  if (!endsAt) return start
  return `${start} – ${new Date(endsAt).toLocaleTimeString('en-NG', opts)}`
}

/** Splits a date into parts for the calendar chip used on event cards. */
export function dateParts(value: string): { day: string; month: string; year: string } {
  const d = new Date(value)
  return {
    day: d.toLocaleDateString('en-NG', { day: '2-digit' }),
    month: d.toLocaleDateString('en-NG', { month: 'short' }),
    year: String(d.getFullYear()),
  }
}
