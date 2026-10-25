import { Fragment, useEffect, useState, type FormEvent } from 'react'
import { Link, Navigate, useParams } from 'react-router-dom'
import { isAxiosError } from 'axios'
import { Camera, CheckCircle2, LockKeyhole, Pencil, Save, ShieldCheck } from 'lucide-react'
import type { updateMe } from '@/api/auth'
import { MemberAvatar, MemberSummary } from '@/components/sections/MemberIdentity'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { useAuth } from '@/context/AuthContext'
import { profileSections, profileSectionPath, type ProfileField } from '@/data/memberProfile'
import { formatDate } from '@/lib/format'
import type { AuthUser, MemberBioData } from '@/types'

type SectionDefinition = (typeof profileSections)[number]
const emailLabels: Record<string, string> = { registered: 'Registered email only', personal: 'Personal email only', official: 'Official email only', all: 'All emails on file' }

function errorMessage(error: unknown): string {
  if (isAxiosError(error)) {
    const messages = error.response?.data?.errors
    const first = messages && Object.values(messages).flat()[0]
    if (typeof first === 'string') return first
    if (typeof error.response?.data?.message === 'string') return error.response.data.message
  }
  return 'Your changes could not be saved. Please try again.'
}

function draftFrom(user: AuthUser, section: SectionDefinition): Record<string, string> {
  return Object.fromEntries(section.fields.map(field => {
    const value = field.source
      ? user[field.key as keyof AuthUser]
      : section.id !== 'membership' ? user.profileDetails?.[section.id]?.[field.key] : null
    return [field.key, value === null || value === undefined ? '' : String(value)]
  }))
}

function ProfilePhoto() {
  const { user, updateProfile } = useAuth()
  const [photo, setPhoto] = useState<File | null>(null)
  const [preview, setPreview] = useState<string | null>(null)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState('')
  const [saved, setSaved] = useState(false)
  useEffect(() => {
    return () => { if (preview) URL.revokeObjectURL(preview) }
  }, [preview])
  if (!user) return null
  async function savePhoto(event: FormEvent) {
    event.preventDefault()
    if (!photo || !user) return
    if (!user.dateOfBirth) { setError('Add your date of birth in BioData before saving your photo.'); return }
    setError(''); setSaving(true)
    try { await updateProfile({photo,dateOfBirth:user.dateOfBirth}); setPhoto(null); setPreview(null); setSaved(true) }
    catch (error) { setError(errorMessage(error)) }
    finally { setSaving(false) }
  }
  return (
    <aside className="space-y-5 xl:sticky xl:top-24 xl:self-start" aria-label="Profile photo">
      <form onSubmit={savePhoto} className="rounded-xl border border-border bg-card p-5 text-center shadow-sm">
        <p className="mb-4 text-[0.68rem] font-semibold tracking-[0.15em] text-muted-foreground uppercase">Profile photograph</p>
        <MemberAvatar user={user} preview={preview} className="mx-auto h-40 w-36" />
        <p className="mt-4 text-sm font-semibold">{[user.title,user.name].filter(Boolean).join(' ')}</p>
        <p className="mt-1 text-xs text-muted-foreground">{user.credential}</p>
        <label className="mt-5 inline-flex cursor-pointer items-center justify-center gap-2 rounded-md border border-border px-3 py-2 text-xs font-semibold hover:bg-secondary focus-within:ring-2 focus-within:ring-ring">
          <Camera className="h-3.5 w-3.5" aria-hidden="true" />Change photo
          <input aria-label="Choose profile photo" type="file" accept="image/jpeg,image/png,image/webp" className="sr-only" onChange={event=>{
            const file=event.target.files?.[0]
            setError(''); setSaved(false)
            if (!file) return
            if (!['image/jpeg','image/png','image/webp'].includes(file.type) || file.size>5*1024*1024) {
              setError('Choose a JPG, PNG or WebP image up to 5 MB.'); setPhoto(null); setPreview(null); event.target.value=''; return
            }
            setPhoto(file)
            setPreview(URL.createObjectURL(file))
            event.target.value=''
          }} />
        </label>
        <p className="mt-2 text-[0.67rem] text-muted-foreground">JPG, PNG or WebP · maximum 5 MB</p>
        {photo && <div className="mt-3 flex justify-center gap-2"><Button size="sm" type="submit" disabled={saving}>{saving?'Saving…':'Save photo'}</Button><Button size="sm" variant="ghost" type="button" onClick={()=>{setPhoto(null);setPreview(null)}} disabled={saving}>Cancel</Button></div>}
        {error && <p role="alert" className="mt-3 text-xs text-destructive">{error}</p>}
        {saved && <p role="status" className="mt-3 text-xs text-success">Profile photo updated.</p>}
      </form>
      <div className="rounded-xl border border-border bg-card p-5">
        <ShieldCheck className="h-5 w-5 text-primary" aria-hidden="true" />
        <h3 className="mt-3 text-sm">Your information is private</h3>
        <p className="mt-2 text-xs leading-relaxed text-muted-foreground">Your contact details, BioData and next-of-kin information are held on your chapter record. Only the details you choose to list appear in the member directory.</p>
      </div>
    </aside>
  )
}

