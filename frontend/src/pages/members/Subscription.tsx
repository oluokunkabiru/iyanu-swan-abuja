import { useState } from 'react'
import { fetchMySubscriptions } from '@/api/auth'
import { SectionHeading, StatusTag } from '@/components/common/Primitives'
import { DuesPaymentCard } from '@/components/sections/MembershipDues'
import { Skeleton } from '@/components/ui/skeleton'
import { useDues } from '@/context/DuesContext'
import { useApiData } from '@/hooks/useApiData'
import { formatDuesAmount, formatShortDate } from '@/lib/format'
import type { SubscriptionRecord } from '@/types'

export default function MembersSubscription() {
  const { quote } = useDues()
  const [refreshKey, setRefreshKey] = useState(0)
  const { data: subscriptions, isLoading } = useApiData(
    fetchMySubscriptions,
    [] as SubscriptionRecord[],
    [refreshKey, quote?.status],
  )
  const cardsReady = !isLoading

  const currentYear = new Date().getFullYear()
  const currentYearRecord = subscriptions.find((s) => s.year === currentYear)
  const paid = subscriptions.filter((s) => s.status === 'Paid')

  const yearsNeedingAction: { year: number; existing?: SubscriptionRecord }[] = []
  if (!currentYearRecord || currentYearRecord.status !== 'Paid') {
    yearsNeedingAction.push({ year: currentYear, existing: currentYearRecord })
  }
  subscriptions
    .filter((s) => s.year !== currentYear && s.status !== 'Paid')
    .forEach((s) => yearsNeedingAction.push({ year: s.year, existing: s }))

  return (
    <div className="space-y-12">
      <div>
        <SectionHeading
          title="Subscription and welfare levy"
          lede="Dues run on a calendar year. Paying keeps you on the active roll, which is what applies member rates and makes you eligible for office."
          className="mb-6"
        />

        {!cardsReady ? (
          <Skeleton className="h-32" />
        ) : yearsNeedingAction.length > 0 ? (
          <div className="space-y-4">
            {yearsNeedingAction.map(({ year }) => (
              <DuesPaymentCard
                key={year}
                year={year}
                onSubmitted={() => setRefreshKey((k) => k + 1)}
              />
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
                <th scope="col" className="py-2.5 pr-4">Level</th>
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
                  <td className="py-3.5 pr-4 align-top text-[0.88rem] text-muted-foreground">
                    {s.membershipLevelName ?? '—'}
                  </td>
                  <td className="tnum py-3.5 pr-4 align-top text-[0.88rem]">
                    {formatDuesAmount(s.subscription)}
                  </td>
                  <td className="tnum py-3.5 pr-4 align-top text-[0.88rem]">
                    {formatDuesAmount(s.welfare)}
                  </td>
                  <td className="tnum py-3.5 pr-4 align-top text-[0.88rem] font-medium">
                    {formatDuesAmount(s.subscription + s.welfare)}
                  </td>
                  <td className="tnum py-3.5 pr-4 align-top text-[0.88rem] text-muted-foreground">
                    {s.paidOn ? formatShortDate(s.paidOn) : '—'}
                  </td>
                  <td className="py-3.5 align-top">
                    <span className="tnum block text-[0.82rem] text-muted-foreground">
                      {s.reference ?? s.bankTransferReference}
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
