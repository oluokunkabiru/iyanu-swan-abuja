import type { PasswordPolicy } from '@/types'

export const defaultPasswordPolicy: PasswordPolicy = {
  minLength: 12,
  requireMixedCase: true,
  requireNumbers: true,
  requireSymbols: true,
}

export function passwordRequirementText(policy: PasswordPolicy): string {
  const requirements = [`at least ${policy.minLength} characters`]

  if (policy.requireMixedCase) requirements.push('an uppercase and lowercase letter')
  if (policy.requireNumbers) requirements.push('a number')
  if (policy.requireSymbols) requirements.push('a symbol such as ! or @')

  return `Use ${requirements.join(', ')}.`
}

export function passwordMeetsPolicy(password: string, policy: PasswordPolicy): boolean {
  return (
    password.length >= policy.minLength &&
    (!policy.requireMixedCase || (/[a-z]/.test(password) && /[A-Z]/.test(password))) &&
    (!policy.requireNumbers || /\d/.test(password)) &&
    (!policy.requireSymbols || /[^A-Za-z0-9]/.test(password))
  )
}

export function passwordRequirementChecks(password: string, policy: PasswordPolicy) {
  return [
    { label: `At least ${policy.minLength} characters`, met: password.length >= policy.minLength },
    {
      label: 'An uppercase and lowercase letter',
      met: !policy.requireMixedCase || (/[a-z]/.test(password) && /[A-Z]/.test(password)),
      enabled: policy.requireMixedCase,
    },
    { label: 'A number', met: !policy.requireNumbers || /\d/.test(password), enabled: policy.requireNumbers },
    {
      label: 'A symbol such as ! or @',
      met: !policy.requireSymbols || /[^A-Za-z0-9]/.test(password),
      enabled: policy.requireSymbols,
    },
  ].filter((requirement) => requirement.enabled !== false)
}
