import { useEffect, useState } from 'react'
import { Heart, ShieldCheck, Sparkles, Target, Trophy, type LucideIcon } from 'lucide-react'
import { getCoreValues } from '@/api/content'
import type { CoreValue } from '@/types'

const ICONS: Record<string, LucideIcon> = {
  integrity: ShieldCheck,
  professionalism: Trophy,
  passion: Heart,
  impact: Target,
  accountability: ShieldCheck,
}

function iconFor(title: string): LucideIcon {
  return ICONS[title.trim().toLowerCase()] ?? Sparkles
}

export function CoreValues() {
  const [values, setValues] = useState<CoreValue[]>([])

  useEffect(() => {
    getCoreValues().then(setValues).catch(() => setValues([]))
  }, [])

  if (values.length === 0) return null

  return (
    <section className="border-t bg-muted/30">
      <div className="mx-auto max-w-6xl px-4 py-20">
        <div className="mx-auto max-w-xl text-center">
          <h2 className="font-heading text-3xl font-bold tracking-tight">Our Core Values</h2>
          <p className="mt-3 text-muted-foreground">The principles that guide everything we do as a chapter.</p>
        </div>
        <div className="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-5">
          {values.map((value, i) => {
            const Icon = iconFor(value.title)
            return (
              <div
                key={value.id}
                className="card-hover fade-up rounded-2xl border bg-card p-6 text-center"
                style={{ animationDelay: `${i * 80}ms` }}
              >
                <span className="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary">
                  <Icon className="h-5 w-5" />
                </span>
                <h3 className="mt-4 font-heading font-semibold">{value.title}</h3>
                {value.description && (
                  <p className="mt-2 text-sm text-muted-foreground text-pretty">{value.description}</p>
                )}
              </div>
            )
          })}
        </div>
      </div>
    </section>
  )
}
