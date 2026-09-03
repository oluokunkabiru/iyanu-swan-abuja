import { SectionHeading, StatusTag } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { useAuth } from '@/context/AuthContext'
import { cpdRecords } from '@/data'
import { formatShortDate } from '@/lib/format'

export default function MembersCpd() {
  const { user } = useAuth()
  if (!user) return null

  const cycleStart = new Date().getFullYear() - 2
  const inCycle = cpdRecords.filter((r) => new Date(r.date).getFullYear() >= cycleStart)
  const structured = inCycle.filter((r) => r.type === 'Structured').reduce((s, r) => s + r.hours, 0)
  const unstructured = inCycle.filter((r) => r.type === 'Unstructured').reduce((s, r) => s + r.hours, 0)
  const total = structured + unstructured
  const remaining = Math.max(0, user.cpdTarget - total)

  return (
    <div className="space-y-12">
      <div>
        <SectionHeading
          title="CPD record"
          lede={`Your ${cycleStart}–${new Date().getFullYear()} cycle. The requirement is ${user.cpdTarget} credit hours over three consecutive years.`}
          className="mb-6"
        />
        <div className="grid gap-px bg-border sm:grid-cols-4">
          <div className="bg-card p-5">
            <p className="text-[0.8rem] text-muted-foreground">Total logged</p>
            <p className="tnum mt-2 font-heading text-3xl text-plum-700 dark:text-primary">{total}</p>
          </div>
          <div className="bg-card p-5">
            <p className="text-[0.8rem] text-muted-foreground">Structured</p>
            <p className="tnum mt-2 font-heading text-3xl">{structured}</p>
          </div>
          <div className="bg-card p-5">
            <p className="text-[0.8rem] text-muted-foreground">Unstructured</p>
            <p className="tnum mt-2 font-heading text-3xl">{unstructured}</p>
          </div>
          <div className="bg-card p-5">
            <p className="text-[0.8rem] text-muted-foreground">Still to earn</p>
            <p className="tnum mt-2 font-heading text-3xl text-accent-foreground">{remaining}</p>
          </div>
        </div>
      </div>

      <div>
        <SectionHeading
          title="Logged activity"
          action={
            <Button variant="outline" size="sm">
              Log an activity
            </Button>
          }
          className="mb-6"
        />
        <div className="overflow-x-auto">
          <table className="w-full min-w-[40rem] text-left">
            <caption className="sr-only">CPD activity log</caption>
            <thead>
              <tr className="border-b border-border text-[0.75rem] font-semibold text-muted-foreground">
                <th scope="col" className="py-2.5 pr-4">Activity</th>
                <th scope="col" className="py-2.5 pr-4">Date</th>
                <th scope="col" className="py-2.5 pr-4">Type</th>
                <th scope="col" className="py-2.5 pr-4">Hours</th>
                <th scope="col" className="py-2.5">Status</th>
              </tr>
            </thead>
            <tbody>
              {cpdRecords.map((r) => (
                <tr key={r.id} className="border-b border-border last:border-0">
                  <td className="py-3.5 pr-4 align-top text-[0.92rem]">{r.activity}</td>
                  <td className="tnum whitespace-nowrap py-3.5 pr-4 align-top text-[0.86rem] text-muted-foreground">
                    {formatShortDate(r.date)}
                  </td>
                  <td className="py-3.5 pr-4 align-top text-[0.86rem]">{r.type}</td>
                  <td className="tnum py-3.5 pr-4 align-top text-[0.86rem]">{r.hours}</td>
                  <td className="py-3.5 align-top">
                    <StatusTag tone={r.verified ? 'positive' : 'neutral'}>
                      {r.verified ? 'Verified' : 'Self-declared'}
                    </StatusTag>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      <div className="border-l-2 border-gold-500 bg-accent px-5 py-4">
        <h2 className="text-[1.05rem]">Getting hours verified</h2>
        <p className="mt-1.5 max-w-[65ch] text-[0.9rem] leading-relaxed text-muted-foreground">
          Attendance at chapter sessions is verified automatically when you register with the ICAN
          details on your membership record. Unstructured activity stays self-declared, which is
          what the Institute expects.
        </p>
      </div>
    </div>
  )
}
