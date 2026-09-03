import type { AuthUser, CpdRecord, SubscriptionRecord, TicketRecord } from '@/types'

/**
 * Demonstration member record. There is no backend in this build — the auth
 * context signs any credentials in against this profile so the member area can
 * be reviewed end to end.
 */
export const demoUser: AuthUser = {
  id: 'usr-demo',
  name: 'Amina Okonjo-Bello',
  email: 'member@swanabujachapter.com',
  credential: 'ACA',
  membershipNumber: 'ICAN/052118',
  membershipStatus: 'active',
  role: 'member',
  joinedAt: '2017-11-04',
  cpdTarget: 120,
}

export const cpdRecords: CpdRecord[] = [
  { id: 'cpd-01', activity: 'Chapter technical seminar — tax reform', date: '2026-05-22', hours: 6, type: 'Structured', verified: true },
  { id: 'cpd-02', activity: 'Faculty webinar — forensic accounting in public finance', date: '2026-04-11', hours: 3, type: 'Structured', verified: true },
  { id: 'cpd-03', activity: 'Professional ethics refresher, ICAN MPD', date: '2026-03-07', hours: 2, type: 'Structured', verified: true },
  { id: 'cpd-04', activity: 'Technical reading — IFRS S1 and S2 implementation guidance', date: '2026-02-19', hours: 4, type: 'Unstructured', verified: false },
  { id: 'cpd-05', activity: 'Mentorship sessions delivered to cohort two', date: '2026-01-30', hours: 5, type: 'Unstructured', verified: true },
  { id: 'cpd-06', activity: '55th Annual Accountants’ Conference', date: '2025-10-16', hours: 18, type: 'Structured', verified: true },
  { id: 'cpd-07', activity: 'Chapter workshop — data analytics for the audit file', date: '2025-08-14', hours: 6, type: 'Structured', verified: true },
  { id: 'cpd-08', activity: 'Public financial management masterclass', date: '2025-05-29', hours: 7, type: 'Structured', verified: true },
  { id: 'cpd-09', activity: 'Chapter technical seminar — sustainability assurance', date: '2024-10-03', hours: 7, type: 'Structured', verified: true },
  { id: 'cpd-10', activity: 'Board readiness programme', date: '2024-06-20', hours: 5, type: 'Structured', verified: true },
]

export const subscriptions: SubscriptionRecord[] = [
  { id: 'sub-2026', year: 2026, subscription: 5000, welfare: 12000, status: 'Paid', paidOn: '2026-01-18', reference: 'SWN-2026-004182' },
  { id: 'sub-2025', year: 2025, subscription: 5000, welfare: 12000, status: 'Paid', paidOn: '2025-02-02', reference: 'SWN-2025-003914' },
  { id: 'sub-2024', year: 2024, subscription: 5000, welfare: 10000, status: 'Paid', paidOn: '2024-01-27', reference: 'SWN-2024-003501' },
  { id: 'sub-2027', year: 2027, subscription: 5000, welfare: 12000, status: 'Outstanding', paidOn: null, reference: null },
]

export const tickets: TicketRecord[] = [
  { id: 'tkt-01', eventTitle: 'Annual technical seminar: sustainability reporting and the assurance gap', eventSlug: 'annual-technical-seminar-2026', tier: 'Member — attending in person', amount: 30000, reference: 'TKT-9F42-AB18', status: 'Confirmed', issuedAt: '2026-08-21' },
  { id: 'tkt-02', eventTitle: 'Mentorship clinic for newly inducted members', eventSlug: 'mentorship-clinic-newly-inducted', tier: 'Member — attending online', amount: 15000, reference: 'TKT-7C10-DD03', status: 'Pending', issuedAt: '2026-08-29' },
  { id: 'tkt-03', eventTitle: "Demystifying Nigeria's new tax reform", eventSlug: 'demystifying-nigerias-new-tax-reform', tier: 'Member — attending in person', amount: 30000, reference: 'TKT-2A77-90BE', status: 'Confirmed', issuedAt: '2026-05-02' },
]
