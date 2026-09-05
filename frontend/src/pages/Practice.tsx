import { Link } from 'react-router-dom'
import { getFirms, getResources } from '@/api/content'
import { PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import { useApiData } from '@/hooks/useApiData'
import type { Firm, ResourceItem } from '@/types'

export default function Practice() {
  const { data: firms, isLoading: loadingFirms } = useApiData(getFirms, [] as Firm[])
  const { data: resources, isLoading: loadingResources } = useApiData(getResources, [] as ResourceItem[])
  const guides = resources.filter((r) => r.category === 'Guide' || r.category === 'Form')

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'Women in practice' }]}
        title="Women in practice"
        intro="A chapter survey of 180 members found that licensing, not technical confidence, is where most women stall on the way to founding a firm. This is what the chapter does about it."
      />

      <Section>
        <div className="grid gap-12 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
          <div>
            <SectionHeading title="What the survey found" />
            <div className="mt-6 max-w-[68ch] space-y-4 text-[0.96rem] leading-relaxed">
              <p>
                Respondents were near-unanimous on technical readiness. The obstacles they named
                were the practice attachment requirement, the cost of the licence in the first two
                years, and the absence of a clear route back after a career break.
              </p>
              <p>The chapter took three actions in response.</p>
            </div>
            <ol className="mt-6 divide-y divide-border border-y border-border">
              <li className="py-4">
                <h3 className="text-[1.02rem]">A standing licensing clinic</h3>
                <p className="mt-1.5 text-[0.9rem] leading-relaxed text-muted-foreground">
                  Held quarterly by the Women in Practice committee. Bring your application and
                  leave with it checked.
                </p>
              </li>
              <li className="py-4">
                <h3 className="text-[1.02rem]">A practice attachment register</h3>
                <p className="mt-1.5 text-[0.9rem] leading-relaxed text-muted-foreground">
                  Members with licensed firms who are willing to host an attachment list themselves
                  here, so candidates are not cold-calling for a placement.
                </p>
              </li>
              <li className="py-4">
                <h3 className="text-[1.02rem]">A submission on re-entry</h3>
                <p className="mt-1.5 text-[0.9rem] leading-relaxed text-muted-foreground">
                  Made to the Professional Practice directorate on how members returning after a
                  break in service are treated.
                </p>
              </li>
            </ol>
          </div>

          <aside id="registration" className="scroll-mt-24 space-y-6">
            <div className="border border-border bg-card p-6">
              <h2 className="text-[1.05rem]">Forms and procedures</h2>
              {loadingResources ? (
                <div className="mt-4 space-y-3">
                  {Array.from({ length: 3 }).map((_, i) => (
                    <Skeleton key={i} className="h-10" />
                  ))}
                </div>
              ) : (
                <ul className="mt-4 divide-y divide-border border-y border-border">
                  {guides.map((g) => (
                    <li key={g.id}>
                      <a href={g.fileUrl} className="group block py-3">
                        <span className="block text-[0.9rem] leading-snug group-hover:text-plum-700 dark:group-hover:text-primary">
                          {g.title}
                        </span>
                        <span className="mt-0.5 block text-[0.78rem] text-muted-foreground">
                          {g.format}
                        </span>
                      </a>
                    </li>
                  ))}
                </ul>
              )}
              <Button variant="outline" asChild className="mt-5 w-full">
                <Link to="/resources">All forms and downloads</Link>
              </Button>
            </div>
          </aside>
        </div>
      </Section>

      <Section tone="tinted">
        <div id="renewal" className="scroll-mt-24">
          <SectionHeading
            title="Licence renewal"
            lede="Renewal runs on an annual cycle with a firm closing date. Late applications are treated as fresh applications."
            className="mb-8"
          />
          <div className="grid gap-8 md:grid-cols-3">
            <div className="border-l-2 border-gold-500 pl-5">
              <h3 className="text-[1.02rem]">Before you apply</h3>
              <p className="mt-2 text-[0.88rem] leading-relaxed text-muted-foreground">
                Confirm your CPD hours are current and your annual subscription to the Institute is
                paid. Both are checked.
              </p>
            </div>
            <div className="border-l-2 border-gold-500 pl-5">
              <h3 className="text-[1.02rem]">Evidence required</h3>
              <p className="mt-2 text-[0.88rem] leading-relaxed text-muted-foreground">
                Professional indemnity cover, the firm's continuity arrangement, and confirmation
                that the practice name complies with the naming policy.
              </p>
            </div>
            <div className="border-l-2 border-gold-500 pl-5">
              <h3 className="text-[1.02rem]">If you are stuck</h3>
              <p className="mt-2 text-[0.88rem] leading-relaxed text-muted-foreground">
                Bring it to the licensing clinic. The committee chair has taken more of these
                through than anyone in the chapter.
              </p>
            </div>
          </div>
        </div>
      </Section>

      <Section>
        <div id="attachment" className="scroll-mt-24">
          <SectionHeading
            title="Firms led by chapter members"
            lede="Firms on this list are led by members of the chapter. Those marked as hosting will consider a practice attachment."
            action={
              <Button variant="outline" asChild>
                <Link to="/directory/firms">Full firms directory</Link>
              </Button>
            }
            className="mb-8"
          />
          {loadingFirms ? (
            <div className="grid gap-px bg-border md:grid-cols-2 lg:grid-cols-3">
              {Array.from({ length: 3 }).map((_, i) => (
                <Skeleton key={i} className="h-28" />
              ))}
            </div>
          ) : (
            <ul className="grid gap-px bg-border md:grid-cols-2 lg:grid-cols-3">
              {firms.map((f) => (
                <li key={f.id} className="bg-card p-5">
                  <h3 className="text-[1rem] leading-snug">{f.name}</h3>
                  <p className="mt-1 text-[0.82rem] text-accent-foreground">{f.principal}</p>
                  <p className="mt-2 text-[0.84rem] text-muted-foreground">{f.services.join(' · ')}</p>
                  <p className="mt-2 text-[0.8rem] text-muted-foreground">{f.area}</p>
                </li>
              ))}
            </ul>
          )}
        </div>
      </Section>
    </>
  )
}
