import { useEffect, useState } from 'react'
import { CalendarX } from 'lucide-react'
import { getEvents } from '@/api/content'
import { EventCard } from '@/components/EventCard'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import type { SwanEvent } from '@/types'

function EventGridSkeleton() {
  return (
    <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      {Array.from({ length: 3 }).map((_, i) => (
        <div key={i} className="animate-pulse overflow-hidden rounded-xl border">
          <div className="h-40 w-full bg-muted" />
          <div className="space-y-3 p-6">
            <div className="h-4 w-3/4 rounded bg-muted" />
            <div className="h-3 w-1/2 rounded bg-muted" />
          </div>
        </div>
      ))}
    </div>
  )
}

function EventList({ when }: { when: 'upcoming' | 'past' }) {
  const [events, setEvents] = useState<SwanEvent[] | null>(null)

  useEffect(() => {
    setEvents(null)
    getEvents(when).then(setEvents).catch(() => setEvents([]))
  }, [when])

  if (events === null) return <EventGridSkeleton />

  if (events.length === 0) {
    return (
      <div className="flex flex-col items-center py-16 text-center text-muted-foreground">
        <CalendarX className="h-10 w-10 text-muted-foreground/50" />
        <p className="mt-3">No {when} events right now.</p>
      </div>
    )
  }

  return (
    <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      {events.map((event, i) => (
        <EventCard key={event.id} event={event} delay={i * 80} />
      ))}
    </div>
  )
}

export default function Events() {
  return (
    <section className="mx-auto max-w-6xl px-4 py-20">
      <div className="mx-auto max-w-xl text-center">
        <h1 className="font-heading text-4xl font-bold tracking-tight">Events</h1>
        <p className="mt-3 text-muted-foreground">Seminars, outreach programmes and community initiatives</p>
      </div>

      <Tabs defaultValue="upcoming" className="mt-12">
        <TabsList className="mx-auto">
          <TabsTrigger value="upcoming">Upcoming</TabsTrigger>
          <TabsTrigger value="past">Past</TabsTrigger>
        </TabsList>
        <TabsContent value="upcoming" className="mt-8">
          <EventList when="upcoming" />
        </TabsContent>
        <TabsContent value="past" className="mt-8">
          <EventList when="past" />
        </TabsContent>
      </Tabs>
    </section>
  )
}
