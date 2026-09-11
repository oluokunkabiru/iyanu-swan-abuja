import { api, ensureCsrfCookie } from '@/api/client'
import type { AuthUser, CpdRecord, NotificationEmailPreference, SubscriptionRecord, TicketRecord } from '@/types'

export async function register(payload: {
  name: string
  email: string
  password: string
  phone?: string
  dateOfBirth?: string
}): Promise<AuthUser> {
  await ensureCsrfCookie()
  const { data } = await api.post<AuthUser>('/register', {
    name: payload.name,
    email: payload.email,
    password: payload.password,
    phone: payload.phone,
    date_of_birth: payload.dateOfBirth,
  })
  return data
}

export async function login(payload: { email: string; password: string }): Promise<AuthUser> {
  await ensureCsrfCookie()
  const { data } = await api.post<AuthUser>('/login', payload)
  return data
}

export async function logout(): Promise<void> {
  await api.post('/logout')
}

export async function fetchMe(): Promise<AuthUser> {
  const { data } = await api.get<AuthUser>('/me')
  return data
}

export async function updateMe(payload: {
  name?: string
  phone?: string
  dateOfBirth?: string
  isDirectoryListed?: boolean
  photo?: File
  personalEmail?: string
  officialEmail?: string
  notificationEmailPreference?: NotificationEmailPreference | ''
}): Promise<AuthUser> {
  const form = new FormData()
  form.append('_method', 'PUT')
  if (payload.name !== undefined) form.append('name', payload.name)
  if (payload.phone !== undefined) form.append('phone', payload.phone)
  if (payload.dateOfBirth !== undefined) form.append('date_of_birth', payload.dateOfBirth)
  if (payload.isDirectoryListed !== undefined) {
    form.append('is_directory_listed', payload.isDirectoryListed ? '1' : '0')
  }
  if (payload.photo) form.append('photo', payload.photo)
  if (payload.personalEmail !== undefined) form.append('personal_email', payload.personalEmail)
  if (payload.officialEmail !== undefined) form.append('official_email', payload.officialEmail)
  if (payload.notificationEmailPreference !== undefined) {
    form.append('notification_email_preference', payload.notificationEmailPreference)
  }

  const { data } = await api.post<AuthUser>('/me', form)
  return data
}

export async function resendVerificationEmail(): Promise<{ message: string }> {
  const { data } = await api.post<{ message: string }>('/email/verification-notification')
  return data
}

export async function fetchMyRegistrations(): Promise<TicketRecord[]> {
  const { data } = await api.get<TicketRecord[]>('/me/registrations')
  return data
}

export async function fetchMyCpdRecords(): Promise<CpdRecord[]> {
  const { data } = await api.get<CpdRecord[]>('/me/cpd-records')
  return data
}

export async function fetchMySubscriptions(): Promise<SubscriptionRecord[]> {
  const { data } = await api.get<SubscriptionRecord[]>('/me/subscriptions')
  return data
}

export async function paySubscriptionDues(
  year: number,
  membershipLevelId: string,
): Promise<{ authorizationUrl: string }> {
  await ensureCsrfCookie()
  const { data } = await api.post<{ authorizationUrl: string }>(`/me/subscriptions/${year}/pay`, {
    membership_level_id: membershipLevelId,
  })
  return data
}

export async function submitBankTransfer(
  year: number,
  payload: { membershipLevelId: string; reference: string; evidence: File },
): Promise<{ message: string }> {
  await ensureCsrfCookie()
  const form = new FormData()
  form.append('membership_level_id', payload.membershipLevelId)
  form.append('reference', payload.reference)
  form.append('evidence', payload.evidence)

  const { data } = await api.post<{ message: string }>(`/me/subscriptions/${year}/bank-transfer`, form)
  return data
}
