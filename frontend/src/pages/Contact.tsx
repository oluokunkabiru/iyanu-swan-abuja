import { useState, type FormEvent } from 'react'
import { Mail, MapPin, Phone } from 'lucide-react'
import { PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { site } from '@/data'

const routes = [
  { office: 'General Secretary', handles: 'Correspondence, meetings, committee service and anything not listed below.' },
  { office: 'Financial Secretary', handles: 'Dues, payment confirmations, receipts and membership status queries.' },
  { office: 'Membership Secretary', handles: 'Joining the chapter, directory entries and the mentorship programme.' },
  { office: 'Publicity Officer', handles: 'Media enquiries, event coverage and anything about this website.' },
]

export default function Contact() {
  const [sent, setSent] = useState(false)
  const [error, setError] = useState<string | null>(null)

  function handleSubmit(e: FormEvent<HTMLFormElement>) {
    e.preventDefault()
    const form = new FormData(e.currentTarget)
    if (!String(form.get('name')).trim() || !String(form.get('message')).trim()) {
      setError('Enter your name and a message so the chapter knows who to reply to.')
      return
    }
    setError(null)
    setSent(true)
  }

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'Contact' }]}
        title="Contact the chapter"
        intro="Route your message to the right office and you will get a faster answer."
      />

      <Section>
        <div className="grid gap-12 lg:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)]">
          <div>
            <SectionHeading title="Send a message" className="mb-6" />

            {sent ? (
              <div className="border-l-2 border-success bg-success/8 px-5 py-6">
                <h3 className="text-[1.05rem]">Message ready to send</h3>
                <p className="mt-2 max-w-[60ch] text-[0.92rem] leading-relaxed text-muted-foreground">
                  This build has no mail service connected, so nothing has actually been
                  transmitted. Wire the form to your backend or a form service and this is where the
                  confirmation will appear.
                </p>
                <Button variant="outline" className="mt-5" onClick={() => setSent(false)}>
                  Write another message
                </Button>
              </div>
            ) : (
              <form onSubmit={handleSubmit} className="space-y-5">
                <div className="grid gap-5 sm:grid-cols-2">
                  <div className="space-y-2">
                    <Label htmlFor="name">Your name</Label>
                    <Input id="name" name="name" autoComplete="name" />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="email">Email address</Label>
                    <Input id="email" name="email" type="email" autoComplete="email" />
                  </div>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="subject">Subject</Label>
                  <Input id="subject" name="subject" placeholder="What is this about?" />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="message">Message</Label>
                  <Textarea id="message" name="message" rows={6} />
                </div>

                {error && (
                  <p role="alert" className="border-l-2 border-destructive bg-destructive/8 px-4 py-3 text-[0.88rem] text-destructive">
                    {error}
                  </p>
                )}

                <Button type="submit" size="lg">
                  Send message
                </Button>
              </form>
            )}
          </div>

          <aside className="space-y-6">
            <div className="border border-border bg-card p-6">
              <h2 className="text-[1.05rem]">Chapter contact</h2>
              <ul className="mt-4 space-y-3 text-[0.9rem]">
                <li className="flex gap-3">
                  <MapPin aria-hidden="true" className="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                  <span>{site.address}</span>
                </li>
                <li className="flex gap-3">
                  <Mail aria-hidden="true" className="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                  <a href={`mailto:${site.email}`} className="underline-offset-4 hover:underline">
                    {site.email}
                  </a>
                </li>
                <li className="flex gap-3">
                  <Phone aria-hidden="true" className="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                  <a href={`tel:${site.phone.replace(/\s/g, '')}`} className="underline-offset-4 hover:underline">
                    {site.phone}
                  </a>
                </li>
              </ul>
            </div>

            <div className="border border-border bg-card p-6">
              <h2 className="text-[1.05rem]">Who handles what</h2>
              <dl className="mt-4 divide-y divide-border border-y border-border">
                {routes.map((r) => (
                  <div key={r.office} className="py-3">
                    <dt className="text-[0.9rem] font-medium">{r.office}</dt>
                    <dd className="mt-1 text-[0.84rem] leading-relaxed text-muted-foreground">
                      {r.handles}
                    </dd>
                  </div>
                ))}
              </dl>
            </div>
          </aside>
        </div>
      </Section>
    </>
  )
}
