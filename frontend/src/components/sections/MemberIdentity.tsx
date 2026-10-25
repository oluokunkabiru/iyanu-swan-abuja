import { Link } from 'react-router-dom'
import { Check, Pencil } from 'lucide-react'
import { StatusTag } from '@/components/common/Primitives'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import { cn } from '@/lib/utils'
import type { AuthUser } from '@/types'

export function MemberAvatar({ user, className, preview }: { user: AuthUser; className?: string; preview?: string | null }) {
  return (
    <Avatar className={cn('rounded-xl border-4 border-background bg-secondary shadow-sm', className)}>
      <AvatarImage src={preview ?? user.photoUrl ?? undefined} alt={`${user.name}'s profile photo`} className="aspect-auto h-full w-full object-cover object-top" />
      <AvatarFallback className="rounded-lg bg-secondary text-2xl text-primary" aria-label={`${user.name}'s initials`}>
        {user.name.split(' ').filter(Boolean).slice(0, 2).map(part => part[0]).join('')}
      </AvatarFallback>
    </Avatar>
  )
}

export function MemberSummary({ user, showPhoto = true, showProfileLink = true }: { user: AuthUser; showPhoto?: boolean; showProfileLink?: boolean }) {
  const details = [
    { label: 'Membership ID', value: user.membershipNumber || 'Not assigned' },
    { label: 'Membership level', value: user.credential || 'Not provided' },
    { label: 'Email address', value: user.email },
    { label: 'Mobile telephone', value: user.phone || 'Not provided' },
    { label: 'Place of work', value: user.placeOfWork || 'Not provided' },
    { label: 'Sector', value: user.sector || 'Not provided' },
  ]

  return (
    <section aria-label="Member information" className={cn('grid gap-x-6 rounded-xl border border-border bg-card p-5 shadow-sm sm:p-7', showPhoto && 'grid-cols-[minmax(0,1fr)_auto]')}>
      <div className="min-w-0">
        <p className="text-[0.67rem] font-semibold tracking-[0.15em] text-muted-foreground uppercase">Your member profile</p>
        <h2 className="mt-2 break-words text-xl sm:text-2xl">{[user.title, user.name].filter(Boolean).join(' ')}</h2>
        <div className="mt-3 flex flex-wrap items-center gap-3">
          <StatusTag tone={user.membershipStatus === 'active' ? 'positive' : 'warning'}>{user.membershipStatus}</StatusTag>
          <span className="flex items-center gap-1.5 text-xs text-muted-foreground">
            {user.emailVerified && <Check className="h-3.5 w-3.5" aria-hidden="true" />}
            {user.emailVerified ? 'Verified email' : 'Email not verified'}
          </span>
        </div>
      </div>
      {showPhoto && <MemberAvatar user={user} className="col-start-2 row-start-1 h-28 w-24 self-start sm:row-span-3 sm:h-40 sm:w-36" />}
      <dl className={cn('mt-6 grid min-w-0 gap-x-6 gap-y-5 border-t border-border pt-5 sm:grid-cols-2 xl:grid-cols-3', showPhoto && 'col-span-2 sm:col-span-1 sm:col-start-1')}>
        {details.map(detail => (
          <div key={detail.label} className="min-w-0">
            <dt className="text-[0.67rem] font-semibold tracking-wider text-muted-foreground uppercase">{detail.label}</dt>
            <dd className={cn('mt-1.5 break-words text-sm font-medium', detail.label === 'Membership ID' && 'tnum')}>{detail.value}</dd>
          </div>
        ))}
      </dl>
      {showProfileLink && <Button asChild size="sm" variant="outline" className="mt-6 justify-self-start"><Link to="/members/profile"><Pencil className="h-3.5 w-3.5" aria-hidden="true" />Update profile</Link></Button>}
    </section>
  )
}
