import { useMemo, useState } from 'react'
import { Link } from 'react-router-dom'
import { Search } from 'lucide-react'
import { getFirms } from '@/api/content'
import { PageHeader, Section, SectionHeading, StatusTag } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Skeleton } from '@/components/ui/skeleton'
import { useApiData } from '@/hooks/useApiData'
import type { Firm } from '@/types'

export default function Firms() {
  const [query, setQuery] = useState('')
  const { data: firms, isLoading } = useApiData(getFirms, [] as Firm[])

  const visible = useMemo(() => {
    const q = query.trim().toLowerCase()
    if (!q) return firms
    return firms.filter(
      (f) =>
        f.name.toLowerCase().includes(q) ||
        f.principal.toLowerCase().includes(q) ||
        f.area.toLowerCase().includes(q) ||
        f.services.some((s) => s.toLowerCase().includes(q)),
    )
  }, [firms, query])

  return (
    <>
      <PageHeader
        breadcrumb={[
          { label: 'Home', to: '/' },
          { label: 'Directories', to: '/directory' },
          { label: 'Registered firms' },
        ]}
        title="Registered firms"
        intro="Firms led by chapter members, with licence status and service lines. Licence status is confirmed against the Institute's register."
      />

      <Section>
        <SectionHeading
          title="Search firms"
          lede={isLoading ? 'Loading…' : `${visible.length} firms listed.`}
          className="mb-6"
        />

        <div className="max-w-md space-y-2">
          <Label htmlFor="firm-search">Firm, principal, service or area</Label>
          <div className="relative">
            <Search
              aria-hidden="true"
              className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
            />
            <Input
              id="firm-search"
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              placeholder="Try “insolvency” or “Maitama”"
              className="pl-9"
            />
          </div>
        </div>

        {isLoading ? (
          <div className="mt-8 grid gap-px bg-border md:grid-cols-2">
            {Array.from({ length: 4 }).map((_, i) => (
              <Skeleton key={i} className="h-40" />
            ))}
          </div>
        ) : (
          <ul className="mt-8 grid gap-px bg-border md:grid-cols-2">
            {visible.map((f) => (
              <li key={f.id} className="bg-card p-6">
                <div className="flex items-start justify-between gap-4">
                  <h2 className="text-[1.08rem] leading-snug">{f.name}</h2>
                  <StatusTag tone={f.licenceStatus === 'Active' ? 'positive' : 'warning'}>
                    {f.licenceStatus}
                  </StatusTag>
                </div>
                <p className="mt-1.5 text-[0.85rem] text-accent-foreground">{f.principal}</p>
                <dl className="mt-4 space-y-2 text-[0.86rem]">
                  <div className="flex gap-2">
                    <dt className="text-muted-foreground">Licence</dt>
                    <dd className="tnum">{f.licenceNumber}</dd>
                  </div>
                  <div className="flex gap-2">
                    <dt className="text-muted-foreground">Area</dt>
                    <dd>{f.area}</dd>
                  </div>
                  <div className="flex gap-2">
                    <dt className="text-muted-foreground">Services</dt>
                    <dd>{f.services.join(', ')}</dd>
                  </div>
                </dl>
              </li>
            ))}
          </ul>
        )}

        {!isLoading && visible.length === 0 && (
          <p className="mt-6 border border-dashed border-rule px-6 py-10 text-center text-[0.9rem] text-muted-foreground">
            No firms match that search. Try a service line such as “audit” or “tax”.
          </p>
        )}
      </Section>

      <Section tone="tinted">
        <SectionHeading
          title="Thinking of setting up your own?"
          lede="The Women in Practice committee runs a quarterly licensing clinic and maintains the practice attachment register."
          action={
            <Button asChild>
              <Link to="/practice">Women in practice</Link>
            </Button>
          }
        />
      </Section>
    </>
  )
}
