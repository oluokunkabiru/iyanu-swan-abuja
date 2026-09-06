import { useEffect, useRef, useState } from 'react'
import { Link, NavLink, useLocation } from 'react-router-dom'
import { ChevronDown, LogOut, Menu, Search, X } from 'lucide-react'
import { BrandLogo } from '@/components/common/BrandLogo'
import { Button } from '@/components/ui/button'
import { ThemeToggle } from '@/components/ThemeToggle'
import { useAuth } from '@/context/AuthContext'
import { useSettings } from '@/context/SettingsContext'
import { navigation } from '@/data'
import { cn } from '@/lib/utils'
import type { NavSection } from '@/types'

export function Header() {
  const { user, signOut } = useAuth()
  const { settings } = useSettings()
  const location = useLocation()
  const [openSection, setOpenSection] = useState<string | null>(null)
  const [drawerOpen, setDrawerOpen] = useState(false)
  const [drawerExpanded, setDrawerExpanded] = useState<string | null>(null)
  const closeTimer = useRef<number | null>(null)

  useEffect(() => {
    setOpenSection(null)
    setDrawerOpen(false)
    setDrawerExpanded(null)
  }, [location.pathname])

  useEffect(() => {
    function onKey(e: KeyboardEvent) {
      if (e.key === 'Escape') {
        setOpenSection(null)
        setDrawerOpen(false)
      }
    }
    window.addEventListener('keydown', onKey)
    return () => window.removeEventListener('keydown', onKey)
  }, [])

  function scheduleClose() {
    if (closeTimer.current) window.clearTimeout(closeTimer.current)
    closeTimer.current = window.setTimeout(() => setOpenSection(null), 140)
  }

  function cancelClose() {
    if (closeTimer.current) window.clearTimeout(closeTimer.current)
  }

  return (
    <header className="sticky top-0 z-50">
      <div className="hidden border-b border-plum-800/60 bg-plum-900 text-plum-200 md:block">
        <div className="mx-auto flex h-9 max-w-7xl items-center justify-between px-4 text-[0.78rem]">
          <p>
          The Society{' '}
            <a
              href="https://icanig.org/ican/"
              target="_blank"
              rel="noreferrer"
              className="text-gold-300 underline-offset-4 hover:underline"
            >
              {settings?.parentBody}
            </a>
          </p>
          <div className="flex items-center gap-5">
            <a href={`mailto:${settings?.email}`} className="hover:text-white">
              {settings?.email}
            </a>
            <Link to="/members" className="hover:text-white">
              Members portal
            </Link>
          </div>
        </div>
      </div>

      <div className="border-b border-border bg-background/95 backdrop-blur">
        <div className="mx-auto flex h-[4.75rem] max-w-[96rem] items-center gap-3 px-4">
          <Link to="/" aria-label={`${settings?.shortName ?? 'SWAN Abuja'} home`} className="shrink-0">
            <BrandLogo className="h-7 sm:h-10" />
          </Link>

          <nav className="ml-auto hidden min-w-0 items-center min-[1200px]:flex" aria-label="Main">
            {navigation.map((section) => (
              <MegaItem
                key={section.label}
                section={section}
                isOpen={openSection === section.label}
                onOpen={() => {
                  cancelClose()
                  setOpenSection(section.label)
                }}
                onClose={scheduleClose}
                onCancelClose={cancelClose}
              />
            ))}
          </nav>

          <div className="ml-auto flex items-center gap-1.5 min-[1200px]:ml-2">
            <Link
              to="/directory"
              aria-label="Search the directories"
              className="hidden h-9 w-9 items-center justify-center rounded-sm text-muted-foreground hover:bg-secondary hover:text-foreground md:flex"
            >
              <Search className="h-[1.05rem] w-[1.05rem]" />
            </Link>
            <ThemeToggle />
            {user ? (
              <div className="hidden items-center gap-2 md:flex">
                <Button asChild size="sm">
                  <Link to="/members">Members area</Link>
                </Button>
                <button
                  type="button"
                  onClick={signOut}
                  aria-label="Sign out"
                  className="flex h-9 w-9 items-center justify-center rounded-sm text-muted-foreground hover:bg-secondary hover:text-foreground"
                >
                  <LogOut className="h-[1.05rem] w-[1.05rem]" />
                </button>
              </div>
            ) : (
              <div className="hidden items-center gap-2 md:flex">
                <Button variant="ghost" size="sm" asChild>
                  <Link to="/login">Sign in</Link>
                </Button>
                <Button size="sm" asChild>
                  <Link to="/membership/register">Join the chapter</Link>
                </Button>
              </div>
            )}
            <button
              type="button"
              onClick={() => setDrawerOpen((v) => !v)}
              aria-label={drawerOpen ? 'Close menu' : 'Open menu'}
              aria-expanded={drawerOpen}
              className="flex h-9 w-9 items-center justify-center rounded-sm text-foreground min-[1200px]:hidden"
            >
              {drawerOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
            </button>
          </div>
        </div>
      </div>

      {drawerOpen && (
        <div className="max-h-[calc(100dvh-4.25rem)] overflow-y-auto border-b border-border bg-background min-[1200px]:hidden">
          <nav className="mx-auto max-w-6xl px-4 py-3" aria-label="Mobile">
            {navigation.map((section) =>
              section.columns ? (
                <div key={section.label} className="border-b border-border/70 last:border-0">
                  <button
                    type="button"
                    onClick={() =>
                      setDrawerExpanded((cur) => (cur === section.label ? null : section.label))
                    }
                    aria-expanded={drawerExpanded === section.label}
                    className="flex w-full items-center justify-between py-3 text-left text-[0.95rem] font-medium"
                  >
                    {section.label}
                    <ChevronDown
                      className={cn(
                        'h-4 w-4 text-muted-foreground transition-transform',
                        drawerExpanded === section.label && 'rotate-180',
                      )}
                    />
                  </button>
                  {drawerExpanded === section.label && (
                    <div className="pb-3">
                      {section.columns.map((col) => (
                        <div key={col.heading} className="mb-3 last:mb-0">
                          <p className="mb-1.5 text-[0.75rem] font-semibold text-accent-foreground">
                            {col.heading}
                          </p>
                          <ul className="space-y-0.5 border-l border-rule pl-3">
                            {col.items.map((item) => (
                              <li key={item.to + item.label}>
                                <Link
                                  to={item.to}
                                  className="block py-1.5 text-[0.9rem] text-muted-foreground hover:text-foreground"
                                >
                                  {item.label}
                                </Link>
                              </li>
                            ))}
                          </ul>
                        </div>
                      ))}
                    </div>
                  )}
                </div>
              ) : (
                <Link
                  key={section.label}
                  to={section.to ?? '/'}
                  className="block border-b border-border/70 py-3 text-[0.95rem] font-medium last:border-0"
                >
                  {section.label}
                </Link>
              ),
            )}

            <div className="mt-4 flex flex-col gap-2 pb-4">
              {user ? (
                <>
                  <Button asChild>
                    <Link to="/members">Members area</Link>
                  </Button>
                  <Button variant="outline" onClick={signOut}>
                    Sign out
                  </Button>
                </>
              ) : (
                <>
                  <Button asChild>
                    <Link to="/membership/register">Join the chapter</Link>
                  </Button>
                  <Button variant="outline" asChild>
                    <Link to="/login">Sign in</Link>
                  </Button>
                </>
              )}
            </div>
          </nav>
        </div>
      )}
    </header>
  )
}

function MegaItem({
  section,
  isOpen,
  onOpen,
  onClose,
  onCancelClose,
}: {
  section: NavSection
  isOpen: boolean
  onOpen: () => void
  onClose: () => void
  onCancelClose: () => void
}) {
  if (!section.columns) {
    return (
      <NavLink
        to={section.to ?? '/'}
        end={section.to === '/'}
        className={({ isActive }) =>
          cn(
            'px-2 py-2 text-[0.85rem] font-medium whitespace-nowrap transition-colors',
            isActive
              ? 'text-plum-700 dark:text-primary'
              : 'text-muted-foreground hover:text-foreground',
          )
        }
      >
        {section.label}
      </NavLink>
    )
  }

  return (
    <div onMouseEnter={onOpen} onMouseLeave={onClose}>
      <button
        type="button"
        onClick={() => (isOpen ? onClose() : onOpen())}
        onFocus={onOpen}
        aria-expanded={isOpen}
        className={cn(
          'flex items-center gap-1 px-2 py-2 text-[0.85rem] font-medium whitespace-nowrap transition-colors',
          isOpen ? 'text-plum-700 dark:text-primary' : 'text-muted-foreground hover:text-foreground',
        )}
      >
        {section.label}
        <ChevronDown className={cn('h-3.5 w-3.5 transition-transform', isOpen && 'rotate-180')} />
      </button>

      {isOpen && (
        <div
          onMouseEnter={onCancelClose}
          onMouseLeave={onClose}
          className="absolute inset-x-0 top-full border-b border-border bg-card shadow-lg shadow-primary/10"
        >
          <div className="mx-auto grid max-w-6xl gap-8 px-4 py-8 lg:grid-cols-[repeat(3,minmax(0,1fr))_20rem]">
            {section.columns.map((col) => (
              <div key={col.heading}>
                <p className="border-b border-gold-500/40 pb-2 text-[0.78rem] font-semibold text-accent-foreground">
                  {col.heading}
                </p>
                <ul className="mt-3 space-y-2.5">
                  {col.items.map((item) => (
                    <li key={item.to + item.label}>
                      <Link to={item.to} className="group block">
                        <span className="text-[0.9rem] font-medium text-foreground group-hover:text-plum-700 dark:group-hover:text-primary">
                          {item.label}
                        </span>
                        {item.description && (
                          <span className="mt-0.5 block text-[0.78rem] leading-snug text-muted-foreground">
                            {item.description}
                          </span>
                        )}
                      </Link>
                    </li>
                  ))}
                </ul>
              </div>
            ))}

            {section.feature && (
              <div className="bg-plum-900 p-5 text-plum-200">
                <h3 className="font-heading text-[1.05rem] leading-snug text-white">
                  {section.feature.title}
                </h3>
                <p className="mt-2 text-[0.82rem] leading-relaxed">{section.feature.body}</p>
                <Link
                  to={section.feature.to}
                  className="mt-4 inline-block border-b border-gold-500 pb-0.5 text-[0.82rem] font-semibold text-gold-300"
                >
                  {section.feature.cta}
                </Link>
              </div>
            )}
          </div>
        </div>
      )}
    </div>
  )
}
