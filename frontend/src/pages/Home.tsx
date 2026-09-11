import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { ArrowRight, Building2, GraduationCap, Users, Wallet } from 'lucide-react'
import {
  getCoreValues,
  getEvents,
  getExecutives,
  getNews,
  getPartners,
  getPublications,
} from '@/api/content'
import { getSliders } from '@/api/sliders'
import { EventCard, NewsCard } from '@/components/common/Cards'
import { LazyImage } from '@/components/common/LazyImage'
import { Section, SectionHeading, Stat } from '@/components/common/Primitives'
import { HomeSlider } from '@/components/sections/HomeSlider'
import { Noticeboard } from '@/components/sections/Noticeboard'
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import { useSettings } from '@/context/SettingsContext'
import { homeSlides } from '@/data'
import { useApiData } from '@/hooks/useApiData'
import { formatDate } from '@/lib/format'
import type { ChapterEvent, CoreValue, ExecutiveMember, NewsPost, Partner, Publication } from '@/types'

const desks = [
  {
    icon: Users,
    title: 'Membership',
    body: 'Eligibility, dues, and what an active membership gets you.',
    to: '/membership',
  },
  {
    icon: GraduationCap,
    title: 'CPD and training',
    body: '120 hours across three years, tracked against your record.',
    to: '/cpd',
  },
  {
    icon: Building2,
    title: 'Directories',
    body: 'Search members by sector, or find a registered firm.',
    to: '/directory',
  },
  {
    icon: Wallet,
    title: 'Pay your dues',
    body: 'Subscription and welfare levy, card, transfer or USSD.',
    to: '/members/subscription',
  },
]