function SectionEditor({ section }: { section: SectionDefinition }) {
  const {user,updateProfile}=useAuth()
  const [draft,setDraft]=useState(()=>user?draftFrom(user,section):{})
  const [editing,setEditing]=useState(false)
  const [saving,setSaving]=useState(false)
  const [saved,setSaved]=useState(false)
  const [error,setError]=useState('')
  if(!user) return null
  function change(key:string,value:string) { setDraft(current=>({...current,[key]:value})); setSaved(false) }
  function isLocked(field:ProfileField) {
    return Boolean(field.readOnly || (!user?.emailVerified && ['personalEmail','officialEmail'].includes(field.key)))
  }
  async function save(event:FormEvent) {
    event.preventDefault()
    if(!user) return
    const payload:Parameters<typeof updateMe>[0]={dateOfBirth:user.dateOfBirth??''}
    const detailFields:Record<string,string>={}
    for(const field of section.fields) {
      if(isLocked(field)) continue
      const value=draft[field.key]??''
      if(field.source==='bio') {
        payload.bioData??={}
        payload.bioData[field.key as keyof MemberBioData]=value
      } else if(field.source==='root') {
        if(['personalEmail','officialEmail'].includes(field.key) && value===(user[field.key as 'personalEmail'|'officialEmail']??'')) continue
        Object.assign(payload,{[field.key]:field.type==='checkbox'?value==='true':value})
      } else detailFields[field.key]=value
    }
    if(section.id!=='membership' && Object.keys(detailFields).length) payload.profileDetails={[section.id]:detailFields}
    if(!payload.dateOfBirth) { setError('Please add your date of birth in BioData before saving your profile.'); return }
    setError(''); setSaving(true); setSaved(false)
    try {
      const next=await updateProfile(payload)
      setDraft(draftFrom(next,section)); setEditing(false); setSaved(true)
    } catch(error) { setError(errorMessage(error)) }
    finally { setSaving(false) }
  }
  function display(field:ProfileField) {
    const value=draft[field.key]
    if(field.type==='checkbox') return value==='true'?'Listed in the member directory':'Not listed in the member directory'
    if(!value) return 'Not provided'
    if(field.key==='notificationEmailPreference') return emailLabels[value]??value
    if(field.type==='date') return formatDate(value)
    return value
  }
  function fieldControl(field:ProfileField) {
    const id=`profile-${section.id}-${field.key}`
    const value=draft[field.key]??''
    if(isLocked(field)) return <p className="mt-2 flex min-h-9 items-center gap-2 break-words text-sm"><LockKeyhole className="h-3 w-3 shrink-0 text-muted-foreground" aria-hidden="true" />{display(field)}</p>
    if(field.type==='select') return <select id={id} value={value} onChange={event=>change(field.key,event.target.value)} className="mt-2 h-10 w-full rounded-md border border-input bg-background px-3 text-sm"><option value="">Select an option</option>{field.options?.map(option=><option key={option} value={option}>{field.key==='notificationEmailPreference'?emailLabels[option]:option}</option>)}</select>
    if(field.type==='textarea') return <Textarea id={id} rows={3} maxLength={field.maxLength??1000} value={value} onChange={event=>change(field.key,event.target.value)} className="mt-2" />
    if(field.type==='checkbox') return <label className="mt-3 flex items-center gap-3 text-sm"><input id={id} type="checkbox" checked={value==='true'} onChange={event=>change(field.key,String(event.target.checked))} className="h-4 w-4 accent-[var(--primary)]" />List my public professional profile</label>
    return <Input id={id} type={field.type??'text'} value={value} onChange={event=>change(field.key,event.target.value)} maxLength={field.maxLength??255} min={field.min} max={field.type==='date'&&field.key==='dateOfBirth'?new Date().toISOString().slice(0,10):field.max} required={field.key==='dateOfBirth'} className="mt-2" />
  }
  return (
    <form onSubmit={save} className="min-w-0 overflow-hidden rounded-xl border border-border bg-card shadow-sm">
      <div className="flex flex-wrap items-center justify-between gap-4 border-b border-border px-5 py-5 sm:px-7">
        <div className="flex items-center gap-3"><span className="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary text-primary"><section.icon className="h-5 w-5" aria-hidden="true" /></span><div><h2 className="text-lg">{section.label}</h2><p className="mt-1 text-xs text-muted-foreground">{section.description}</p></div></div>
        {!editing && <Button type="button" size="sm" variant="outline" onClick={()=>{setEditing(true);setSaved(false);setError('')}}><Pencil className="h-3.5 w-3.5" aria-hidden="true" />Edit details</Button>}
      </div>
      <div className="px-5 py-6 sm:px-7">
        {section.id==='bio'&&<p className="mb-6 rounded-lg bg-secondary/60 px-4 py-3 text-xs leading-relaxed text-muted-foreground">Your registered name and membership number are managed by the chapter. Contact the secretariat if a correction is needed.</p>}
        {section.id==='contact'&&!user.emailVerified&&<p className="mb-6 rounded-lg bg-accent/30 p-4 text-xs">Verify your registered email before adding personal or official email addresses. <Link to="/members/overview" className="font-semibold underline">Check email verification</Link></p>}
        <dl className="grid min-w-0 gap-x-8 gap-y-6 sm:grid-cols-2">
          {section.fields.map((field,index)=>(
            <Fragment key={field.key}>
              {field.group&&field.group!==section.fields[index-1]?.group&&<div className="mt-2 border-t border-border pt-5 sm:col-span-2"><h3 className="text-sm">{field.group}</h3></div>}
              <div className={field.type==='textarea'||field.type==='checkbox'?'min-w-0 sm:col-span-2':'min-w-0'}>
                <dt>{editing&&!isLocked(field)?<Label htmlFor={`profile-${section.id}-${field.key}`} className="text-xs text-muted-foreground">{field.label}</Label>:<span className="text-xs text-muted-foreground">{field.label}</span>}</dt>
                <dd>{editing?fieldControl(field):<p className={`mt-2 min-h-5 whitespace-pre-wrap break-words text-[0.9rem] font-medium ${!draft[field.key]?'text-muted-foreground/60':''}`}>{display(field)}</p>}</dd>
              </div>
            </Fragment>
          ))}
        </dl>
        {section.id==='membership'&&<Link to="/members/subscription" className="mt-7 inline-flex text-sm font-semibold text-primary underline-offset-4 hover:underline">View subscription and welfare payments →</Link>}
        {error&&<p role="alert" className="mt-6 rounded-lg border border-destructive/20 bg-destructive/5 p-3 text-sm text-destructive">{error} {!user.dateOfBirth&&section.id!=='bio'&&<Link to="/members/profile" className="font-semibold underline">Open BioData</Link>}</p>}
        {saved&&<p role="status" className="mt-6 flex items-center gap-2 text-sm text-success"><CheckCircle2 className="h-4 w-4" aria-hidden="true" />Your {section.label.toLowerCase()} have been saved.</p>}
      </div>
      {editing&&<div className="flex flex-wrap items-center justify-end gap-3 border-t border-border bg-secondary/25 px-5 py-4 sm:px-7"><Button type="button" variant="ghost" disabled={saving} onClick={()=>{setEditing(false);setDraft(draftFrom(user,section));setError('')}}>Cancel</Button><Button type="submit" disabled={saving}><Save className="h-4 w-4" aria-hidden="true" />{saving?'Saving…':'Save changes'}</Button></div>}
    </form>
  )
}

export default function MembersProfile() {
  const {section:sectionId='bio'}=useParams()
  const {user}=useAuth()
  const section=profileSections.find(section=>section.id===sectionId)
  if(!user) return null
  if(!section) return <Navigate to={profileSectionPath('bio')} replace />
  return (
    <div className="space-y-6">
      <MemberSummary user={user} showPhoto={false} showProfileLink={false} />
      <div className="grid items-start gap-6 xl:grid-cols-[minmax(0,1fr)_15rem]">
        <SectionEditor key={section.id} section={section} />
        <ProfilePhoto />
      </div>
    </div>
  )
}
