import { useMemo, useState } from 'react'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { EmptyState, SectionHeading, StatusTag } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Textarea } from '@/components/ui/textarea'
import { useAuth } from '@/context/AuthContext'
import { formatDate } from '@/lib/format'
import type { NotificationEmailPreference } from '@/types'

const notificationPreferenceOptions: { value: NotificationEmailPreference; label: string }[] = [
  { value: 'registered', label: 'Registered email only' },
  { value: 'personal', label: 'Personal email only' },
  { value: 'official', label: 'Official email only' },
  { value: 'all', label: 'All emails on file' },
]

const preferences = [
  { id: 'pref-mentor', label: 'Available as a mentor', detail: 'The Membership Secretary may match you with a newly inducted member.' },
  { id: 'pref-attachment', label: 'Willing to host a practice attachment', detail: 'For members with a licensed firm.' },
  { id: 'pref-notices', label: 'Email me chapter notices', detail: 'Deadlines, circulars and the meeting agenda.' },
]

function initials(name: string): string {
  return name
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0]?.toUpperCase())
    .join('')
}

export default function MembersProfile() {
  const { user, signOut, updateProfile } = useAuth()
  const [phone, setPhone] = useState(user?.phone ?? '')
  const [residentialAddress, setResidentialAddress] = useState(user?.residentialAddress ?? '')
  const [placeOfWork, setPlaceOfWork] = useState(user?.placeOfWork ?? '')
  const [dateOfBirth, setDateOfBirth] = useState(user?.dateOfBirth ?? '')
  const [isDirectoryListed, setIsDirectoryListed] = useState(user?.isDirectoryListed ?? true)
  const [photo, setPhoto] = useState<File | null>(null)
  const [saving, setSaving] = useState(false)
  const [saved, setSaved] = useState(false)
  const [personalEmail, setPersonalEmail] = useState(user?.personalEmail ?? '')
  const [officialEmail, setOfficialEmail] = useState(user?.officialEmail ?? '')
  const [notificationPreference, setNotificationPreference] = useState<NotificationEmailPreference | ''>(
    user?.notificationEmailPreference ?? '',
  )
  const [savingEmails, setSavingEmails] = useState(false)
  const [savedEmails, setSavedEmails] = useState(false)
  const [checked, setChecked] = useState<Record<string, boolean>>({
    'pref-mentor': false,
    'pref-attachment': false,
    'pref-notices': true,
  })

  const photoPreviewUrl = useMemo(() => (photo ? URL.createObjectURL(photo) : null), [photo])

  if (!user) return null

  async function handleSave() {
    setSaving(true)
    setSaved(false)
    try {
      await updateProfile({
        phone,
        residentialAddress,
        placeOfWork,
        dateOfBirth: dateOfBirth || undefined,
        isDirectoryListed,
        photo: photo ?? undefined,
      })
      setPhoto(null)
      setSaved(true)
    } finally {
      setSaving(false)
    }
  }

  async function handleSaveEmails() {
    setSavingEmails(true)
    setSavedEmails(false)
    try {
      await updateProfile({
        personalEmail,
        officialEmail,
        notificationEmailPreference: notificationPreference,
      })
      setSavedEmails(true)
    } finally {
      setSavingEmails(false)
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
          <div className="grid gap-1 py-3.5 sm:grid-cols-[12rem_minmax(0,1fr)]">
            <dt className="text-[0.88rem] text-muted-foreground">Sector</dt>
            <dd className="text-[0.92rem]">{user.sector ?? '—'}</dd>
          </div>
          <div className="grid gap-1 py-3.5 sm:grid-cols-[12rem_minmax(0,1fr)]">
            <dt className="text-[0.88rem] text-muted-foreground">Specialisation</dt>
            <dd className="text-[0.92rem]">{user.specialisation ?? '—'}</dd>
          </div>
          <div className="grid gap-1 py-3.5 sm:grid-cols-[12rem_minmax(0,1fr)]">
            <dt className="text-[0.88rem] text-muted-foreground">Year admitted</dt>
            <dd className="tnum text-[0.92rem]">{user.yearAdmitted ?? '—'}</dd>
          </div>
          {user.chapterRole && (
            <div className="grid gap-1 py-3.5 sm:grid-cols-[12rem_minmax(0,1fr)]">
              <dt className="text-[0.88rem] text-muted-foreground">Chapter role</dt>
              <dd className="text-[0.92rem]">{user.chapterRole}</dd>
            </div>
          )}
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
          <div className="sm:col-span-2 flex items-center gap-4">
            <Avatar className="h-16 w-16">
              <AvatarImage src={photoPreviewUrl ?? (user.photoUrl ?? undefined)} alt="" />
              <AvatarFallback className="bg-secondary text-secondary-foreground">
                {initials(user.name)}
              </AvatarFallback>
            </Avatar>
            <div className="space-y-1.5">
              <Label htmlFor="profile-photo">Profile photo</Label>
              <Input
                id="profile-photo"
                type="file"
                accept="image/jpeg,image/png,image/webp"
                onChange={(e) => setPhoto(e.target.files?.[0] ?? null)}
                className="max-w-xs"
              />
            </div>
          </div>
          <div className="space-y-2">
            <Label htmlFor="profile-email">Email address</Label>
            <Input id="profile-email" type="email" defaultValue={user.email} disabled />
          </div>
          <div className="space-y-2">
            <Label htmlFor="profile-phone">WhatsApp telephone number</Label>
            <Input
              id="profile-phone"
              type="tel"
              placeholder="0800 000 0000"
              value={phone}
              onChange={(e) => setPhone(e.target.value)}
            />
          </div>
          <div className="space-y-2">
            <Label htmlFor="profile-dob">Date of birth</Label>
            <Input
              id="profile-dob"
              type="date"
              value={dateOfBirth}
              onChange={(e) => setDateOfBirth(e.target.value)}
            />
            <p className="text-[0.78rem] text-muted-foreground">
              Used only to send you a birthday greeting.
            </p>
          </div>
          <div className="space-y-2">
            <Label htmlFor="profile-place-of-work">Place of work</Label>
            <Input
              id="profile-place-of-work"
              placeholder="Employer or firm name"
              value={placeOfWork}
              onChange={(e) => setPlaceOfWork(e.target.value)}
            />
          </div>
          <div className="sm:col-span-2 space-y-2">
            <Label htmlFor="profile-address">Residential address</Label>
            <Textarea
              id="profile-address"
              placeholder="Street, city and state"
              rows={2}
              value={residentialAddress}
              onChange={(e) => setResidentialAddress(e.target.value)}
            />
          </div>
          <div className="sm:col-span-2">
            <label className="flex cursor-pointer items-start gap-3">
              <input
                type="checkbox"
                checked={isDirectoryListed}
                onChange={(e) => setIsDirectoryListed(e.target.checked)}
                className="mt-1 h-4 w-4 shrink-0 accent-[var(--primary)]"
              />
              <span>
                <span className="block text-[0.93rem] font-medium">
                  List me in the members directory
                </span>
                <span className="mt-0.5 block text-[0.84rem] leading-relaxed text-muted-foreground">
                  Name, photo, sector and role only. Contact details are never published.
                </span>
              </span>
            </label>
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
        <SectionHeading
          title="Notification emails"
          lede="Which of your emails should chapter notices go to."
          className="mb-6"
        />
        {!user.emailVerified ? (
          <EmptyState
            title="Verify your registered email first"
            body="Once you confirm the link we sent to your registered email, you can add a personal or official email and choose where notices go."
          />
        ) : (
          <form
            className="grid max-w-lg gap-5 sm:grid-cols-2"
            onSubmit={(e) => {
              e.preventDefault()
              handleSaveEmails()
            }}
          >
            <div className="space-y-2">
              <Label htmlFor="profile-personal-email">Personal email</Label>
              <Input
                id="profile-personal-email"
                type="email"
                placeholder="you@example.com"
                value={personalEmail}
                onChange={(e) => setPersonalEmail(e.target.value)}
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="profile-official-email">Official email</Label>
              <Input
                id="profile-official-email"
                type="email"
                placeholder="you@firm.com"
                value={officialEmail}
                onChange={(e) => setOfficialEmail(e.target.value)}
              />
            </div>
            <div className="sm:col-span-2 space-y-2">
              <Label htmlFor="profile-notification-preference">Send notices to</Label>
              <Select
                value={notificationPreference || undefined}
                onValueChange={(value) => setNotificationPreference(value as NotificationEmailPreference)}
              >
                <SelectTrigger id="profile-notification-preference" className="w-full sm:w-64">
                  <SelectValue placeholder="Chapter default" />
                </SelectTrigger>
                <SelectContent>
                  {notificationPreferenceOptions.map((option) => (
                    <SelectItem key={option.value} value={option.value}>
                      {option.label}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              <p className="text-[0.78rem] text-muted-foreground">
                Leave unset to use the chapter&rsquo;s default. Only emails you&rsquo;ve added above are
                usable here.
              </p>
            </div>
            <div className="sm:col-span-2 flex items-center gap-3">
              <Button type="submit" disabled={savingEmails}>
                {savingEmails ? 'Saving…' : 'Save changes'}
              </Button>
              {savedEmails && <span className="text-[0.85rem] text-muted-foreground">Saved.</span>}
            </div>
          </form>
        )}
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
