import { getAnnouncements, getProgramme } from '@/api/content'
import { AnnouncementRow } from '@/components/common/Cards'
import { PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { Skeleton } from '@/components/ui/skeleton'
import { useApiData } from '@/hooks/useApiData'
import { Link } from 'react-router-dom'
import type { Announcement, ProgrammeEntry } from '@/types'

export default function Announcements() {
  const { data: announcements, isLoading: loadingAnnouncements } = useApiData(
    getAnnouncements,
    [] as Announcement[],
  )
  const { data: programme, isLoading: loadingProgramme } = useApiData(getProgramme, [] as ProgrammeEntry[])

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'Announcements' }]}
        title="Announcements"
        intro="Deadlines, circulars and notices issued to members."
      />

      <Section>
        <div className="grid gap-12 lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)]">
          <div>
            <SectionHeading title="All notices" className="mb-6" />
            {loadingAnnouncements ? (
              <div className="space-y-3">
                {Array.from({ length: 5 }).map((_, i) => (
                  <Skeleton key={i} className="h-14" />
                ))}
              </div>
            ) : (
              <ul className="divide-y divide-border border-y border-border">
                {announcements.map((a) => (
                  <AnnouncementRow key={a.id} {...a} />
                ))}
              </ul>
            )}
          </div>

          <aside>
            <SectionHeading title="Forthcoming programme" className="mb-6" />
            {loadingProgramme ? (
              <div className="space-y-3">
                {Array.from({ length: 3 }).map((_, i) => (
                  <Skeleton key={i} className="h-16" />
                ))}
              </div>
            ) : (
              <ul className="divide-y divide-border border-y border-border">
                {programme.map((p) => (
                  <li key={p.id} className="py-3.5">
                    <Link
                      to={p.href}
                      className="block font-medium leading-snug hover:text-plum-700 dark:hover:text-primary"
                    >
                      {p.name}
                    </Link>
                    <p className="tnum mt-1 text-[0.8rem] text-muted-foreground">{p.date}</p>
                    <p className="text-[0.8rem] text-muted-foreground">{p.venue}</p>
                  </li>
                ))}
              </ul>
            )}
          </aside>
        </div>
      </Section>
    </>
  )
}
