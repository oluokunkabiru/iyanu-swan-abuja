import { Link } from 'react-router-dom'
import { ArrowRight } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { useSettings } from '@/context/SettingsContext'

export function MembershipSteps() {
  const settings = useSettings()

  const subscription = settings?.membership_subscription_fee ?? 5000
  const welfare = settings?.membership_welfare_fee ?? 12000
  const total = subscription + welfare

  const steps = [
    {
      title: 'Make payment',
      description: `Pay a total of ₦${total.toLocaleString()} (₦${subscription.toLocaleString()} subscription + ₦${welfare.toLocaleString()} welfare).`,
    },
    {
      title: 'Get confirmed',
      description: 'Our team confirms your payment and formally admits you as a member.',
    },
    {
      title: 'Attend a meeting',
      description: 'Join us at a scheduled chapter meeting to complete your induction.',
    },
  ]

  return (
    <section>
      <div className="mx-auto max-w-6xl px-4 py-20">
        <div className="mx-auto max-w-xl text-center">
          <h2 className="font-heading text-3xl font-bold tracking-tight">How to become a member</h2>
          <p className="mt-3 text-muted-foreground">Three simple steps to join SWAN Abuja Chapter</p>
        </div>

        <div className="relative mt-14 grid gap-6 sm:grid-cols-3">
          <div
            aria-hidden
            className="absolute top-5 right-[16.5%] left-[16.5%] hidden border-t border-dashed border-border sm:block"
          />
          {steps.map((step, index) => (
            <div
              key={step.title}
              className="card-hover fade-up relative rounded-2xl border bg-card p-6"
              style={{ animationDelay: `${index * 100}ms` }}
            >
              <span className="relative z-10 flex h-10 w-10 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground shadow-md shadow-primary/20">
                {index + 1}
              </span>
              <h3 className="mt-4 font-heading font-semibold">{step.title}</h3>
              <p className="mt-2 text-sm text-muted-foreground text-pretty">{step.description}</p>
            </div>
          ))}
        </div>

        <div className="mt-10 text-center">
          <Button size="lg" className="group shadow-lg shadow-primary/25" asChild>
            <Link to="/register">
              Start your membership
              <ArrowRight className="h-4 w-4 transition-transform group-hover:translate-x-0.5" />
            </Link>
          </Button>
        </div>
      </div>
    </section>
  )
}
