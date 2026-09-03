import { useEffect, useState } from 'react'
import { ChevronLeft, ChevronRight, Quote } from 'lucide-react'
import { getSpotlights } from '@/api/content'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import type { MemberSpotlight } from '@/types'

function initials(name: string) {
  return name
    .split(' ')
    .map((part) => part[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
}

export function MemberSpotlightCarousel() {
  const [spotlights, setSpotlights] = useState<MemberSpotlight[]>([])
  const [index, setIndex] = useState(0)

  useEffect(() => {
    getSpotlights().then(setSpotlights).catch(() => setSpotlights([]))
  }, [])

  if (spotlights.length === 0) return null

  const current = spotlights[index]

  return (
    <section className="border-t bg-muted/30">
      <div className="mx-auto max-w-3xl px-4 py-20 text-center">
        <h2 className="font-heading text-3xl font-bold tracking-tight">Member Spotlight</h2>

        <div key={current.id} className="fade-up mt-10 flex flex-col items-center">
          <Avatar className="h-20 w-20 ring-4 ring-background shadow-lg shadow-primary/10">
            <AvatarImage src={current.photo_url ?? undefined} alt={current.name} />
            <AvatarFallback className="bg-gradient-to-br from-primary/20 to-accent font-heading font-semibold text-primary">
              {initials(current.name)}
            </AvatarFallback>
          </Avatar>
          <Quote className="mt-5 h-6 w-6 text-primary/50" />
          {current.quote && (
            <p className="mt-2 max-w-xl font-heading text-xl text-balance italic text-foreground/90">
              &ldquo;{current.quote}&rdquo;
            </p>
          )}
          <p className="mt-4 text-sm font-semibold text-primary">{current.name}</p>

          {spotlights.length > 1 && (
            <div className="mt-8 flex items-center gap-4">
              <Button
                size="icon"
                variant="outline"
                className="rounded-full"
                onClick={() => setIndex((i) => (i - 1 + spotlights.length) % spotlights.length)}
                aria-label="Previous"
              >
                <ChevronLeft className="h-4 w-4" />
              </Button>
              <div className="flex gap-1.5">
                {spotlights.map((s, i) => (
                  <button
                    key={s.id}
                    type="button"
                    aria-label={`Go to spotlight ${i + 1}`}
                    onClick={() => setIndex(i)}
                    className={`h-1.5 rounded-full transition-all ${
                      i === index ? 'w-5 bg-primary' : 'w-1.5 bg-primary/25'
                    }`}
                  />
                ))}
              </div>
              <Button
                size="icon"
                variant="outline"
                className="rounded-full"
                onClick={() => setIndex((i) => (i + 1) % spotlights.length)}
                aria-label="Next"
              >
                <ChevronRight className="h-4 w-4" />
              </Button>
            </div>
          )}
        </div>
      </div>
    </section>
  )
}
