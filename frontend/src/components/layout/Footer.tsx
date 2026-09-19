import { Link } from 'react-router-dom'
import { Mail, MapPin, Phone } from 'lucide-react'
import { BrandLogo } from '@/components/common/BrandLogo'
import { FacebookIcon, InstagramIcon, LinkedInIcon, XIcon, YouTubeIcon } from '@/components/icons/SocialIcons'
import { Skeleton } from '@/components/ui/skeleton'
import { footerLinks } from '@/data'
import { useSettings } from '@/context/SettingsContext'

const socialIcons = {
  facebook: FacebookIcon,
  twitter: XIcon,
  instagram: InstagramIcon,
  linkedin: LinkedInIcon,
  youtube: YouTubeIcon,
}

export function Footer() {
  const { settings, isLoading: loadingSettings } = useSettings()
  const year = new Date().getFullYear()

  return (
    <footer className="mt-auto bg-plum-900 text-plum-200">
      <div className="mx-auto max-w-6xl px-4 py-14">
        <div className="grid gap-10 lg:grid-cols-[minmax(0,20rem)_minmax(0,1fr)]">
          <div>
            <Link
              to="/"
              aria-label={`${settings?.shortName ?? 'SWAN Abuja'} home`}
              className="inline-flex bg-white p-2"
            >
              <BrandLogo className="h-10" />
            </Link>
            <p className="mt-4 max-w-xs text-[0.86rem] leading-relaxed">
              {loadingSettings ? (
                <Skeleton className="h-4 w-48 bg-plum-800" />
              ) : (
                settings?.tagline
              )}
            </p>

            <ul className="mt-6 space-y-2.5 text-[0.86rem]">
              <li className="flex items-start gap-2.5">
                <MapPin aria-hidden="true" className="mt-0.5 h-4 w-4 shrink-0 text-gold-500" />
                <span>
                  {loadingSettings ? (
                    <Skeleton className="inline-block h-3 w-40 align-middle bg-plum-800" />
                  ) : (
                    settings?.address
                  )}
                </span>
              </li>
              <li className="flex items-start gap-2.5">
                <Mail aria-hidden="true" className="mt-0.5 h-4 w-4 shrink-0 text-gold-500" />
                <a href={`mailto:${settings?.email}`} className="hover:text-white">
                  {loadingSettings ? (
                    <Skeleton className="inline-block h-3 w-32 align-middle bg-plum-800" />
                  ) : (
                    settings?.email
                  )}
                </a>
              </li>
              <li className="flex items-start gap-2.5">
                <Phone aria-hidden="true" className="mt-0.5 h-4 w-4 shrink-0 text-gold-500" />
                <a href={`tel:${settings?.phone?.replace(/\s/g, '') ?? ''}`} className="hover:text-white">
                  {loadingSettings ? (
                    <Skeleton className="inline-block h-3 w-24 align-middle bg-plum-800" />
                  ) : (
                    settings?.phone
                  )}
                </a>
              </li>
            </ul>

            <ul className="mt-6 flex flex-wrap gap-2">
              {(settings?.socials ?? []).map((s) => (
                <li key={s.label}>
                  <SocialLinkIcon network={s.network} label={s.label} url={s.url} />
                </li>
              ))}
            </ul>
          </div>

          <div className="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            {footerLinks.map((col) => (
              <div key={col.heading}>
                <p className="border-b border-gold-500/40 pb-2 text-[0.8rem] font-semibold text-gold-300">
                  {col.heading}
                </p>
                <ul className="mt-3 space-y-2 text-[0.86rem]">
                  {col.items.map((item) => (
                    <li key={item.to}>
                      <Link to={item.to} className="hover:text-white">
                        {item.label}
                      </Link>
                    </li>
                  ))}
                </ul>
              </div>
            ))}
          </div>
        </div>

        <div className="mt-12 flex flex-col gap-3 border-t border-plum-800 pt-6 text-[0.8rem] sm:flex-row sm:items-center sm:justify-between">
          <p>
            {loadingSettings ? (
              <Skeleton className="inline-block h-3 w-28 align-middle bg-plum-800" />
            ) : (
              settings?.chapterName
            )}{' '}
            — {year}. All rights reserved.
          </p>
          <a href="#site-header" className="text-gold-300 underline-offset-4 hover:underline">
            The Society of Women Accountants of Nigeria
          </a>
        </div>
      </div>
    </footer>
  )
}

function SocialLinkIcon({
  network,
  label,
  url,
}: {
  network: keyof typeof socialIcons
  label: string
  url: string
}) {
  const Icon = socialIcons[network]

  return (
    <a
      href={url}
      target="_blank"
      rel="noreferrer"
      aria-label={label}
      title={label}
      className="flex h-9 w-9 items-center justify-center rounded-sm border border-plum-800 text-plum-200 transition-colors hover:border-gold-500 hover:text-gold-300"
    >
      <Icon aria-hidden="true" className="h-4 w-4" />
    </a>
  )
}
