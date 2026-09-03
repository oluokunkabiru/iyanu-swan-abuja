import { useState } from 'react'
import { PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { gallery, galleryAlbums } from '@/data'
import { cn } from '@/lib/utils'

export default function Gallery() {
  const albums = ['All', ...galleryAlbums]
  const [album, setAlbum] = useState('All')
  const visible = album === 'All' ? gallery : gallery.filter((g) => g.album === album)

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'Gallery' }]}
        title="Gallery"
        intro="Seminars, outreach and chapter life."
      />

      <Section>
        <SectionHeading title="Albums" className="mb-6" />
        <div className="flex flex-wrap gap-2">
          {albums.map((a) => (
            <button
              key={a}
              type="button"
              onClick={() => setAlbum(a)}
              aria-pressed={album === a}
              className={cn(
                'rounded-sm border px-3 py-1.5 text-[0.84rem] font-medium transition-colors',
                album === a
                  ? 'border-plum-700 bg-plum-700 text-white dark:border-primary dark:bg-primary dark:text-primary-foreground'
                  : 'border-border bg-card text-muted-foreground hover:text-foreground',
              )}
            >
              {a}
            </button>
          ))}
        </div>

        <ul className="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {visible.map((img) => (
            <li key={img.id} className="border border-border bg-card">
              <img
                src={img.imageUrl}
                alt={img.caption}
                loading="lazy"
                className="aspect-[4/3] w-full object-cover"
              />
              <div className="p-4">
                <p className="text-[0.92rem] leading-snug">{img.caption}</p>
                <p className="tnum mt-1 text-[0.78rem] text-muted-foreground">
                  {img.album} · {img.year}
                </p>
              </div>
            </li>
          ))}
        </ul>
      </Section>
    </>
  )
}
