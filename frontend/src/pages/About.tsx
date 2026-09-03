import { CoreValues } from '@/components/sections/CoreValues'
import { ExecutiveGrid } from '@/components/sections/ExecutiveGrid'
import { MissionVision } from '@/components/sections/MissionVision'
import { useSettings } from '@/context/SettingsContext'

export default function About() {
  const settings = useSettings()

  return (
    <>
      <section className="relative overflow-hidden border-b">
        <div
          aria-hidden
          className="pointer-events-none absolute -top-32 left-1/2 h-72 w-[36rem] -translate-x-1/2 rounded-full bg-primary/15 blur-3xl dark:bg-primary/10"
        />
        <div className="relative mx-auto max-w-3xl px-4 py-20 text-center">
          <h1 className="fade-up font-heading text-4xl font-bold tracking-tight text-balance sm:text-5xl">
            About {settings?.chapter_name ?? 'SWAN Abuja Chapter'}
          </h1>
          {settings?.tagline && (
            <p className="fade-up mt-4 text-lg text-muted-foreground [animation-delay:100ms]">{settings.tagline}</p>
          )}
        </div>
      </section>

      <MissionVision />
      <CoreValues />
      <ExecutiveGrid />
    </>
  )
}
