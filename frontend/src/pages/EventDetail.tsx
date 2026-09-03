import { useEffect, useState, type FormEvent } from 'react'
import { useParams } from 'react-router-dom'
import { CalendarDays, Check, MapPin, PartyPopper } from 'lucide-react'
import { getEvent, registerForEvent } from '@/api/content'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { useAuth } from '@/context/AuthContext'
import type { SwanEvent } from '@/types'

function formatDate(value: string) {
  return new Date(value).toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
  })
}

function EventDetailSkeleton() {
  return (
    <section className="mx-auto max-w-4xl animate-pulse px-4 py-16">
      <div className="mb-8 h-64 w-full rounded-2xl bg-muted" />
      <div className="h-9 w-2/3 rounded bg-muted" />
      <div className="mt-4 h-4 w-1/3 rounded bg-muted" />
      <div className="mt-8 h-24 w-full rounded bg-muted" />
    </section>
  )
}

export default function EventDetail() {
  const { slug } = useParams<{ slug: string }>()
  const { user } = useAuth()
  const [event, setEvent] = useState<SwanEvent | null>(null)
  const [ticketTypeId, setTicketTypeId] = useState<string>('')
  const [form, setForm] = useState({ name: user?.name ?? '', email: user?.email ?? '', phone: '', notes: '' })
  const [status, setStatus] = useState<'idle' | 'submitting' | 'success' | 'error'>('idle')

  useEffect(() => {
    if (!slug) return
    getEvent(slug).then((data) => {
      setEvent(data)
      if (data.ticket_types?.length) setTicketTypeId(String(data.ticket_types[0].id))
    })
  }, [slug])

  useEffect(() => {
    if (!user) return
    setForm((f) => ({ ...f, name: f.name || user.name, email: f.email || user.email }))
  }, [user])

  async function handleSubmit(e: FormEvent) {
    e.preventDefault()
    if (!slug || !ticketTypeId) return
    setStatus('submitting')
    try {
      await registerForEvent(slug, {
        event_ticket_type_id: Number(ticketTypeId),
        name: form.name,
        email: form.email,
        phone: form.phone || undefined,
        notes: form.notes || undefined,
      })
      setStatus('success')
    } catch {
      setStatus('error')
    }
  }

  if (!event) return <EventDetailSkeleton />

  return (
    <section className="mx-auto max-w-4xl px-4 py-16">
      <div className="fade-up mb-8 aspect-[2/1] w-full overflow-hidden rounded-2xl border shadow-lg shadow-primary/5 sm:aspect-[21/9]">
        {event.cover_url ? (
          <img src={event.cover_url} alt={event.title} className="h-full w-full object-cover" />
        ) : (
          <div className="flex h-full w-full items-center justify-center bg-gradient-to-br from-primary/25 via-accent/40 to-primary/10">
            <CalendarDays className="h-10 w-10 text-primary/60" />
          </div>
        )}
      </div>

      <h1 className="fade-up font-heading text-3xl font-bold tracking-tight text-balance sm:text-4xl">
        {event.title}
      </h1>
      <div className="fade-up mt-4 flex flex-wrap gap-x-5 gap-y-2 text-sm text-muted-foreground">
        <span className="flex items-center gap-2">
          <CalendarDays className="h-4 w-4 text-primary" /> {formatDate(event.starts_at)}
        </span>
        {event.location && (
          <span className="flex items-center gap-2">
            <MapPin className="h-4 w-4 text-primary" /> {event.location}
          </span>
        )}
      </div>
      {event.description && (
        <p className="fade-up mt-6 max-w-2xl text-muted-foreground text-pretty">{event.description}</p>
      )}

      {!!event.ticket_types?.length && (
        <Card className="fade-up mt-10 rounded-2xl">
          <CardHeader>
            <CardTitle className="font-heading">Register for this event</CardTitle>
          </CardHeader>
          <CardContent>
            {status === 'success' ? (
              <div className="flex flex-col items-center gap-3 py-6 text-center">
                <span className="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary">
                  <PartyPopper className="h-6 w-6" />
                </span>
                <p className="max-w-sm text-sm text-muted-foreground">
                  Thanks — your registration has been received. We'll confirm once payment is verified.
                </p>
              </div>
            ) : (
              <form onSubmit={handleSubmit} className="space-y-6">
                <div className="space-y-2">
                  <Label>Ticket type</Label>
                  <div className="grid gap-2.5 sm:grid-cols-2">
                    {event.ticket_types.map((ticket) => {
                      const selected = ticketTypeId === String(ticket.id)
                      return (
                        <button
                          key={ticket.id}
                          type="button"
                          onClick={() => setTicketTypeId(String(ticket.id))}
                          className={`flex items-center justify-between rounded-xl border px-4 py-3 text-left transition-all ${
                            selected
                              ? 'border-primary bg-primary/5 ring-1 ring-primary'
                              : 'hover:border-primary/40 hover:bg-accent/40'
                          }`}
                        >
                          <span className="flex items-center gap-2.5">
                            <span
                              className={`flex h-5 w-5 items-center justify-center rounded-full border-2 transition-colors ${
                                selected ? 'border-primary bg-primary text-primary-foreground' : 'border-muted-foreground/30'
                              }`}
                            >
                              {selected && <Check className="h-3 w-3" />}
                            </span>
                            {ticket.label}
                          </span>
                          <span className="font-semibold">
                            {ticket.price === 0 ? 'Free' : `₦${ticket.price.toLocaleString()}`}
                          </span>
                        </button>
                      )
                    })}
                  </div>
                </div>

                <div className="grid gap-4 sm:grid-cols-2">
                  <div className="space-y-2">
                    <Label htmlFor="name">Full name</Label>
                    <Input
                      id="name"
                      required
                      value={form.name}
                      onChange={(e) => setForm((f) => ({ ...f, name: e.target.value }))}
                    />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="email">Email</Label>
                    <Input
                      id="email"
                      type="email"
                      required
                      value={form.email}
                      onChange={(e) => setForm((f) => ({ ...f, email: e.target.value }))}
                    />
                  </div>
                  <div className="space-y-2 sm:col-span-2">
                    <Label htmlFor="phone">Phone</Label>
                    <Input
                      id="phone"
                      value={form.phone}
                      onChange={(e) => setForm((f) => ({ ...f, phone: e.target.value }))}
                    />
                  </div>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="notes">Notes (optional)</Label>
                  <Textarea
                    id="notes"
                    value={form.notes}
                    onChange={(e) => setForm((f) => ({ ...f, notes: e.target.value }))}
                  />
                </div>
                {status === 'error' && (
                  <p className="text-sm text-destructive">Something went wrong. Please try again.</p>
                )}
                <Button type="submit" size="lg" className="shadow-lg shadow-primary/25" disabled={status === 'submitting'}>
                  {status === 'submitting' ? 'Submitting…' : 'Submit registration'}
                </Button>
              </form>
            )}
          </CardContent>
        </Card>
      )}
    </section>
  )
}
