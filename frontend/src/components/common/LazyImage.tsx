import { useLayoutEffect, useRef, useState, type ImgHTMLAttributes } from 'react'
import { cn } from '@/lib/utils'

/**
 * A plain <img> pop in abruptly against this site's high-contrast brand
 * background (navy/gold container fills) once it decodes, which reads as a
 * flash/flicker on scroll. This fades the image in once it has actually
 * loaded instead of snapping straight from background to image.
 */
export function LazyImage({ className, onLoad, loading = 'lazy', ...props }: ImgHTMLAttributes<HTMLImageElement>) {
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
      className={cn('transition-opacity duration-500 ease-out', loaded ? 'opacity-100' : 'opacity-0', className)}
      onLoad={(e) => {
        setLoaded(true)
        onLoad?.(e)
      }}
      {...props}
    />
  )
}
