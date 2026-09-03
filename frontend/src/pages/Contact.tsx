import { useState, type FormEvent } from 'react'
import { CheckCircle2, Mail, MapPin, Phone } from 'lucide-react'
import { submitContact } from '@/api/content'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { useSettings } from '@/context/SettingsContext'

export default function Contact() {
  const settings = useSettings()
  const [form, setForm] = useState({ name: '', email: '', phone: '', subject: '', message: '' })
  const [status, setStatus] = useState<'idle' | 'submitting' | 'success' | 'error'>('idle')

  async function handleSubmit(e: FormEvent) {
    e.preventDefault()
    setStatus('submitting')
    try {
      await submitContact({
        name: form.name,
        email: form.email,
        phone: form.phone || undefined,
        subject: form.subject || undefined,
        message: form.message,
      })
      setStatus('success')
      setForm({ name: '', email: '', phone: '', subject: '', message: '' })
    } catch {
      setStatus('error')
    }
  }

  const contactItems = [
    settings?.address && { icon: MapPin, label: 'Address', value: settings.address },
    settings?.phone && { icon: Phone, label: 'Phone', value: settings.phone },
    settings?.email && { icon: Mail, label: 'Email', value: settings.email },
  ].filter(Boolean) as { icon: typeof MapPin; label: string; value: string }[]

  return (
    <section className="mx-auto max-w-5xl px-4 py-20">
      <div className="mx-auto max-w-xl text-center">
        <h1 className="font-heading text-4xl font-bold tracking-tight">Contact Us</h1>
        <p className="mt-3 text-muted-foreground">We'd love to hear from you — reach out any time</p>
      </div>

      <div className="mt-14 grid gap-10 lg:grid-cols-5">
        <div className="fade-up space-y-4 lg:col-span-2">
          {contactItems.map((item) => (
            <div key={item.label} className="flex items-start gap-4 rounded-2xl border bg-card p-5">
              <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
                <item.icon className="h-5 w-5" />
              </span>
              <div>
                <p className="text-xs font-medium text-muted-foreground uppercase">{item.label}</p>
                <p className="mt-0.5 font-medium">{item.value}</p>
              </div>
            </div>
          ))}
        </div>

        <div className="fade-up rounded-2xl border bg-card p-6 shadow-sm lg:col-span-3">
          {status === 'success' ? (
            <div className="flex flex-col items-center gap-3 py-10 text-center">
              <span className="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary">
                <CheckCircle2 className="h-6 w-6" />
              </span>
              <p className="font-heading text-lg font-semibold">Message sent</p>
              <p className="max-w-sm text-sm text-muted-foreground">We'll get back to you as soon as possible.</p>
              <Button variant="outline" className="mt-2" onClick={() => setStatus('idle')}>
                Send another message
              </Button>
            </div>
          ) : (
            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="name">Name</Label>
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
                <div className="space-y-2">
                  <Label htmlFor="phone">Phone</Label>
                  <Input
                    id="phone"
                    value={form.phone}
                    onChange={(e) => setForm((f) => ({ ...f, phone: e.target.value }))}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="subject">Subject</Label>
                  <Input
                    id="subject"
                    value={form.subject}
                    onChange={(e) => setForm((f) => ({ ...f, subject: e.target.value }))}
                  />
                </div>
              </div>
              <div className="space-y-2">
                <Label htmlFor="message">Message</Label>
                <Textarea
                  id="message"
                  required
                  rows={5}
                  value={form.message}
                  onChange={(e) => setForm((f) => ({ ...f, message: e.target.value }))}
                />
              </div>
              {status === 'error' && (
                <p className="text-sm text-destructive">Something went wrong. Please try again.</p>
              )}
              <Button type="submit" size="lg" className="shadow-lg shadow-primary/25" disabled={status === 'submitting'}>
                {status === 'submitting' ? 'Sending…' : 'Send message'}
              </Button>
            </form>
          )}
        </div>
      </div>
    </section>
  )
}
