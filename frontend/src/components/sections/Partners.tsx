import { useEffect, useState } from 'react'
import { getPartners } from '@/api/content'
import type { Partner } from '@/types'

export function Partners() {
  const [partners, setPartners] = useState<Partner[]>([])

  useEffect(() => {
    getPartners().then(setPartners).catch(() => setPartners([]))
  }, [])

  if (partners.length === 0) return null

  return (
    <section className="border-t bg-muted/30">
      <div className="mx-auto max-w-6xl px-4 py-14">
        <p className="text-center text-sm font-medium tracking-wide text-muted-foreground uppercase">
          In partnership with
        </p>
        <div className="mt-7 flex flex-wrap items-center justify-center gap-x-12 gap-y-6">
          {partners.map((partner) => {
            const content = partner.logo_url ? (
              <img
                src={partner.logo_url}
                alt={partner.name}
                className="h-9 object-contain grayscale transition-all duration-300 hover:grayscale-0"
              />
            ) : (
              <span className="font-heading text-lg font-semibold text-muted-foreground transition-colors hover:text-foreground">
                {partner.name}
              </span>
            )

            return partner.url ? (
              <a key={partner.id} href={partner.url} target="_blank" rel="noreferrer">
                {content}
              </a>
            ) : (
              <span key={partner.id}>{content}</span>
            )
          })}
        </div>
      </div>
    </section>
  )
}
