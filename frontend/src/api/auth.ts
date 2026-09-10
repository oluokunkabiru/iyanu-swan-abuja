import { api, ensureCsrfCookie } from '@/api/client'
import type { AuthUser, CpdRecord, SubscriptionRecord, TicketRecord } from '@/types'

export async function register(payload: {
  name: string
  email: string
  password: string
  phone?: string
}): Promise<AuthUser> {
  await ensureCsrfCookie()
  const { data } = await api.post<AuthUser>('/register', payload)
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

  const { data } = await api.post<AuthUser>('/me', form)
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

export async function paySubscriptionDues(year: number): Promise<{ authorizationUrl: string }> {
  await ensureCsrfCookie()
  const { data } = await api.post<{ authorizationUrl: string }>(`/me/subscriptions/${year}/pay`)
  return data
}
