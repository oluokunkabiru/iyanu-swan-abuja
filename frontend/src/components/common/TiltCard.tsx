import { useRef, type MouseEvent, type ReactNode } from 'react'
import { motion, useMotionTemplate, useMotionValue, useSpring, useTransform } from 'framer-motion'
import { usePrefersReducedMotion } from '@/hooks/usePrefersReducedMotion'
import { cn } from '@/lib/utils'

/**
 * Wraps content (typically a photo or feature card) with a subtle 3D tilt
 * that follows the cursor, plus a soft light glare tracking the same point.
 * Falls back to a static wrapper when the user has asked for less motion.
 */
export function TiltCard({
  children,
  className,
  max = 8,
  glare = true,
}: {
  children: ReactNode
  className?: string
  /** Maximum rotation, in degrees, at the edge of the element. */
  max?: number
  /** Whether to show the cursor-following light glare overlay. */
  glare?: boolean
}) {
  const prefersReducedMotion = usePrefersReducedMotion()
  const ref = useRef<HTMLDivElement>(null)

  const x = useMotionValue(0)
  const y = useMotionValue(0)
  const rotateX = useSpring(useTransform(y, [-0.5, 0.5], [max, -max]), {
    stiffness: 300,
    damping: 30,
  })
  const rotateY = useSpring(useTransform(x, [-0.5, 0.5], [-max, max]), {
    stiffness: 300,
    damping: 30,
  })
  const glareXPercent = useTransform(x, [-0.5, 0.5], ['0%', '100%'])
  const glareYPercent = useTransform(y, [-0.5, 0.5], ['0%', '100%'])
  const glareBackground = useMotionTemplate`radial-gradient(circle at ${glareXPercent} ${glareYPercent}, rgba(255,255,255,0.25), transparent 60%)`

  function handleMouseMove(event: MouseEvent<HTMLDivElement>) {
    if (prefersReducedMotion) return
    const rect = ref.current?.getBoundingClientRect()
    if (!rect) return
    x.set((event.clientX - rect.left) / rect.width - 0.5)
    y.set((event.clientY - rect.top) / rect.height - 0.5)
  }

  function handleMouseLeave() {
    x.set(0)
    y.set(0)
  }

  return (
    <motion.div
      ref={ref}
      onMouseMove={handleMouseMove}
      onMouseLeave={handleMouseLeave}
      style={prefersReducedMotion ? undefined : { rotateX, rotateY, transformPerspective: 800 }}
      className={cn('group relative', className)}
    >
      {children}
      {glare && !prefersReducedMotion && (
        <motion.span
          aria-hidden="true"
          className="pointer-events-none absolute inset-0 opacity-0 transition-opacity duration-300 group-hover:opacity-100"
          style={{ background: glareBackground }}
        />
      )}
    </motion.div>
  )
}
