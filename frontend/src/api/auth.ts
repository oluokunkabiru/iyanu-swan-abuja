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

export async function updateMe(payload: { name?: string; phone?: string }): Promise<AuthUser> {
  const { data } = await api.put<AuthUser>('/me', payload)
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
