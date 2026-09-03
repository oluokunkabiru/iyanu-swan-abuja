import logo from '@/assets/logo.png'
import { cn } from '@/lib/utils'

export function BrandLogo({ className }: { className?: string }) {
  return (
    <img
      src={logo}
      alt="Society of Women Accountants of Nigeria"
      className={cn('block h-auto w-auto', className)}
    />
  )
}
