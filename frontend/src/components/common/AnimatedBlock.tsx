/**
 * AnimatedBlock — wraps child content with automatic fade-in and slide-up animation.
 * Commonly used to animate sections, cards and list items.
 */

import type { ReactNode } from 'react'
import { motion } from 'framer-motion'
import { blockVariants } from '@/lib/animations'
import { usePrefersReducedMotion } from '@/hooks/usePrefersReducedMotion'
import { cn } from '@/lib/utils'

interface AnimatedBlockProps {
  children: ReactNode
  className?: string
  delay?: number
  variant?: 'block' | 'scale' | 'slideUp' | 'fromLeft' | 'fromRight'
}

export function AnimatedBlock({
  children,
  className,
  delay = 0,
  variant = 'block',
}: AnimatedBlockProps) {
  const reducedMotion = usePrefersReducedMotion()
  const variants = {
    block: blockVariants,
    scale: {
      initial: { opacity: 0, scale: 0.95 },
      animate: { opacity: 1, scale: 1 },
    },
    slideUp: {
      initial: { opacity: 0, y: 20 },
      animate: { opacity: 1, y: 0 },
    },
    fromLeft: {
      initial: { opacity: 0, x: -24 },
      animate: { opacity: 1, x: 0 },
    },
    fromRight: {
      initial: { opacity: 0, x: 24 },
      animate: { opacity: 1, x: 0 },
    },
  }

  return (
    <motion.div
      initial={reducedMotion ? false : 'initial'}
      whileInView="animate"
      viewport={{ once: true, amount: 0.2 }}
      variants={variants[variant]}
      transition={{
        duration: reducedMotion ? 0 : 0.5,
        delay: reducedMotion ? 0 : delay,
        ease: 'easeOut',
      }}
      className={cn(className)}
    >
      {children}
    </motion.div>
  )
}
