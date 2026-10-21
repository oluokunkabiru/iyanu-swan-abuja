import { useEffect } from 'react'
import { Outlet, useLocation } from 'react-router-dom'
import { AnimatePresence, MotionConfig, motion } from 'framer-motion'
import { Footer } from '@/components/layout/Footer'
import { Header } from '@/components/layout/Header'
import { pageVariants } from '@/lib/animations'

function ScrollToTop() {
  const { pathname, hash } = useLocation()

  useEffect(() => {
    if (hash) {
      const el = document.querySelector(hash)
      if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' })
        return
      }
    }
    window.scrollTo({ top: 0 })
  }, [pathname, hash])

  return null
}

export function Layout() {
  const { pathname } = useLocation()
  const isHome = pathname === '/'

  return (
    <MotionConfig reducedMotion="user">
      <div className="flex min-h-screen flex-col">
        <a
          href="#main"
          className="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-sm focus:bg-primary focus:px-4 focus:py-2 focus:text-primary-foreground"
        >
          Skip to content
        </a>
        <ScrollToTop />
        <Header />
        <AnimatePresence mode="wait">
          <motion.main
            id="main"
            className="flex-1"
            initial={isHome ? false : 'initial'}
            animate="animate"
            exit="exit"
            // The homepage animates individual components so the slider stays still.
            variants={isHome ? undefined : pageVariants}
            key={pathname}
          >
            <Outlet />
          </motion.main>
        </AnimatePresence>
        <Footer />
      </div>
    </MotionConfig>
  )
}
