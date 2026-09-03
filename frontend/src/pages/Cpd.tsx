import { Link } from 'react-router-dom'
import { PageHeader, Section, SectionHeading, Stat } from '@/components/common/Primitives'
import { TrainingRow } from '@/components/common/Cards'
import { Button } from '@/components/ui/button'
import { committees, trainings, upcomingEvents } from '@/data'

export default function Cpd() {
  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'CPD' }]}
        title="Continuing professional development"
        intro="The requirement is 120 credit hours over three consecutive years. Chapter sessions are accredited, so hours earned here post to your ICAN record."
      />

      <Section>
        <div className="grid gap-8 sm:grid-cols-3">
          <Stat value="120" label="Credit hours required" note="Across three consecutive years" />
          <Stat value="40" label="Suggested annual pace" note="Keeps you clear of a year-three scramble" />
          <Stat value="1,240" label="Hours delivered by the chapter" note="Across seminars, clinics and technical sessions" />
        </div>
      </Section>

      <Section tone="tinted">
        <div className="grid gap-12 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
          <div>
            <SectionHeading title="How the requirement works" />
            <div className="mt-6 max-w-[68ch] space-y-4 text-[0.96rem] leading-relaxed">
              <p>
                CPD is counted in two kinds. Structured activity is anything delivered by an
                accredited provider with attendance recorded — the chapter seminar, a faculty
                webinar, an MPD course. Unstructured activity is professional reading, research and
                the preparation you do to teach or mentor others.
              </p>
              <p>
                The Institute expects the greater share to be structured. The chapter programme is
                built to cover that share for a member who attends the annual seminar and two or
                three other sessions a year.
              </p>
              <p>
                Register for chapter sessions using the ICAN details on your membership record.
                That is what lets the chapter post your attendance rather than leaving you to
                claim it.
              </p>
            </div>
            <Button asChild className="mt-6">
              <Link to="/members/cpd">Open your CPD record</Link>
            </Button>
          </div>

          <aside className="border border-border bg-card p-6">
            <h2 className="text-[1.05rem]">Where hours come from</h2>
            <ul className="mt-4 space-y-3 text-[0.88rem] leading-relaxed">
              <li className="border-l-2 border-gold-500 pl-4">
                <span className="font-medium">Chapter technical seminars</span> — 6 to 7 hours each,
                twice a year
              </li>
              <li className="border-l-2 border-gold-500 pl-4">
                <span className="font-medium">Faculty and MPD sessions</span> — 2 to 6 hours,
                monthly across the year
              </li>
              <li className="border-l-2 border-gold-500 pl-4">
                <span className="font-medium">Annual Accountants' Conference</span> — the single
                largest block, around 18 hours
              </li>
              <li className="border-l-2 border-gold-500 pl-4">
                <span className="font-medium">Mentoring and technical writing</span> — counts as
                unstructured hours
              </li>
            </ul>
          </aside>
        </div>
      </Section>

      <Section>
        <SectionHeading
          title="Training calendar"
          lede="Member rates apply automatically when you are signed in."
          action={
            <Button variant="outline" asChild>
              <Link to="/cpd/trainings">Full calendar</Link>
            </Button>
          }
          className="mb-8"
        />
        <div className="overflow-x-auto">
          <table className="w-full min-w-[44rem] text-left">
            <caption className="sr-only">Forthcoming training sessions</caption>
            <thead>
              <tr className="border-b border-border text-[0.75rem] font-semibold text-muted-foreground">
                <th scope="col" className="py-2.5 pr-4">Session</th>
                <th scope="col" className="py-2.5 pr-4">Date</th>
                <th scope="col" className="py-2.5 pr-4">CPD</th>
                <th scope="col" className="py-2.5 pr-4">Fee</th>
                <th scope="col" className="py-2.5">Availability</th>
              </tr>
            </thead>
            <tbody>
              {trainings.slice(0, 4).map((t) => (
                <TrainingRow key={t.id} training={t} />
              ))}
            </tbody>
          </table>
        </div>
      </Section>

      <Section tone="tinted">
        <SectionHeading
          title="Technical work at the chapter"
          lede="Committees that shape the CPD programme and the chapter's technical positions."
          className="mb-8"
        />
        <ul className="grid gap-px bg-border sm:grid-cols-2 lg:grid-cols-3">
          {committees.slice(0, 3).map((c) => (
            <li key={c.id} className="bg-card p-6">
              <h3 className="text-[1.05rem]">
                <Link to={`/committees/${c.slug}`} className="hover:text-plum-700 dark:hover:text-primary">
                  {c.name}
                </Link>
              </h3>
              <p className="mt-2 text-[0.88rem] leading-relaxed text-muted-foreground">{c.remit}</p>
            </li>
          ))}
        </ul>
        <p className="mt-6 text-[0.9rem] text-muted-foreground">
          {upcomingEvents.length} chapter events are open for registration.{' '}
          <Link to="/events" className="font-semibold text-plum-700 underline-offset-4 hover:underline dark:text-primary">
            See the events diary
          </Link>
        </p>
      </Section>
    </>
  )
}
