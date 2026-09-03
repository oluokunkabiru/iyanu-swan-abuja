import { useEffect, useState } from 'react'
import { getExecutives } from '@/api/content'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import type { ExecutiveMember } from '@/types'

function initials(name: string) {
  return name
    .split(' ')
    .map((part) => part[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
}

export function ExecutiveGrid({ limit }: { limit?: number }) {
  const [executives, setExecutives] = useState<ExecutiveMember[]>([])

  useEffect(() => {
    getExecutives().then(setExecutives).catch(() => setExecutives([]))
  }, [])

  if (executives.length === 0) return null

  const items = limit ? executives.slice(0, limit) : executives

  return (
    <section className="mx-auto max-w-6xl px-4 py-20">
      <div className="mx-auto max-w-xl text-center">
        <h2 className="font-heading text-3xl font-bold tracking-tight">Executive Committee</h2>
        <p className="mt-3 text-muted-foreground">Meet the women leading SWAN Abuja Chapter</p>
      </div>
      <div className="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-5">
        {items.map((executive, i) => (
          <div
            key={executive.id}
            className="fade-up group text-center"
            style={{ animationDelay: `${i * 80}ms` }}
          >
            <Avatar className="mx-auto h-24 w-24 ring-4 ring-background shadow-lg shadow-primary/10 transition-transform duration-300 group-hover:-translate-y-1">
              <AvatarImage src={executive.photo_url ?? undefined} alt={executive.name} />
              <AvatarFallback className="bg-gradient-to-br from-primary/20 to-accent font-heading text-lg font-semibold text-primary">
                {initials(executive.name)}
              </AvatarFallback>
            </Avatar>
            <h3 className="mt-4 font-heading font-semibold">
              {executive.name}
              {executive.credential && (
                <span className="text-muted-foreground">, {executive.credential}</span>
              )}
            </h3>
            <p className="text-sm text-primary">{executive.position}</p>
          </div>
        ))}
      </div>
    </section>
  )
}
