import { passwordRequirementChecks } from '@/lib/password'
import type { PasswordPolicy } from '@/types'

export function PasswordRequirements({ password, policy }: { password: string; policy: PasswordPolicy }) {
  const requirements = passwordRequirementChecks(password, policy)
  const metCount = requirements.filter((requirement) => requirement.met).length
  const percentage = Math.round((metCount / requirements.length) * 100)

  return (
    <div className="space-y-2" aria-live="polite">
      <div className="flex items-center justify-between text-[0.78rem]">
        <span className="text-muted-foreground">Password strength</span>
        <span className={metCount === requirements.length ? 'text-green-700 dark:text-green-400' : 'text-muted-foreground'}>
          {metCount}/{requirements.length} requirements met
        </span>
      </div>
      <div
        className="h-1.5 overflow-hidden rounded-full bg-secondary"
        role="progressbar"
        aria-label="Password strength"
        aria-valuemin={0}
        aria-valuemax={requirements.length}
        aria-valuenow={metCount}
      >
        <div
          className={`h-full transition-[width,background-color] ${metCount === requirements.length ? 'bg-green-600' : 'bg-destructive'}`}
          style={{ width: `${percentage}%` }}
        />
      </div>
      <ul className="space-y-1 text-[0.78rem]">
        {requirements.map((requirement) => (
          <li
            key={`${requirement.label}-${requirement.met}`}
            className={`animate-in fade-in slide-in-from-left-1 duration-200 ease-out motion-reduce:animate-none motion-reduce:transition-none transition-all hover:translate-x-1 ${requirement.met ? 'text-green-700 dark:text-green-400' : 'text-destructive'}`}
          >
            <span aria-hidden="true">{requirement.met ? '✓' : '○'}</span>{' '}
            {requirement.label}
          </li>
        ))}
      </ul>
    </div>
  )
}
