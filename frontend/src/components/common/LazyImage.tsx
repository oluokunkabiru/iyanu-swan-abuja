import { useLayoutEffect, useRef, useState, type ImgHTMLAttributes } from 'react'
import { cn } from '@/lib/utils'

interface LazyImageProps extends ImgHTMLAttributes<HTMLImageElement> {
  /**
   * Adds a small self-contained zoom on hover, so a bare image still moves
   * under the cursor even when it isn't part of a card with its own
   * `group-hover` zoom treatment. On by default; pass `false` for images
   * that already handle their own hover zoom via a `group-hover:scale-*`
   * class (event/news covers, gallery grids), so the two don't fight.
   */
  hoverZoom?: boolean
}

/**
 * A plain <img> pop in abruptly against this site's high-contrast brand
 * background (navy/gold container fills) once it decodes, which reads as a
 * flash/flicker on scroll. This fades the image in once it has actually
 * loaded instead of snapping straight from background to image.
 */
export function LazyImage({
  className,
  onLoad,
  loading = 'lazy',
  hoverZoom = true,
  ...props
}: LazyImageProps) {
  const [loaded, setLoaded] = useState(false)
  const imgRef = useRef<HTMLImageElement>(null)

  useLayoutEffect(() => {
    if (imgRef.current?.complete) {
      setLoaded(true)
    }
  }, [])

  return (
    <img
      ref={imgRef}
      loading={loading}
      className={cn(
        'transition-[opacity,transform] duration-500 ease-out',
        loaded ? 'opacity-100' : 'opacity-0',
        hoverZoom && 'hover:scale-[1.05]',
        className,
      )}
      onLoad={(e) => {
        setLoaded(true)
        onLoad?.(e)
      }}
      {...props}
    />
  )
}
