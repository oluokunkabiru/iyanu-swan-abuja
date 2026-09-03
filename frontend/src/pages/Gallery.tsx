import { useEffect, useState } from 'react'
import { ImageOff } from 'lucide-react'
import { getGallery } from '@/api/content'
import type { GalleryImage } from '@/types'

export default function Gallery() {
  const [images, setImages] = useState<GalleryImage[]>([])

  useEffect(() => {
    getGallery().then(setImages).catch(() => setImages([]))
  }, [])

  return (
    <section className="mx-auto max-w-6xl px-4 py-20">
      <div className="mx-auto max-w-xl text-center">
        <h1 className="font-heading text-4xl font-bold tracking-tight">Gallery</h1>
        <p className="mt-3 text-muted-foreground">Moments from our events and community programmes</p>
      </div>

      {images.length === 0 ? (
        <div className="flex flex-col items-center py-16 text-center text-muted-foreground">
          <ImageOff className="h-10 w-10 text-muted-foreground/50" />
          <p className="mt-3">No photos yet — check back soon.</p>
        </div>
      ) : (
        <div className="mt-12 columns-2 gap-4 sm:columns-3">
          {images.map((image, i) => (
            <figure
              key={image.id}
              className="fade-up group mb-4 break-inside-avoid overflow-hidden rounded-xl border"
              style={{ animationDelay: `${(i % 6) * 60}ms` }}
            >
              {image.image_url && (
                <img
                  src={image.image_url}
                  alt={image.caption ?? ''}
                  className="w-full transition-transform duration-500 group-hover:scale-105"
                />
              )}
              {image.caption && (
                <figcaption className="p-2.5 text-center text-xs text-muted-foreground">{image.caption}</figcaption>
              )}
            </figure>
          ))}
        </div>
      )}
    </section>
  )
}
