import { Link } from 'react-router-dom'
import { ArrowRight, Sparkles } from 'lucide-react'
import { BrandLogo } from '@/components/common/BrandLogo'
import { Button } from '@/components/ui/button'
import { useSettings } from '@/context/SettingsContext'

export function Hero() {
  const settings = useSettings()

  return (
    <section className="relative overflow-hidden border-b bg-background">
      <div className="relative mx-auto grid max-w-6xl items-center gap-12 px-4 py-20 lg:grid-cols-2 lg:py-28">
        <div className="fade-up">
          <span className="inline-flex items-center gap-1.5 rounded-full border border-primary/20 bg-primary/10 px-3 py-1 text-xs font-medium text-primary">
            <Sparkles className="h-3.5 w-3.5" />
            {settings?.tagline ?? 'Empowering the professional female accountant'}
          </span>
          <h1 className="mt-5 font-heading text-4xl leading-[1.08] font-extrabold tracking-tight text-balance sm:text-5xl lg:text-[3.25rem]">
            {settings?.chapter_name ?? 'Society of Women Accountants of Nigeria — Abuja Chapter'}
          </h1>
          {settings?.mission && (
            <p className="mt-6 max-w-xl text-lg text-muted-foreground text-pretty">{settings.mission}</p>
          )}
          <div className="mt-9 flex flex-wrap gap-3">
            <Button size="lg" className="group shadow-lg shadow-primary/25" asChild>
              <Link to="/register">
                Become a member
                <ArrowRight className="h-4 w-4 transition-transform group-hover:translate-x-0.5" />
              </Link>
            </Button>
            <Button size="lg" variant="outline" asChild>
              <Link to="/events">View upcoming events</Link>
            </Button>
          </div>
        </div>

        <div className="fade-up relative [animation-delay:150ms]">
          <div
            aria-hidden
            className="absolute -inset-4 -z-10 rounded-[2rem] border border-secondary/40"
          />
          {settings?.hero_video_url ? (
            <div className="aspect-video overflow-hidden rounded-2xl border bg-card shadow-2xl shadow-primary/10">
              <iframe
                className="h-full w-full"
                src={settings.hero_video_url}
                title="SWAN Abuja Chapter"
                allowFullScreen
              />
            </div>
          ) : (
            <div className="relative flex aspect-video flex-col items-center justify-center overflow-hidden rounded-2xl border bg-card text-center shadow-2xl shadow-primary/10">
              <span className="relative bg-white p-3 shadow-lg shadow-primary/20">
                <BrandLogo className="h-10" />
              </span>
              <span className="relative mt-4 px-8 font-heading text-lg font-semibold text-foreground">
                {settings?.chapter_name ?? 'SWAN Abuja Chapter'}
              </span>
            </div>
          )}
        </div>
      </div>
    </section>
  )
}
