import { Link } from 'react-router-dom'
import { Mail, MapPin, Phone } from 'lucide-react'
import { FacebookIcon, InstagramIcon, XIcon } from '@/components/icons/SocialIcons'
import { useSettings } from '@/context/SettingsContext'

export function Footer() {
  const settings = useSettings()

  return (
    <footer className="border-t bg-muted/30">
      <div className="mx-auto grid max-w-6xl gap-10 px-4 py-16 sm:grid-cols-2 lg:grid-cols-4">
        <div>
          <h3 className="font-heading font-semibold">{settings?.chapter_name ?? 'SWAN Abuja Chapter'}</h3>
          <p className="mt-2 text-sm text-muted-foreground">
            {settings?.tagline ?? 'Empowering the professional female accountant'}
          </p>
          <div className="mt-5 flex gap-2">
            {settings?.facebook_url && (
              <a
                href={settings.facebook_url}
                target="_blank"
                rel="noreferrer"
                aria-label="Facebook"
                className="flex h-9 w-9 items-center justify-center rounded-full border bg-card text-muted-foreground transition-colors hover:border-primary/30 hover:bg-primary/10 hover:text-primary"
              >
                <FacebookIcon className="h-4 w-4" />
              </a>
            )}
            {settings?.instagram_url && (
              <a
                href={settings.instagram_url}
                target="_blank"
                rel="noreferrer"
                aria-label="Instagram"
                className="flex h-9 w-9 items-center justify-center rounded-full border bg-card text-muted-foreground transition-colors hover:border-primary/30 hover:bg-primary/10 hover:text-primary"
              >
                <InstagramIcon className="h-4 w-4" />
              </a>
            )}
            {settings?.twitter_url && (
              <a
                href={settings.twitter_url}
                target="_blank"
                rel="noreferrer"
                aria-label="X / Twitter"
                className="flex h-9 w-9 items-center justify-center rounded-full border bg-card text-muted-foreground transition-colors hover:border-primary/30 hover:bg-primary/10 hover:text-primary"
              >
                <XIcon className="h-4 w-4" />
              </a>
            )}
          </div>
        </div>

        <div>
          <h4 className="font-heading text-sm font-semibold">Quick links</h4>
          <ul className="mt-4 space-y-2.5 text-sm text-muted-foreground">
            <li><Link to="/about" className="transition-colors hover:text-primary">About us</Link></li>
            <li><Link to="/events" className="transition-colors hover:text-primary">Events</Link></li>
            <li><Link to="/news" className="transition-colors hover:text-primary">News</Link></li>
            <li><Link to="/gallery" className="transition-colors hover:text-primary">Gallery</Link></li>
          </ul>
        </div>

        <div>
          <h4 className="font-heading text-sm font-semibold">Membership</h4>
          <ul className="mt-4 space-y-2.5 text-sm text-muted-foreground">
            <li><Link to="/register" className="transition-colors hover:text-primary">Become a member</Link></li>
            <li><Link to="/login" className="transition-colors hover:text-primary">Member login</Link></li>
            <li><Link to="/dashboard" className="transition-colors hover:text-primary">Dashboard</Link></li>
          </ul>
        </div>

        <div>
          <h4 className="font-heading text-sm font-semibold">Contact</h4>
          <ul className="mt-4 space-y-2.5 text-sm text-muted-foreground">
            {settings?.address && (
              <li className="flex items-start gap-2.5">
                <MapPin className="mt-0.5 h-4 w-4 shrink-0 text-primary" /> {settings.address}
              </li>
            )}
            {settings?.phone && (
              <li className="flex items-center gap-2.5">
                <Phone className="h-4 w-4 shrink-0 text-primary" /> {settings.phone}
              </li>
            )}
            {settings?.email && (
              <li className="flex items-center gap-2.5">
                <Mail className="h-4 w-4 shrink-0 text-primary" /> {settings.email}
              </li>
            )}
          </ul>
        </div>
      </div>

      <div className="border-t py-5 text-center text-xs text-muted-foreground">
        © {new Date().getFullYear()} {settings?.chapter_name ?? 'SWAN Abuja Chapter'}. All rights reserved.
      </div>
    </footer>
  )
}
