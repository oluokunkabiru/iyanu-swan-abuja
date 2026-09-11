import { Link } from 'react-router-dom'
import { PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'

const pathway = [
  {
    stage: 'Registration',
    detail:
      'Register with ICAN as a student — either on the ATSWA route or directly at professional level, depending on your qualifications.',
  },
  {
    stage: 'Exemptions',
    detail:
      'Accredited degrees and recognised professional qualifications carry exemptions. Confirm the correct amount before you pay; exemption payments are not refunded.',
  },
  {
    stage: 'Examinations',
    detail:
      'Foundation, Skills and Professional levels. Study texts and past questions are published by the Institute for each syllabus.',
  },
  {
    stage: 'Induction',
    detail:
      'On passing and meeting the experience requirement you are inducted as an Associate Chartered Accountant, and your SWAN membership begins the same day.',
  },
]

export default function Students() {
  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'Students' }]}
        title="Students and prospective members"
        intro="If you are working toward the ICAN qualification, the chapter is already interested in you. Open lectures, career seminars and the newly inducted clinic are all available before you qualify."
      />

      <Section>
        <div className="grid gap-12 lg:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)]">
          <div>
            <SectionHeading title="The route to membership" />
            <ol className="mt-6 divide-y divide-border border-y border-border">
              {pathway.map((p, i) => (
                <li key={p.stage} className="flex gap-5 py-4">
                  <span className="tnum shrink-0 font-heading text-2xl leading-none text-plum-200 dark:text-plum-500">
                    {String(i + 1).padStart(2, '0')}
                  </span>
                  <div>
                    <h3 className="text-[1.05rem]">{p.stage}</h3>
                    <p className="mt-1.5 text-[0.9rem] leading-relaxed text-muted-foreground">
                      {p.detail}
                    </p>
                  </div>
                </li>
              ))}
            </ol>
            <Button variant="outline" asChild className="mt-6">
              <Link to="/about#ican">About the chapter and ICAN</Link>
            </Button>
          </div>

          <aside className="space-y-6">
            <div className="border border-border bg-card p-6">
              <h2 className="text-[1.05rem]">What the chapter offers before you qualify</h2>
              <ul className="mt-4 space-y-2.5 text-[0.88rem] leading-relaxed text-muted-foreground">
                <li className="border-l-2 border-gold-500 pl-4">
                  Open lectures and career seminars at the non-member rate
                </li>
                <li className="border-l-2 border-gold-500 pl-4">
                  Study support sessions run by members in academia
                </li>
                <li className="border-l-2 border-gold-500 pl-4">
                  The chapter job board, which carries entry-level roles
                </li>
                <li className="border-l-2 border-gold-500 pl-4">
                  Introductions to members in the sector you are aiming at
                </li>
              </ul>
            </div>

            <div className="border border-border bg-card p-6">
              <h2 className="text-[1.05rem]">The day you are inducted</h2>
              <p className="mt-3 text-[0.9rem] leading-relaxed text-muted-foreground">
                Your SWAN membership begins automatically. Pay the chapter dues and attend a
                meeting to go onto the active roll, then join the next mentorship cohort.
              </p>
              <Button asChild className="mt-5 w-full">
                <Link to="/membership">Read about membership</Link>
              </Button>
            </div>
          </aside>
        </div>
      </Section>
    </>
  )
}
