import { useMemo, useState } from 'react'
import { Link } from 'react-router-dom'
import { Search } from 'lucide-react'
import { PageHeader, Section, SectionHeading, StatusTag } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { directoryMembers, firms } from '@/data'
import { cn } from '@/lib/utils'
import type { Sector } from '@/types'

const sectors: (Sector | 'All')[] = [
  'All',
  'Public practice',
  'Public sector',
  'Financial services',
  'Industry',
  'Academia',
  'Consulting',
]

export default function Directory() {
  const [query, setQuery] = useState('')
  const [sector, setSector] = useState<(typeof sectors)[number]>('All')

  const visible = useMemo(() => {
    const q = query.trim().toLowerCase()
    return directoryMembers.filter((m) => {
      const matchesSector = sector === 'All' || m.sector === sector
      const matchesQuery =
        q === '' ||
        m.name.toLowerCase().includes(q) ||
        m.specialisation.toLowerCase().includes(q) ||
        m.membershipNumber.toLowerCase().includes(q)
      return matchesSector && matchesQuery
    })
  }, [query, sector])

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'Members directory' }]}
        title="Members directory"
        intro="Search the chapter roll by name, sector or specialisation. Members choose what appears here; contact details are exchanged through the chapter, not published."
        aside={
          <Button variant="outline" asChild className="border-plum-200/40 bg-transparent text-white hover:bg-plum-800 hover:text-white">
            <Link to="/directory/firms">Registered firms</Link>
          </Button>
        }
      />

      <Section>
        <SectionHeading
          title="Search the roll"
          lede={`${visible.length} of ${directoryMembers.length} members shown.`}
          className="mb-6"
        />

        <div className="space-y-5">
          <div className="max-w-md space-y-2">
            <Label htmlFor="member-search">Name, specialisation or membership number</Label>
            <div className="relative">
              <Search
                aria-hidden="true"
                className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
              />
              <Input
                id="member-search"
                value={query}
                onChange={(e) => setQuery(e.target.value)}
                placeholder="Try “forensic” or “ICAN/021760”"
                className="pl-9"
              />
            </div>
          </div>

          <fieldset>
            <legend className="mb-2 text-[0.8rem] font-semibold text-muted-foreground">Sector</legend>
            <div className="flex flex-wrap gap-2">
              {sectors.map((s) => (
                <button
                  key={s}
                  type="button"
                  onClick={() => setSector(s)}
                  aria-pressed={sector === s}
                  className={cn(
                    'rounded-sm border px-3 py-1.5 text-[0.84rem] font-medium transition-colors',
                    sector === s
                      ? 'border-plum-700 bg-plum-700 text-white dark:border-primary dark:bg-primary dark:text-primary-foreground'
                      : 'border-border bg-card text-muted-foreground hover:text-foreground',
                  )}
                >
                  {s}
                </button>
              ))}
            </div>
          </fieldset>
        </div>

        <div className="mt-8 overflow-x-auto">
          <table className="w-full min-w-[48rem] text-left">
            <caption className="sr-only">Chapter members</caption>
            <thead>
              <tr className="border-b border-border text-[0.75rem] font-semibold text-muted-foreground">
                <th scope="col" className="py-2.5 pr-4">Member</th>
                <th scope="col" className="py-2.5 pr-4">Membership number</th>
                <th scope="col" className="py-2.5 pr-4">Sector</th>
                <th scope="col" className="py-2.5 pr-4">Specialisation</th>
                <th scope="col" className="py-2.5">Admitted</th>
              </tr>
            </thead>
            <tbody>
              {visible.map((m) => (
                <tr key={m.id} className="border-b border-border last:border-0">
                  <td className="py-3.5 pr-4 align-top">
                    <span className="block font-medium leading-snug">
                      {m.name}, {m.credential}
                    </span>
                    {m.chapterRole && (
                      <span className="mt-1 inline-block">
                        <StatusTag tone="gold">{m.chapterRole}</StatusTag>
                      </span>
                    )}
                  </td>
                  <td className="tnum py-3.5 pr-4 align-top text-[0.86rem] text-muted-foreground">
                    {m.membershipNumber}
                  </td>
                  <td className="py-3.5 pr-4 align-top text-[0.86rem]">{m.sector}</td>
                  <td className="py-3.5 pr-4 align-top text-[0.86rem] text-muted-foreground">
                    {m.specialisation}
                  </td>
                  <td className="tnum py-3.5 align-top text-[0.86rem] text-muted-foreground">
                    {m.yearAdmitted}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {visible.length === 0 && (
          <p className="mt-6 border border-dashed border-rule px-6 py-10 text-center text-[0.9rem] text-muted-foreground">
            No members match that search. Try a shorter term, or clear the sector filter.
          </p>
        )}
      </Section>

      <Section tone="tinted">
        <SectionHeading
          title="Looking for a firm instead?"
          lede={`${firms.length} firms led by chapter members are listed with their licence status and service lines.`}
          action={
            <Button asChild>
              <Link to="/directory/firms">Open the firms directory</Link>
            </Button>
          }
        />
      </Section>
    </>
  )
}
