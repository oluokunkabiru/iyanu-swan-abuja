import type { PointerEvent, ReactNode } from 'react'
import { motion, useMotionValue, useSpring } from 'framer-motion'
import { cardHover } from '@/lib/animations'
import { cn } from '@/lib/utils'

/** A restrained lift used for cards and other repeatable content surfaces. */
export function InteractiveSurface({
  children,
  className,
  as = 'div',
}: {
  children: ReactNode
  className?: string
  as?: 'div' | 'article' | 'li'
}) {
  const Component = motion[as]
  const rotateX = useSpring(useMotionValue(0), { stiffness: 260, damping: 24, mass: 0.35 })
  const rotateY = useSpring(useMotionValue(0), { stiffness: 260, damping: 24, mass: 0.35 })

  function handlePointerMove(event: PointerEvent<HTMLElement>) {
    if (event.pointerType !== 'mouse') return

    const bounds = event.currentTarget.getBoundingClientRect()
    const x = (event.clientX - bounds.left) / bounds.width - 0.5
    const y = (event.clientY - bounds.top) / bounds.height - 0.5
    rotateX.set(y * -2.2)
    rotateY.set(x * 2.2)
  }

  function resetTilt() {
    rotateX.set(0)
    rotateY.set(0)
  }

  return (
    <Component
      className={cn('interactive-panel transform-gpu [perspective:900px]', className)}
      initial="rest"
      whileHover="hover"
      whileFocus="hover"
      whileTap="rest"
      onPointerMove={handlePointerMove}
      onPointerLeave={resetTilt}
      style={{ rotateX, rotateY }}
      variants={cardHover}
    >
      {children}
    </Component>
  )
}
