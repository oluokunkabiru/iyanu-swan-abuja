import { useEffect, useRef, useState } from 'react'
import { animate, useInView } from 'framer-motion'
import { usePrefersReducedMotion } from '@/hooks/usePrefersReducedMotion'

interface ParsedStat {
  prefix: string
  suffix: string
  numberOfValue: number
  decimals: number
  useCommas: boolean
}

/**
 * Pulls the numeric part out of a stat string like "1,240", "120+" or
 * "₦25,000", so it can be counted up while keeping whatever formatting
 * surrounded it. Returns null when there's nothing numeric to animate.
 */
function parseStatValue(raw: string): ParsedStat | null {
  const match = raw.match(/^(\D*)([\d,]*\d(?:\.\d+)?)(\D*)$/)
  if (!match) return null
  const [, prefix, numberPart, suffix] = match
  const decimals = numberPart.includes('.') ? numberPart.split('.')[1].length : 0
  return {
    prefix,
    suffix,
    numberOfValue: Number(numberPart.replace(/,/g, '')),
    decimals,
    useCommas: numberPart.includes(','),
  }
}

function formatStatValue(current: number, parsed: ParsedStat) {
  const rounded = parsed.decimals > 0 ? current.toFixed(parsed.decimals) : Math.round(current).toString()
  const [intPart, decPart] = rounded.split('.')
  const withCommas = parsed.useCommas ? Number(intPart).toLocaleString('en-US') : intPart
  return `${parsed.prefix}${withCommas}${decPart ? `.${decPart}` : ''}${parsed.suffix}`
}

/** Animates a stat figure counting up from zero when it scrolls into view. */
export function CountUp({ value, className }: { value: string; className?: string }) {
  const ref = useRef<HTMLSpanElement>(null)
  const isInView = useInView(ref, { once: true, amount: 0.5 })
  const prefersReducedMotion = usePrefersReducedMotion()
  const parsed = parseStatValue(value)
  const [display, setDisplay] = useState(() => (parsed ? formatStatValue(0, parsed) : value))

  useEffect(() => {
    if (!isInView || !parsed || prefersReducedMotion) {
      if (prefersReducedMotion) setDisplay(value)
      return
    }
    const controls = animate(0, parsed.numberOfValue, {
      duration: 1.4,
      ease: [0.16, 1, 0.3, 1],
      onUpdate(current) {
        setDisplay(formatStatValue(current, parsed))
      },
    })
    return () => controls.stop()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [isInView, prefersReducedMotion, value])

  if (!parsed) {
    return (
      <span ref={ref} className={className}>
        {value}
      </span>
    )
  }

  return (
    <span ref={ref} className={className}>
      {display}
    </span>
  )
}
