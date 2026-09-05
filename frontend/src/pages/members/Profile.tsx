import { useState } from 'react'
import { updateMe } from '@/api/auth'
import { SectionHeading, StatusTag } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useAuth } from '@/context/AuthContext'
import { formatDate } from '@/lib/format'

const preferences = [
  { id: 'pref-directory', label: 'List me in the members directory', detail: 'Name, sector and specialisation only. Contact details are never published.' },
  { id: 'pref-mentor', label: 'Available as a mentor', detail: 'The Membership Secretary may match you with a newly inducted member.' },
  { id: 'pref-attachment', label: 'Willing to host a practice attachment', detail: 'For members with a licensed firm.' },
  { id: 'pref-notices', label: 'Email me chapter notices', detail: 'Deadlines, circulars and the meeting agenda.' },
]

export default function MembersProfile() {
  const { user, signOut } = useAuth()
  const [phone, setPhone] = useState('')
  const [saving, setSaving] = useState(false)
  const [saved, setSaved] = useState(false)
  const [checked, setChecked] = useState<Record<string, boolean>>({
    'pref-directory': true,
    'pref-mentor': false,
    'pref-attachment': false,
    'pref-notices': true,
  })

  if (!user) return null

  async function handleSave() {
    setSaving(true)
    setSaved(false)
    try {
      await updateMe({ phone })
      setSaved(true)
    } finally {
      setSaving(false)
    }
  }

  return (
    <div className="space-y-12">
      <div>
        <SectionHeading title="Your record" lede="Details held against your chapter membership." className="mb-6" />
        <dl className="divide-y divide-border border-y border-border">
          <div className="grid gap-1 py-3.5 sm:grid-cols-[12rem_minmax(0,1fr)]">
            <dt className="text-[0.88rem] text-muted-foreground">Name</dt>
            <dd className="text-[0.92rem]">
              {user.name}, {user.credential}
            </dd>
          </div>
          <div className="grid gap-1 py-3.5 sm:grid-cols-[12rem_minmax(0,1fr)]">
            <dt className="text-[0.88rem] text-muted-foreground">Membership number</dt>
            <dd className="tnum text-[0.92rem]">{user.membershipNumber}</dd>
          </div>
          <div className="grid gap-1 py-3.5 sm:grid-cols-[12rem_minmax(0,1fr)]">
            <dt className="text-[0.88rem] text-muted-foreground">Email</dt>
            <dd className="text-[0.92rem]">{user.email}</dd>
          </div>
          <div className="grid gap-1 py-3.5 sm:grid-cols-[12rem_minmax(0,1fr)]">
            <dt className="text-[0.88rem] text-muted-foreground">Member since</dt>
            <dd className="text-[0.92rem]">{formatDate(user.joinedAt)}</dd>
          </div>
          <div className="grid gap-1 py-3.5 sm:grid-cols-[12rem_minmax(0,1fr)]">
            <dt className="text-[0.88rem] text-muted-foreground">Status</dt>
            <dd>
              <StatusTag tone={user.membershipStatus === 'active' ? 'positive' : 'warning'}>
                {user.membershipStatus}
              </StatusTag>
            </dd>
          </div>
        </dl>
        <p className="mt-4 text-[0.85rem] text-muted-foreground">
          Your name and membership number come from the ICAN roll. To correct either, update your
          ICAN profile and the chapter record follows.
        </p>
      </div>

      <div>
        <SectionHeading title="Contact details" className="mb-6" />
        <form
          className="grid max-w-lg gap-5 sm:grid-cols-2"
          onSubmit={(e) => {
            e.preventDefault()
            handleSave()
          }}
        >
          <div className="space-y-2">
            <Label htmlFor="profile-email">Email address</Label>
            <Input id="profile-email" type="email" defaultValue={user.email} disabled />
          </div>
          <div className="space-y-2">
            <Label htmlFor="profile-phone">Phone number</Label>
            <Input
              id="profile-phone"
              type="tel"
              placeholder="0800 000 0000"
              value={phone}
              onChange={(e) => setPhone(e.target.value)}
            />
          </div>
          <div className="sm:col-span-2 flex items-center gap-3">
            <Button type="submit" disabled={saving}>
              {saving ? 'Saving…' : 'Save changes'}
            </Button>
            {saved && <span className="text-[0.85rem] text-muted-foreground">Saved.</span>}
          </div>
        </form>
      </div>

      <div>
        <SectionHeading title="Preferences" className="mb-6" />
        <ul className="divide-y divide-border border-y border-border">
          {preferences.map((p) => (
            <li key={p.id} className="py-4">
              <label className="flex cursor-pointer items-start gap-3">
                <input
                  type="checkbox"
                  checked={checked[p.id]}
                  onChange={(e) => setChecked((c) => ({ ...c, [p.id]: e.target.checked }))}
                  className="mt-1 h-4 w-4 shrink-0 accent-[var(--primary)]"
                />
                <span>
                  <span className="block text-[0.93rem] font-medium">{p.label}</span>
                  <span className="mt-0.5 block text-[0.84rem] leading-relaxed text-muted-foreground">
                    {p.detail}
                  </span>
                </span>
              </label>
            </li>
          ))}
        </ul>
      </div>

      <div className="border border-border bg-card p-5">
        <h2 className="text-[1.05rem]">Sign out</h2>
        <p className="mt-2 text-[0.9rem] text-muted-foreground">
          Ends this session and clears it from browser storage.
        </p>
        <Button variant="outline" className="mt-4" onClick={signOut}>
          Sign out
        </Button>
      </div>
    </div>
  )
}
