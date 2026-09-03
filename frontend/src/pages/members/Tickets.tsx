import { Link } from 'react-router-dom'
import { SectionHeading, StatusTag } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { tickets } from '@/data'
import { formatNaira, formatShortDate } from '@/lib/format'

export default function MembersTickets() {
  return (
    <div className="space-y-12">
      <div>
        <SectionHeading
          title="Event tickets"
          lede="Tickets issued to your record. Bring the reference to the registration desk, or have it open on your phone."
          action={
            <Button variant="outline" size="sm" asChild>
              <Link to="/events">Book another event</Link>
            </Button>
          }
          className="mb-6"
        />

        <ul className="grid gap-px bg-border md:grid-cols-2">
          {tickets.map((t) => (
            <li key={t.id} className="bg-card p-5">
              <div className="flex items-start justify-between gap-3">
                <h3 className="text-[1rem] leading-snug">
                  <Link
                    to={`/events/${t.eventSlug}`}
                    className="hover:text-plum-700 dark:hover:text-primary"
                  >
                    {t.eventTitle}
                  </Link>
                </h3>
                <StatusTag tone={t.status === 'Confirmed' ? 'positive' : 'warning'}>
                  {t.status}
                </StatusTag>
              </div>

              <dl className="mt-4 space-y-2 text-[0.86rem]">
                <div className="flex justify-between gap-3">
                  <dt className="text-muted-foreground">Tier</dt>
                  <dd className="text-right">{t.tier}</dd>
                </div>
                <div className="flex justify-between gap-3">
                  <dt className="text-muted-foreground">Amount</dt>
                  <dd className="tnum font-medium">{formatNaira(t.amount)}</dd>
                </div>
                <div className="flex justify-between gap-3">
                  <dt className="text-muted-foreground">Reference</dt>
                  <dd className="tnum">{t.reference}</dd>
                </div>
                <div className="flex justify-between gap-3">
                  <dt className="text-muted-foreground">Issued</dt>
                  <dd className="tnum">{formatShortDate(t.issuedAt)}</dd>
                </div>
              </dl>
            </li>
          ))}
        </ul>
      </div>
    </div>
  )
}
