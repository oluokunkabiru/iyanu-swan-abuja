import { Link } from 'react-router-dom'
import { Mail, MapPin, Phone } from 'lucide-react'
import { BrandLogo } from '@/components/common/BrandLogo'
import { footerLinks, site } from '@/data'

export function Footer() {
  const year = new Date().getFullYear()

  return (
    <footer className="mt-auto bg-plum-900 text-plum-200">
      <div className="mx-auto max-w-6xl px-4 py-14">
        <div className="grid gap-10 lg:grid-cols-[minmax(0,20rem)_minmax(0,1fr)]">
          <div>
            <Link
              to="/"
              aria-label={`${site.shortName} home`}
              className="inline-flex bg-white p-2"
            >
              <BrandLogo className="h-10" />
            </Link>
            <p className="mt-4 max-w-xs text-[0.86rem] leading-relaxed">{site.tagline}</p>

            <ul className="mt-6 space-y-2.5 text-[0.86rem]">
              <li className="flex items-start gap-2.5">
                <MapPin aria-hidden="true" className="mt-0.5 h-4 w-4 shrink-0 text-gold-500" />
                <span>{site.address}</span>
              </li>
              <li className="flex items-start gap-2.5">
                <Mail aria-hidden="true" className="mt-0.5 h-4 w-4 shrink-0 text-gold-500" />
                <a href={`mailto:${site.email}`} className="hover:text-white">
                  {site.email}
                </a>
              </li>
              <li className="flex items-start gap-2.5">
                <Phone aria-hidden="true" className="mt-0.5 h-4 w-4 shrink-0 text-gold-500" />
                <a href={`tel:${site.phone.replace(/\s/g, '')}`} className="hover:text-white">
                  {site.phone}
                </a>
              </li>
            </ul>

            <ul className="mt-6 flex flex-wrap gap-x-4 gap-y-2 text-[0.82rem]">
              {site.socials.map((s) => (
                <li key={s.label}>
                  <a
                    href={s.url}
                    target="_blank"
                    rel="noreferrer"
                    className="border-b border-plum-800 pb-0.5 hover:border-gold-500 hover:text-white"
                  >
                    {s.label}
                  </a>
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
            {site.chapterName} — {year}. All rights reserved.
          </p>
          <p>
            A society of the{' '}
            <a
              href="https://icanig.org/ican/"
              target="_blank"
              rel="noreferrer"
              className="text-gold-300 underline-offset-4 hover:underline"
            >
              Institute of Chartered Accountants of Nigeria
            </a>
          </p>
        </div>
      </div>
    </footer>
  )
}
