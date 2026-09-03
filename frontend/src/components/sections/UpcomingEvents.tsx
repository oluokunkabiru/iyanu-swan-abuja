import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { ArrowRight } from 'lucide-react'
import { getEvents } from '@/api/content'
import { EventCard } from '@/components/EventCard'
import { Button } from '@/components/ui/button'
import type { SwanEvent } from '@/types'

export function UpcomingEvents() {
  const [events, setEvents] = useState<SwanEvent[]>([])

  useEffect(() => {
    getEvents('upcoming').then((data) => setEvents(data.slice(0, 3))).catch(() => setEvents([]))
  }, [])

  if (events.length === 0) return null

  return (
    <section className="border-t bg-muted/30">
      <div className="mx-auto max-w-6xl px-4 py-20">
        <div className="flex items-end justify-between">
          <div>
            <h2 className="font-heading text-3xl font-bold tracking-tight">Upcoming Events</h2>
            <p className="mt-2 text-muted-foreground">Seminars, outreach and community programmes</p>
          </div>
          <Button variant="ghost" className="hidden sm:inline-flex" asChild>
            <Link to="/events">
              View all <ArrowRight className="h-4 w-4" />
            </Link>
          </Button>
        </div>

        <div className="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {events.map((event, i) => (
            <EventCard key={event.id} event={event} delay={i * 100} />
          ))}
        </div>

        <div className="mt-8 text-center sm:hidden">
          <Button variant="outline" asChild>
            <Link to="/events">View all events</Link>
          </Button>
        </div>
      </div>
    </section>
  )
}
