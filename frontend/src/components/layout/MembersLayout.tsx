import { NavLink, Outlet } from 'react-router-dom'
import { PageHeader, Section, StatusTag } from '@/components/common/Primitives'
import { useAuth } from '@/context/AuthContext'
import { cn } from '@/lib/utils'

const railLinks = [
  { to: '/members', label: 'Overview', end: true },
  { to: '/members/cpd', label: 'CPD record' },
  { to: '/members/subscription', label: 'Subscription and welfare' },
  { to: '/members/tickets', label: 'Event tickets' },
  { to: '/members/profile', label: 'Profile' },
]

export function MembersLayout() {
  const { user } = useAuth()

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'Members area' }]}
        title={user ? `Good to see you, ${user.name.split(' ')[0]}` : 'Members area'}
        intro={
          user
            ? `${user.membershipNumber} · member since ${new Date(user.joinedAt).getFullYear()}`
            : undefined
        }
        aside={
          user && (
            <div className="border border-gold-500/40 bg-plum-800/60 p-4 text-plum-200">
              <p className="text-[0.78rem]">Membership status</p>
              <p className="mt-1.5 font-heading text-xl capitalize text-gold-300">
                {user.membershipStatus}
              </p>
            </div>
          )
        }
      />

      <Section>
        <div className="grid gap-10 lg:grid-cols-[14rem_minmax(0,1fr)]">
          <nav aria-label="Members area" className="lg:sticky lg:top-32 lg:self-start">
            <ul className="flex gap-1 overflow-x-auto border-b border-border pb-px lg:flex-col lg:gap-0 lg:overflow-visible lg:border-b-0 lg:border-l lg:border-rule lg:pb-0">
              {railLinks.map((link) => (
                <li key={link.to}>
                  <NavLink
                    to={link.to}
                    end={link.end}
                    className={({ isActive }) =>
                      cn(
                        'block whitespace-nowrap px-4 py-2.5 text-[0.88rem] font-medium transition-colors lg:-ml-px lg:border-l-2',
                        isActive
                          ? 'border-b-2 border-gold-500 text-plum-700 lg:border-b-0 lg:border-l-gold-500 dark:text-primary'
                          : 'border-transparent text-muted-foreground hover:text-foreground',
                      )
                    }
                  >
                    {link.label}
                  </NavLink>
                </li>
              ))}
            </ul>

            {user?.membershipStatus === 'pending' && (
              <div className="mt-6 hidden border border-border bg-card p-4 lg:block">
                <StatusTag tone="warning">Awaiting confirmation</StatusTag>
                <p className="mt-2 text-[0.82rem] leading-relaxed text-muted-foreground">
                  The Financial Secretary confirms payments within two working days. Member rates
                  apply once your record is confirmed.
                </p>
              </div>
            )}
          </nav>

          <div className="min-w-0">
            <Outlet />
          </div>
        </div>
      </Section>
    </>
  )
}
