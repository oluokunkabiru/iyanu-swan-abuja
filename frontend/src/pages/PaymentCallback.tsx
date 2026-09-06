import { Link, useSearchParams } from 'react-router-dom'
import { verifyPayment } from '@/api/content'
import { Section } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import { useApiData } from '@/hooks/useApiData'

type Outcome = { type: 'subscription' | 'event_registration'; status: string } | null

export default function PaymentCallback() {
  const [params] = useSearchParams()
  const reference = params.get('reference')

  const { data: outcome, isLoading } = useApiData(
    () => (reference ? verifyPayment(reference) : Promise.resolve(null)),
    null as Outcome,
    [reference],
  )

  const paid = outcome?.status === 'paid'
  const continueTo = outcome?.type === 'subscription' ? '/members/subscription' : '/members/tickets'

  return (
    <Section>
      <div className="mx-auto max-w-md text-center">
        {isLoading ? (
          <div className="space-y-4">
            <Skeleton className="mx-auto h-8 w-2/3" />
            <Skeleton className="h-4 w-full" />
          </div>
        ) : !reference || !outcome ? (
          <>
            <h1 className="text-2xl">We couldn&rsquo;t confirm this payment</h1>
            <p className="mt-3 text-[0.92rem] text-muted-foreground">
              No payment reference was found. If you completed a payment, check your subscription or
              tickets in the members area.
            </p>
          </>
        ) : paid ? (
          <>
            <h1 className="text-2xl">Payment confirmed</h1>
            <p className="mt-3 text-[0.92rem] text-muted-foreground">
              Thank you — your payment has been received and your record has been updated.
            </p>
          </>
        ) : (
          <>
            <h1 className="text-2xl">Payment not completed</h1>
            <p className="mt-3 text-[0.92rem] text-muted-foreground">
              We could not confirm this payment. If you were charged, contact the Financial Secretary
              with your reference: <span className="font-medium">{reference}</span>.
            </p>
          </>
        )}

        <Button asChild className="mt-8">
          <Link to={outcome ? continueTo : '/members'}>Go to members area</Link>
        </Button>
      </div>
    </Section>
  )
}
