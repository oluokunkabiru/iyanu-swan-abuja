import { useState } from 'react'
import { Link, NavLink, Outlet, useLocation } from 'react-router-dom'
import { ArrowLeft, ArrowUpRight, ChevronRight, CircleHelp, LayoutDashboard, LogOut, Menu, ShieldCheck, Ticket, Wallet } from 'lucide-react'
import { MotionConfig } from 'framer-motion'
import { BrandLogo } from '@/components/common/BrandLogo'
import { StatusTag } from '@/components/common/Primitives'
import { ThemeToggle } from '@/components/ThemeToggle'
import { Button } from '@/components/ui/button'
import { Dialog, DialogContent, DialogDescription, DialogTitle, DialogTrigger } from '@/components/ui/dialog'
import { useAuth } from '@/context/AuthContext'
import { DuesProvider } from '@/context/DuesContext'
import { MembershipDuesSummary } from '@/components/sections/MembershipDues'
import { MemberAvatar } from '@/components/sections/MemberIdentity'
import { profileSectionPath, profileSections } from '@/data/memberProfile'
import { cn } from '@/lib/utils'

export function MembersLayout() {
  const { user, signOut } = useAuth()
  const { pathname } = useLocation()
  const [mobileOpen, setMobileOpen] = useState(false)
  if (!user) return null
  const profileSection = profileSections.find(section => profileSectionPath(section.id) === pathname)
  const pageTitle = profileSection?.label ?? (pathname.endsWith('/subscription') ? 'Subscription and welfare' : pathname.endsWith('/tickets') ? 'Event tickets' : 'Dashboard')
  const linkClass = ({ isActive }: { isActive: boolean }) => cn(
    'flex items-center gap-3 rounded-lg px-3 py-2.5 text-[0.86rem] font-medium transition-colors',
    isActive ? 'bg-white text-plum-900 shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white',
  )
  function sidebar() {
    return (
      <>
        <Link to="/" className="mx-5 mt-7 block rounded-lg bg-white px-3 py-3" onClick={() => setMobileOpen(false)}><BrandLogo className="max-h-10 w-full" /></Link>
        <p className="px-6 pt-4 pb-6 text-[0.68rem] font-semibold tracking-[0.2em] text-white/50 uppercase">Abuja Chapter · Members portal</p>
        <nav aria-label="Members area" className="member-sidebar-scroll min-h-0 flex-1 space-y-6 overflow-y-auto px-4 pb-6">
          <NavLink to="/members/overview" end className={linkClass} onClick={() => setMobileOpen(false)}><LayoutDashboard className="h-4 w-4" aria-hidden="true" />Overview</NavLink>
          <div>
            <p className="mb-2 px-3 text-[0.65rem] font-semibold tracking-[0.18em] text-white/40 uppercase">My profile</p>
            <ul className="space-y-1">{profileSections.map(section => (
              <li key={section.id}><NavLink to={profileSectionPath(section.id)} end className={linkClass} onClick={() => setMobileOpen(false)}><section.icon className="h-4 w-4 shrink-0" aria-hidden="true" />{section.label}</NavLink></li>
            ))}</ul>
          </div>
          <div>
            <p className="mb-2 px-3 text-[0.65rem] font-semibold tracking-[0.18em] text-white/40 uppercase">My account</p>
            <ul className="space-y-1">
              <li><NavLink to="/members/subscription" className={linkClass} onClick={() => setMobileOpen(false)}><Wallet className="h-4 w-4" aria-hidden="true" />Subscription & welfare</NavLink></li>
              <li><NavLink to="/members/tickets" className={linkClass} onClick={() => setMobileOpen(false)}><Ticket className="h-4 w-4" aria-hidden="true" />Event tickets</NavLink></li>
              <li><Link to="/change-password" className="flex items-center gap-3 rounded-lg px-3 py-2.5 text-[0.86rem] text-white/70 hover:bg-white/10 hover:text-white"><ShieldCheck className="h-4 w-4" aria-hidden="true" />Password & security</Link></li>
            </ul>
          </div>
        </nav>
        <div className="border-t border-white/10 p-4">
          <Link to="/contact" className="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-white/70 hover:text-white"><CircleHelp className="h-4 w-4" aria-hidden="true" />Help & support<ArrowUpRight className="ml-auto h-3.5 w-3.5" aria-hidden="true" /></Link>
          <button type="button" onClick={signOut} className="mt-1 flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm text-white/70 hover:bg-white/10 hover:text-white"><LogOut className="h-4 w-4" aria-hidden="true" />Sign out</button>
        </div>
      </>
    )
  }
  return (
    <MotionConfig reducedMotion="user">
      <DuesProvider>
      <div className="min-h-screen bg-secondary/35">
        <a href="#member-content" className="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:rounded focus:bg-card focus:p-3">Skip to content</a>
        <aside className="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col bg-plum-900 lg:flex">{sidebar()}</aside>
        <div className="min-w-0 lg:pl-64">
          <header className="sticky top-0 z-30 flex h-18 items-center justify-between gap-3 border-b border-border bg-card px-4 sm:px-8">
            <div className="flex min-w-0 items-center gap-3">
              <Dialog open={mobileOpen} onOpenChange={setMobileOpen}>
                <DialogTrigger asChild><Button variant="outline" size="icon" className="lg:hidden" aria-label="Open member menu"><Menu className="h-5 w-5" /></Button></DialogTrigger>
                <DialogContent className="top-0 left-0 flex h-dvh w-72 max-w-[85vw] translate-x-0 translate-y-0 flex-col gap-0 overflow-hidden rounded-none border-0 bg-plum-900 p-0 text-white sm:max-w-72">
                  <DialogTitle className="sr-only">Member navigation</DialogTitle>
                  <DialogDescription className="sr-only">Your profile sections and membership account.</DialogDescription>
                  {sidebar()}
                </DialogContent>
              </Dialog>
              <span className="hidden text-sm text-muted-foreground sm:inline">Members portal</span><ChevronRight className="hidden h-3.5 w-3.5 text-muted-foreground sm:block" aria-hidden="true" /><span className="truncate text-sm font-semibold">{pageTitle}</span>
            </div>
            <div className="flex shrink-0 items-center gap-2 sm:gap-4">
              <Link to="/" className="hidden items-center gap-2 text-[0.8rem] font-medium text-muted-foreground hover:text-foreground md:inline-flex"><ArrowLeft className="h-3.5 w-3.5" aria-hidden="true" />Visit website</Link><ThemeToggle />
              <Link to="/members/profile" aria-label="Your member profile" className="flex min-w-0 items-center gap-2 border-l border-border pl-3">
                <MemberAvatar user={user} className="h-9 w-9 rounded-full border-2 [&_[data-slot=avatar-fallback]]:rounded-full [&_[data-slot=avatar-fallback]]:text-xs" />
                <span className="min-w-0"><span className="block max-w-24 truncate text-xs font-semibold sm:max-w-40 sm:text-sm">{user.firstName}</span><span className="block max-w-24 truncate text-[0.65rem] text-muted-foreground sm:max-w-40" title={user.membershipNumber || undefined}>ID: {user.membershipNumber || 'Not assigned'}</span></span>
              </Link>
            </div>
          </header>
          <main id="member-content" className="mx-auto max-w-[100rem] px-4 py-7 sm:px-8 sm:py-9">
            <div className="mb-7 flex flex-wrap items-start justify-between gap-4">
              <div><p className="mb-2 text-[0.67rem] font-semibold tracking-[0.18em] text-muted-foreground uppercase">Your chapter. Your community.</p><h1 className="text-2xl sm:text-3xl">{profileSection ? 'My profile' : pageTitle === 'Dashboard' ? `Welcome back, ${user.firstName}` : pageTitle}</h1><p className="mt-2 text-sm text-muted-foreground">{profileSection ? 'Keep your membership record complete and up to date.' : 'Manage your chapter membership, payments and activities.'}</p></div>
              <div className="flex items-center gap-2 rounded-full border border-border bg-card px-3 py-2 text-xs"><span className="text-muted-foreground">Membership</span><StatusTag tone={user.membershipStatus === 'active' ? 'positive' : 'warning'}>{user.membershipStatus}</StatusTag></div>
            </div>
            {pathname !== '/members/overview' && <MembershipDuesSummary />}
            <Outlet />
          </main>
          <footer className="px-4 pb-6 text-center text-xs text-muted-foreground sm:px-8">© {new Date().getFullYear()} Society of Women Accountants of Nigeria, Abuja Chapter</footer>
        </div>
      </div>
      </DuesProvider>
    </MotionConfig>
  )
}