export default function Home() {
  const [slides, setSlides] = useState(homeSlides)
  const { settings, isLoading: loadingSettings } = useSettings()
  const { data: executives, isLoading: loadingExecutives } = useApiData(
    getExecutives,
    [] as ExecutiveMember[],
  )
  const { data: coreValues, isLoading: loadingCoreValues } = useApiData(getCoreValues, [] as CoreValue[])
  const { data: publications, isLoading: loadingPublications } = useApiData(
    getPublications,
    [] as Publication[],
  )
  const { data: upcomingEvents, isLoading: loadingEvents } = useApiData(
    () => getEvents('upcoming'),
    [] as ChapterEvent[],
  )
  const { data: news, isLoading: loadingNews } = useApiData(getNews, [] as NewsPost[])
  const { data: partners, isLoading: loadingPartners } = useApiData(getPartners, [] as Partner[])

  const chairperson = executives[0]
  const registrationSteps = settings?.registrationSteps ?? []
  const featuredEvents = upcomingEvents.filter((e) => e.isFeatured)
  const homepageEvents = (featuredEvents.length > 0 ? featuredEvents : upcomingEvents).slice(0, 3)

  useEffect(() => {
    const controller = new AbortController()

    getSliders(controller.signal)
      .then((managedSlides) => {
        if (managedSlides.length > 0) setSlides(managedSlides)
      })
      .catch(() => {
        // Keep the local slides when the API is unavailable or has no published slides.
      })

    return () => controller.abort()
  }, [])

  return (
    <>
      <HomeSlider slides={slides} />

      {/* The slider intentionally leads into the chapter chairperson's welcome. */}
      <Section>
        {loadingExecutives || loadingSettings ? (
          <div className="grid items-center gap-10 lg:grid-cols-[minmax(17rem,0.72fr)_minmax(0,1.28fr)] lg:gap-16">
            <Skeleton className="aspect-[4/5] w-full max-w-sm" />
            <div className="space-y-4">
              <Skeleton className="h-8 w-2/3" />
              <Skeleton className="h-24 w-full" />
            </div>
          </div>
        ) : (
          <div className="grid items-center gap-10 lg:grid-cols-[minmax(17rem,0.72fr)_minmax(0,1.28fr)] lg:gap-16">
            <div className="relative mx-auto w-full max-w-sm border border-border bg-secondary p-3 lg:mx-0">
              {chairperson?.photoUrl && (
                <LazyImage
                  src={chairperson.photoUrl}
                  alt={`${chairperson.name}, ${chairperson.position}`}
                  loading="eager"
                  className="aspect-[4/5] w-full object-cover object-top"
                />
              )}
              <div className="absolute right-0 bottom-0 bg-plum-900 px-4 py-3 text-right text-white">
                <p className="text-[0.88rem] font-semibold">
                  {chairperson?.name}, {chairperson?.credential}
                </p>
                <p className="mt-0.5 text-[0.72rem] text-gold-300">Chapter Chairperson</p>
              </div>
            </div>

            <div>
              <SectionHeading
                title={settings?.chairpersonWelcome?.heading ?? 'A word from the chapter'}
                lede="A message from the Chairperson"
                className="mb-7"
              />
              <div className="space-y-4 text-[0.98rem] leading-relaxed text-muted-foreground">
                {(settings?.chairpersonWelcome?.paragraphs ?? []).map((paragraph) => (
                  <p key={paragraph}>{paragraph}</p>
                ))}
              </div>
              <p className="mt-7 border-l-2 border-gold-500 pl-4 text-[0.9rem] font-semibold text-foreground">
                {chairperson?.name}, {chairperson?.credential}
                <span className="mt-1 block text-[0.78rem] font-normal text-muted-foreground">
                  Chairperson, SWAN Abuja Chapter
                </span>
              </p>
            </div>
          </div>
        )}
      </Section>

      {/* Vision, mission, values */}
      <Section tone="tinted">
        <div className="grid gap-12 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
          <div>
            <SectionHeading title="What we stand for" lede={settings?.aims} />
            <div className="mt-8 space-y-6">
              <div>
                <h3 className="text-[1.05rem] text-plum-700 dark:text-primary">Our vision</h3>
                <p className="mt-2 text-[0.95rem] leading-relaxed">{settings?.vision}</p>
              </div>
              <div>
                <h3 className="text-[1.05rem] text-plum-700 dark:text-primary">Our mission</h3>
                <ul className="mt-2 space-y-2.5">
                  {(settings?.mission ?? []).map((m) => (
                    <li key={m} className="border-l-2 border-gold-500 pl-4 text-[0.95rem] leading-relaxed">
                      {m}
                    </li>
                  ))}
                </ul>
              </div>
            </div>
            <Button variant="outline" asChild className="mt-8">
              <Link to="/about">More about the chapter</Link>
            </Button>
          </div>

          <div>
            <SectionHeading title="Core values" lede="Five commitments the chapter is measured against." />
            {loadingCoreValues ? (
              <div className="mt-8 space-y-3">
                {Array.from({ length: 5 }).map((_, i) => (
                  <Skeleton key={i} className="h-12" />
                ))}
              </div>
            ) : (
              <dl className="mt-8 divide-y divide-border border-y border-border">
                {coreValues.map((v) => (
                  <div key={v.title} className="grid gap-1.5 py-4 sm:grid-cols-[8rem_minmax(0,1fr)] sm:gap-5">
                    <dt className="font-heading text-[1.02rem] text-accent-foreground">{v.title}</dt>
                    <dd className="text-[0.9rem] leading-relaxed text-muted-foreground">
                      {v.description}
                    </dd>
                  </div>
                ))}
              </dl>
            )}
          </div>
        </div>
      </Section>

      {/* Quick desks */}
      <section className="border-b border-border bg-card">
        <div className="mx-auto grid max-w-6xl gap-px bg-border px-4 md:grid-cols-2 lg:grid-cols-4">
          {desks.map((desk) => (
            <Link
              key={desk.title}
              to={desk.to}
              className="group bg-card p-6 transition-colors hover:bg-secondary"
            >
              <desk.icon aria-hidden="true" className="h-6 w-6 text-primary" />
              <h2 className="mt-4 text-[1.05rem]">{desk.title}</h2>
              <p className="mt-1.5 text-[0.85rem] leading-relaxed text-muted-foreground">
                {desk.body}
              </p>
              <span className="mt-3 inline-flex items-center gap-1.5 text-[0.82rem] font-semibold text-plum-700 dark:text-primary">
                Open
                <ArrowRight
                  aria-hidden="true"
                  className="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5"
                />
              </span>
            </Link>
          ))}
        </div>
      </section>
      {/* Chapter figures */}
      <Section>
        {loadingSettings ? (
          <div className="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            {Array.from({ length: 4 }).map((_, i) => (
              <Skeleton key={i} className="h-16" />
            ))}
          </div>
        ) : (
          <div className="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            {(settings?.chapterStats ?? []).map((s) => (
              <Stat key={s.label} value={s.value} label={s.label} note={s.note} />
            ))}
          </div>
        )}
      </Section>

      {/* Noticeboard + publications */}
      <Section>
        <div className="grid gap-12 lg:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)]">
          <div>
            <SectionHeading
              title="Noticeboard"
              lede="Deadlines, circulars and what the chapter has published lately."
              className="mb-6"
            />
            <Noticeboard />
          </div>

          <div>
            <SectionHeading title="From the library" lede="Communiqués, bulletins and reports." className="mb-6" />
            {loadingPublications ? (
              <div className="space-y-3">
                {Array.from({ length: 5 }).map((_, i) => (
                  <Skeleton key={i} className="h-10" />
                ))}
              </div>
            ) : (
              <ul className="divide-y divide-border border-y border-border">
                {publications.slice(0, 5).map((p) => (
                  <li key={p.id}>
                    <a href={p.fileUrl} className="group block py-3.5">
                      <span className="block text-[0.9rem] leading-snug group-hover:text-plum-700 dark:group-hover:text-primary">
                        {p.title}
                      </span>
                      <span className="mt-1 block text-[0.78rem] text-muted-foreground">
                        {p.category} · {formatDate(p.publishedAt)} · {p.sizeLabel}
                      </span>
                    </a>
                  </li>
                ))}
              </ul>
            )}
            <Link
              to="/publications"
              className="mt-4 inline-block border-b border-plum-700 pb-0.5 text-[0.85rem] font-semibold text-plum-700 dark:border-primary dark:text-primary"
            >
              Full publications library
            </Link>
          </div>
        </div>
      </Section>

      {/* Featured events */}
      <Section tone="tinted">
        <SectionHeading
          title="Featured events"
          lede="Technical seminars, outreach and the chapter meeting calendar."
          action={
            <Button variant="outline" asChild>
              <Link to="/events">All events</Link>
            </Button>
          }
          className="mb-8"
        />
        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          {loadingEvents
            ? Array.from({ length: 3 }).map((_, i) => <Skeleton key={i} className="h-64" />)
            : homepageEvents.map((e) => <EventCard key={e.id} event={e} />)}
        </div>
      </Section>

      {/* Registration — a genuine three-step sequence, so numbered. */}
      <Section>
        <SectionHeading
          title="Becoming a member"
          lede="Three steps. Most members complete the whole thing inside a fortnight."
          className="mb-10"
        />
        {loadingSettings ? (
          <div className="grid gap-px bg-border md:grid-cols-3">
            {Array.from({ length: 3 }).map((_, i) => (
              <Skeleton key={i} className="h-40" />
            ))}
          </div>
        ) : (
          <ol className="grid gap-px bg-border md:grid-cols-3">
            {registrationSteps.map((s) => (
              <li key={s.step} className="bg-card p-6">
                <span className="tnum font-heading text-3xl leading-none text-plum-200 dark:text-plum-500">
                  {String(s.step).padStart(2, '0')}
                </span>
                <h3 className="mt-4 text-[1.1rem]">{s.title}</h3>
                <p className="mt-2 text-[0.9rem] leading-relaxed text-muted-foreground">
                  {s.description}
                </p>
              </li>
            ))}
          </ol>
        )}
        <div className="mt-8 flex flex-wrap gap-3">
          <Button asChild>
            <Link to="/membership/register">Start your registration</Link>
          </Button>
          <Button variant="outline" asChild>
            <Link to="/membership">Read about membership</Link>
          </Button>
        </div>
      </Section>

      {/* Council */}
      <Section>
        <SectionHeading
          title="The chapter council"
          lede="The executive committee elected to lead SWAN Abuja this session."
          action={
            <Button variant="outline" asChild>
              <Link to="/governance">Full council and governance</Link>
            </Button>
          }
          className="mb-8"
        />
        {loadingExecutives ? (
          <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-5">
            {Array.from({ length: 5 }).map((_, i) => (
              <Skeleton key={i} className="aspect-square" />
            ))}
          </div>
        ) : (
          <ul className="grid gap-6 sm:grid-cols-2 lg:grid-cols-5">
            {executives
              .filter((e) => e.isPrincipal)
              .map((exec) => (
                <li key={exec.id} className="border border-border bg-card">
                  {exec.photoUrl && (
                    <LazyImage
                      src={exec.photoUrl}
                      alt=""
                      className="aspect-square w-full object-cover object-top"
                    />
                  )}
                  <div className="p-4">
                    <h3 className="text-[0.98rem] leading-snug">
                      {exec.name}, {exec.credential}
                    </h3>
                    <p className="mt-1 text-[0.8rem] text-accent-foreground">{exec.position}</p>
                  </div>
                </li>
              ))}
          </ul>
        )}
      </Section>

      {/* News */}
      <Section tone="tinted">
        <SectionHeading
          title="Chapter news"
          action={
            <Button variant="outline" asChild>
              <Link to="/news">All news</Link>
            </Button>
          }
          className="mb-8"
        />
        <div className="grid gap-6 lg:grid-cols-3">
          {loadingNews
            ? Array.from({ length: 3 }).map((_, i) => <Skeleton key={i} className="h-64" />)
            : news.slice(0, 3).map((post) => <NewsCard key={post.id} post={post} />)}
        </div>
      </Section>

      {/* Affiliates */}
      <Section>
        <SectionHeading
          title="Affiliates and professional bodies"
          lede="The chapter works within a wider network of accountancy institutions."
          className="mb-8"
        />
        {loadingPartners ? (
          <div className="flex flex-wrap items-center justify-center gap-x-12 gap-y-6">
            {Array.from({ length: 4 }).map((_, i) => (
              <Skeleton key={i} className="h-10 w-28" />
            ))}
          </div>
        ) : (
          <div className="flex flex-wrap items-center justify-center gap-x-12 gap-y-6">
            {partners.map((p) => {
              const content = p.logoUrl ? (
                <img
                  src={p.logoUrl}
                  alt={p.name}
                  className="h-10 object-contain grayscale transition-all duration-300 hover:grayscale-0"
                />
              ) : (
                <span className="font-heading text-lg font-semibold text-muted-foreground transition-colors hover:text-foreground">
                  {p.name}
                </span>
              )

              return p.url ? (
                <a key={p.id} href={p.url} target="_blank" rel="noreferrer">
                  {content}
                </a>
              ) : (
                <span key={p.id}>{content}</span>
              )
            })}
          </div>
        )}
      </Section>

      {/* Contact */}
      <section className="border-t border-rule bg-plum-900 text-plum-200">
        <div className="mx-auto flex max-w-6xl flex-col gap-6 px-4 py-14 md:flex-row md:items-center md:justify-between">
          <div>
            <h2 className="font-heading text-2xl text-white">Something you cannot find?</h2>
            <p className="mt-2 max-w-xl text-[0.95rem] leading-relaxed">
              The General Secretary handles chapter correspondence, and the Financial Secretary
              handles anything to do with dues and confirmations.
            </p>
          </div>
          <div className="flex flex-wrap gap-3">
            <Button size="lg" asChild>
              <Link to="/contact">Contact the chapter</Link>
            </Button>
            <Button
              size="lg"
              variant="outline"
              asChild
              className="border-plum-200/40 bg-transparent text-white hover:bg-plum-800 hover:text-white"
            >
              <Link to="/faqs">Read the FAQs</Link>
            </Button>
          </div>
        </div>
      </section>
    </>
  )
}
