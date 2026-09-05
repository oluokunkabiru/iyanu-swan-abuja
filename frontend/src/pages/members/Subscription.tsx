import { fetchMySubscriptions } from '@/api/auth'
import { SectionHeading, StatusTag } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import { useApiData } from '@/hooks/useApiData'
import { formatNaira, formatShortDate } from '@/lib/format'
import type { SubscriptionRecord } from '@/types'

export default function MembersSubscription() {
  const { data: subscriptions, isLoading } = useApiData(fetchMySubscriptions, [] as SubscriptionRecord[])
  const outstanding = subscriptions.filter((s) => s.status === 'Outstanding')
  const paid = subscriptions.filter((s) => s.status === 'Paid')

  return (
    <div className="space-y-12">
      <div>
        <SectionHeading
          title="Subscription and welfare levy"
          lede="Dues run on a calendar year. Paying keeps you on the active roll, which is what applies member rates and makes you eligible for office."
          className="mb-6"
        />

        {isLoading ? (
          <Skeleton className="h-32" />
        ) : outstanding.length > 0 ? (
          <div className="space-y-4">
            {outstanding.map((s) => (
              <div key={s.id} className="border border-gold-500/50 bg-accent p-6">
                <div className="flex flex-wrap items-start justify-between gap-4">
                  <div>
                    <h3 className="font-heading text-xl">{s.year} dues</h3>
                    <dl className="mt-3 space-y-1.5 text-[0.9rem]">
                      <div className="flex gap-3">
                        <dt className="text-muted-foreground">Subscription</dt>
                        <dd className="tnum font-medium">{formatNaira(s.subscription)}</dd>
                      </div>
                      <div className="flex gap-3">
                        <dt className="text-muted-foreground">Welfare levy</dt>
                        <dd className="tnum font-medium">{formatNaira(s.welfare)}</dd>
                      </div>
                    </dl>
                  </div>
                  <div className="text-right">
                    <p className="tnum font-heading text-3xl text-plum-700 dark:text-primary">
                      {formatNaira(s.subscription + s.welfare)}
                    </p>
                    <Button className="mt-3">Pay {s.year} dues</Button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <div className="border-l-2 border-success bg-success/8 px-5 py-4">
            <h3 className="text-[1.05rem]">Nothing outstanding</h3>
            <p className="mt-1.5 text-[0.9rem] text-muted-foreground">
              Your dues are current and you are on the active roll.
            </p>
          </div>
        )}
      </div>

      <div>
        <SectionHeading title="Payment history" className="mb-6" />
        {isLoading ? (
          <div className="space-y-3">
            {Array.from({ length: 4 }).map((_, i) => (
              <Skeleton key={i} className="h-10" />
            ))}
          </div>
        ) : (
        <div className="overflow-x-auto">
          <table className="w-full min-w-[40rem] text-left">
            <caption className="sr-only">Subscription payment history</caption>
            <thead>
              <tr className="border-b border-border text-[0.75rem] font-semibold text-muted-foreground">
                <th scope="col" className="py-2.5 pr-4">Year</th>
                <th scope="col" className="py-2.5 pr-4">Subscription</th>
                <th scope="col" className="py-2.5 pr-4">Welfare</th>
                <th scope="col" className="py-2.5 pr-4">Total</th>
                <th scope="col" className="py-2.5 pr-4">Paid on</th>
                <th scope="col" className="py-2.5">Reference</th>
              </tr>
            </thead>
            <tbody>
              {paid.map((s) => (
                <tr key={s.id} className="border-b border-border last:border-0">
                  <td className="tnum py-3.5 pr-4 align-top font-medium">{s.year}</td>
                  <td className="tnum py-3.5 pr-4 align-top text-[0.88rem]">
                    {formatNaira(s.subscription)}
                  </td>
                  <td className="tnum py-3.5 pr-4 align-top text-[0.88rem]">
                    {formatNaira(s.welfare)}
                  </td>
                  <td className="tnum py-3.5 pr-4 align-top text-[0.88rem] font-medium">
                    {formatNaira(s.subscription + s.welfare)}
                  </td>
                  <td className="tnum py-3.5 pr-4 align-top text-[0.88rem] text-muted-foreground">
                    {s.paidOn ? formatShortDate(s.paidOn) : '—'}
                  </td>
                  <td className="py-3.5 align-top">
                    <span className="tnum block text-[0.82rem] text-muted-foreground">
                      {s.reference}
                    </span>
                    <span className="mt-1 inline-block">
                      <StatusTag tone="positive">Paid</StatusTag>
                    </span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
        )}
      </div>

      <div className="border border-border bg-card p-5">
        <h2 className="text-[1.05rem]">Where the welfare levy goes</h2>
        <p className="mt-2 max-w-[65ch] text-[0.9rem] leading-relaxed text-muted-foreground">
          The levy funds the chapter response when a member faces bereavement or illness, and pays
          for the community outreach the chapter commits to each year. Spending is published in the
          annual accounts.
        </p>
      </div>
    </div>
  )
}
