import { useEffect, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { CalendarCheck, LogOut } from 'lucide-react'
import { fetchMyRegistrations, updateMe } from '@/api/auth'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useAuth } from '@/context/AuthContext'
import type { EventRegistration } from '@/types'

const statusLabel: Record<string, string> = {
  pending: 'Pending',
  active: 'Active',
  expired: 'Expired',
}

function initials(name: string) {
  return name
    .split(' ')
    .map((part) => part[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
}

export default function Dashboard() {
  const { user, logout, refresh } = useAuth()
  const navigate = useNavigate()
  const [registrations, setRegistrations] = useState<EventRegistration[]>([])
  const [phone, setPhone] = useState(user?.member_profile?.phone ?? '')
  const [saving, setSaving] = useState(false)
  const [saved, setSaved] = useState(false)

  useEffect(() => {
    fetchMyRegistrations().then(setRegistrations).catch(() => setRegistrations([]))
  }, [])

  if (!user) return null

  async function handleLogout() {
    await logout()
    navigate('/')
  }

  async function handleSaveProfile() {
    setSaving(true)
    try {
      await updateMe({ phone })
      await refresh()
      setSaved(true)
      setTimeout(() => setSaved(false), 2000)
    } finally {
      setSaving(false)
    }
  }

  return (
    <section className="mx-auto max-w-4xl px-4 py-16">
      <div className="fade-up flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Avatar className="h-12 w-12 ring-4 ring-background shadow-md shadow-primary/10">
            <AvatarFallback className="bg-secondary font-heading font-semibold text-primary">
              {initials(user.name)}
            </AvatarFallback>
          </Avatar>
          <div>
            <h1 className="font-heading text-2xl font-bold sm:text-3xl">Welcome, {user.name}</h1>
            <p className="text-sm text-muted-foreground">{user.email}</p>
          </div>
        </div>
        <Button variant="outline" onClick={handleLogout}>
          <LogOut className="h-4 w-4" /> Log out
        </Button>
      </div>

      <div className="fade-up mt-10 grid gap-6 sm:grid-cols-2 [animation-delay:100ms]">
        <Card className="rounded-2xl">
          <CardHeader>
            <CardTitle className="font-heading">Membership status</CardTitle>
          </CardHeader>
          <CardContent className="space-y-3 text-sm">
            <div className="flex items-center justify-between">
              <span className="text-muted-foreground">Status</span>
              <Badge variant={user.member_profile?.membership_status === 'active' ? 'default' : 'secondary'}>
                {statusLabel[user.member_profile?.membership_status ?? 'pending']}
              </Badge>
            </div>
            {user.member_profile?.membership_number && (
              <div className="flex items-center justify-between">
                <span className="text-muted-foreground">Member number</span>
                <span className="font-medium">{user.member_profile.membership_number}</span>
              </div>
            )}
            {user.member_profile?.joined_at && (
              <div className="flex items-center justify-between">
                <span className="text-muted-foreground">Joined</span>
                <span className="font-medium">{new Date(user.member_profile.joined_at).toLocaleDateString()}</span>
              </div>
            )}
          </CardContent>
        </Card>

        <Card className="rounded-2xl">
          <CardHeader>
            <CardTitle className="font-heading">Profile</CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            <div className="space-y-2">
              <Label htmlFor="phone">Phone</Label>
              <Input id="phone" value={phone} onChange={(e) => setPhone(e.target.value)} />
            </div>
            <Button size="sm" onClick={handleSaveProfile} disabled={saving}>
              {saving ? 'Saving…' : saved ? 'Saved ✓' : 'Save'}
            </Button>
          </CardContent>
        </Card>
      </div>

      <Card className="fade-up mt-6 rounded-2xl [animation-delay:150ms]">
        <CardHeader>
          <CardTitle className="font-heading">My event registrations</CardTitle>
        </CardHeader>
        <CardContent>
          {registrations.length === 0 ? (
            <div className="flex flex-col items-center py-8 text-center text-muted-foreground">
              <CalendarCheck className="h-8 w-8 text-muted-foreground/50" />
              <p className="mt-2 text-sm">You haven't registered for any events yet.</p>
            </div>
          ) : (
            <div className="space-y-3">
              {registrations.map((registration) => (
                <div
                  key={registration.id}
                  className="flex items-center justify-between rounded-xl border bg-background px-4 py-3 text-sm"
                >
                  <div>
                    <p className="font-medium">{registration.event?.title}</p>
                    <p className="text-muted-foreground">{registration.ticket_type?.label}</p>
                  </div>
                  <Badge variant={registration.payment_status === 'confirmed' ? 'default' : 'secondary'}>
                    {registration.payment_status === 'confirmed' ? 'Confirmed' : 'Pending'}
                  </Badge>
                </div>
              ))}
            </div>
          )}
        </CardContent>
      </Card>
    </section>
  )
}
