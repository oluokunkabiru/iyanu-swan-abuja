import { useState } from 'react'
import { Link } from 'react-router-dom'
import { ChevronDown } from 'lucide-react'
import { PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { faqs } from '@/data'
import { cn } from '@/lib/utils'
import type { Faq } from '@/types'

const topics: (Faq['topic'] | 'All')[] = ['All', 'Membership', 'Events', 'Payments', 'CPD', 'General']

export default function Faqs() {
  const [topic, setTopic] = useState<(typeof topics)[number]>('All')
  const [openId, setOpenId] = useState<string | null>(faqs[0].id)

  const visible = topic === 'All' ? faqs : faqs.filter((f) => f.topic === topic)

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'FAQs' }]}
        title="Frequently asked questions"
        intro="Membership, events, payments and CPD. If your question is not here, the General Secretary will answer it."
      />

      <Section>
        <SectionHeading title="Questions we get most" className="mb-6" />

        <div className="flex flex-wrap gap-2">
          {topics.map((t) => (
            <button
              key={t}
              type="button"
              onClick={() => setTopic(t)}
              aria-pressed={topic === t}
              className={cn(
                'rounded-sm border px-3 py-1.5 text-[0.84rem] font-medium transition-colors',
                topic === t
                  ? 'border-plum-700 bg-plum-700 text-white dark:border-primary dark:bg-primary dark:text-primary-foreground'
                  : 'border-border bg-card text-muted-foreground hover:text-foreground',
              )}
            >
              {t}
            </button>
          ))}
        </div>

        <ul className="mt-8 divide-y divide-border border-y border-border">
          {visible.map((faq) => {
            const isOpen = openId === faq.id
            return (
              <li key={faq.id}>
                <h3>
                  <button
                    type="button"
                    onClick={() => setOpenId(isOpen ? null : faq.id)}
                    aria-expanded={isOpen}
                    className="flex w-full items-start justify-between gap-4 py-4 text-left"
                  >
                    <span className="font-heading text-[1.05rem] leading-snug">{faq.question}</span>
                    <ChevronDown
                      aria-hidden="true"
                      className={cn(
                        'mt-1 h-4 w-4 shrink-0 text-gold-500 transition-transform',
                        isOpen && 'rotate-180',
                      )}
                    />
                  </button>
                </h3>
                {isOpen && (
                  <p className="max-w-[70ch] pb-5 text-[0.93rem] leading-relaxed text-muted-foreground">
                    {faq.answer}
                  </p>
                )}
              </li>
            )
          })}
        </ul>
      </Section>

      <Section tone="tinted">
        <div className="flex flex-wrap items-center justify-between gap-6">
          <div>
            <h2 className="text-xl">Still stuck?</h2>
            <p className="mt-2 max-w-xl text-[0.93rem] text-muted-foreground">
              Anything about dues, confirmations or receipts goes to the Financial Secretary.
              Everything else goes to the General Secretary.
            </p>
          </div>
          <Button asChild>
            <Link to="/contact">Contact the chapter</Link>
          </Button>
        </div>
      </Section>
    </>
  )
}
