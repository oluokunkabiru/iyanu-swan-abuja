import { useEffect, useState } from 'react'

/**
 * Tracks the user's `prefers-reduced-motion` setting so interactive,
 * JS-driven animations (mouse-tilt, count-up, etc.) can be skipped for
 * people who have asked the OS for less motion. CSS-only animations are
 * already handled globally in index.css; this covers the animations that
 * have to make that decision in script.
 */
export function usePrefersReducedMotion() {
  const [reduced, setReduced] = useState(() =>
    typeof window !== 'undefined'
      ? window.matchMedia('(prefers-reduced-motion: reduce)').matches
      : false,
  )

  useEffect(() => {
    const mql = window.matchMedia('(prefers-reduced-motion: reduce)')
    const handler = () => setReduced(mql.matches)
    mql.addEventListener('change', handler)
    return () => mql.removeEventListener('change', handler)
  }, [])

  return reduced
}
