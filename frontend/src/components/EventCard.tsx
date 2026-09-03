import { Link } from 'react-router-dom'
import { CalendarDays, MapPin } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import type { SwanEvent } from '@/types'

function formatDate(value: string) {
  return new Date(value).toLocaleDateString(undefined, {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  })
}

export function EventCard({ event, delay = 0 }: { event: SwanEvent; delay?: number }) {
  const cheapest = event.ticket_types?.length
    ? Math.min(...event.ticket_types.map((t) => t.price))
    : null

  return (
    <Card className="card-hover fade-up overflow-hidden py-0" style={{ animationDelay: `${delay}ms` }}>
      <div className="relative h-40 w-full">
        {event.cover_url ? (
          <img src={event.cover_url} alt={event.title} className="h-full w-full object-cover" />
        ) : (
          <div className="flex h-full w-full items-center justify-center bg-gradient-to-br from-primary/25 via-accent/40 to-primary/10">
            <CalendarDays className="h-8 w-8 text-primary/60" />
          </div>
        )}
        <span className="absolute top-3 left-3 rounded-full bg-background/90 px-2.5 py-1 text-xs font-semibold shadow-sm backdrop-blur">
          {formatDate(event.starts_at)}
        </span>
      </div>
      <CardHeader>
        <CardTitle className="font-heading text-lg leading-snug">{event.title}</CardTitle>
      </CardHeader>
      <CardContent className="space-y-2 text-sm text-muted-foreground">
        {event.location && (
          <div className="flex items-center gap-2">
            <MapPin className="h-4 w-4 shrink-0" /> {event.location}
          </div>
        )}
      </CardContent>
      <CardFooter className="flex items-center justify-between pb-6">
        <span className="text-sm font-semibold text-primary">
          {cheapest !== null ? (cheapest === 0 ? 'Free' : `From ₦${cheapest.toLocaleString()}`) : ''}
        </span>
        <Button size="sm" variant="outline" asChild>
          <Link to={`/events/${event.slug}`}>Details</Link>
        </Button>
      </CardFooter>
    </Card>
  )
}
