import { Compass, Target } from 'lucide-react'
import { useSettings } from '@/context/SettingsContext'

export function MissionVision() {
  const settings = useSettings()

  if (!settings?.mission && !settings?.vision) return null

  return (
    <section className="mx-auto max-w-6xl px-4 py-20">
      <div className="grid gap-5 sm:grid-cols-2">
        {settings.mission && (
          <div className="rounded-2xl border bg-card p-8">
            <span className="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary">
              <Target className="h-5 w-5" />
            </span>
            <h2 className="mt-5 font-heading text-xl font-semibold">Our Mission</h2>
            <p className="mt-3 text-muted-foreground text-pretty">{settings.mission}</p>
          </div>
        )}
        {settings.vision && (
          <div className="rounded-2xl border bg-card p-8">
            <span className="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary">
              <Compass className="h-5 w-5" />
            </span>
            <h2 className="mt-5 font-heading text-xl font-semibold">Our Vision</h2>
            <p className="mt-3 text-muted-foreground text-pretty">{settings.vision}</p>
          </div>
        )}
      </div>
    </section>
  )
}
