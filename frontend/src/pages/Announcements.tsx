import { AnnouncementRow } from '@/components/common/Cards'
import { PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { announcements, programme } from '@/data'
import { Link } from 'react-router-dom'

export default function Announcements() {
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
            <ul className="divide-y divide-border border-y border-border">
              {announcements.map((a) => (
                <AnnouncementRow key={a.id} {...a} />
              ))}
            </ul>
          </div>

          <aside>
            <SectionHeading title="Forthcoming programme" className="mb-6" />
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
          </aside>
        </div>
      </Section>
    </>
  )
}
