import { Link } from 'react-router-dom'
import { Section } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'

const suggestions = [
  { label: 'Membership', to: '/membership' },
  { label: 'Events', to: '/events' },
  { label: 'CPD', to: '/cpd' },
  { label: 'Members directory', to: '/directory' },
  { label: 'Publications', to: '/publications' },
  { label: 'Contact', to: '/contact' },
]

export default function NotFound() {
  return (
    <Section>
      <div className="mx-auto max-w-xl py-12 text-center">
        <p className="font-heading text-5xl text-plum-200 dark:text-plum-500">404</p>
        <h1 className="mt-4 text-2xl">That page is not on the site</h1>
        <p className="mt-3 text-[0.95rem] leading-relaxed text-muted-foreground">
          The link may be out of date, or the page may have moved during the rebuild. Here is where
          most people are heading.
        </p>
        <ul className="mt-8 flex flex-wrap justify-center gap-2">
          {suggestions.map((s) => (
            <li key={s.to}>
              <Link
                to={s.to}
                className="inline-block rounded-sm border border-border bg-card px-4 py-2 text-[0.86rem] font-medium hover:border-plum-500"
              >
                {s.label}
              </Link>
            </li>
          ))}
        </ul>
        <Button asChild className="mt-8">
          <Link to="/">Back to the home page</Link>
        </Button>
      </div>
    </Section>
  )
}
