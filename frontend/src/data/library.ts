import type { Committee, Publication, ResourceItem, Training } from '@/types'

export const publications: Publication[] = [
  { id: 'pub-communique-2026', title: 'Communiqué issued at the close of the 2026 chapter technical seminar', category: 'Communiqué', publishedAt: '2026-06-04', fileUrl: '#', sizeLabel: '412 KB' },
  { id: 'pub-annual-report', title: 'Chapter annual report and financial statements, 2025', category: 'Report', publishedAt: '2026-03-30', fileUrl: '#', sizeLabel: '2.1 MB' },
  { id: 'pub-newsletter-q2', title: 'The Abuja Ledger — second quarter newsletter', category: 'Newsletter', publishedAt: '2026-07-15', fileUrl: '#', sizeLabel: '3.4 MB' },
  { id: 'pub-technical-esg', title: 'Technical bulletin: applying IFRS S1 and S2 in a first reporting cycle', category: 'Technical', publishedAt: '2026-05-02', fileUrl: '#', sizeLabel: '890 KB' },
  { id: 'pub-chair-address', title: "Chairperson's address at the investiture of the chapter council", category: 'Address', publishedAt: '2026-01-24', fileUrl: '#', sizeLabel: '256 KB' },
  { id: 'pub-practice-survey', title: 'Women in practice: chapter survey findings and recommendations', category: 'Report', publishedAt: '2026-03-12', fileUrl: '#', sizeLabel: '1.3 MB' },
  { id: 'pub-newsletter-q1', title: 'The Abuja Ledger — first quarter newsletter', category: 'Newsletter', publishedAt: '2026-04-08', fileUrl: '#', sizeLabel: '3.1 MB' },
  { id: 'pub-technical-tax', title: 'Technical bulletin: transitional provisions under the tax reform', category: 'Technical', publishedAt: '2026-06-20', fileUrl: '#', sizeLabel: '740 KB' },
]

export const resources: ResourceItem[] = [
  { id: 'res-membership-form', title: 'Chapter membership registration form', description: 'Complete and submit alongside your dues payment to open a chapter record.', category: 'Form', fileUrl: '#', format: 'PDF' },
  { id: 'res-cpd-log', title: 'CPD activity log template', description: 'Track structured and unstructured hours across the three-year cycle.', category: 'Template', fileUrl: '#', format: 'XLSX' },
  { id: 'res-firm-registration', title: 'Procedure for firm registration', description: 'What the Professional Practice directorate requires when registering a new firm.', category: 'Guide', fileUrl: '#', format: 'PDF' },
  { id: 'res-licence-renewal', title: 'Practice licence renewal — conditions and procedure', description: 'Renewal conditions, evidence required and the annual closing date.', category: 'Guide', fileUrl: '#', format: 'PDF' },
  { id: 'res-attachment', title: 'Practice attachment application form', description: 'For members seeking a hosted attachment toward the practice licence.', category: 'Form', fileUrl: '#', format: 'DOCX' },
  { id: 'res-constitution', title: 'SWAN Abuja Chapter constitution and standing rules', description: 'Governance structure, election procedure and the duties of each office.', category: 'Policy', fileUrl: '#', format: 'PDF' },
  { id: 'res-welfare-policy', title: 'Welfare fund policy', description: 'What the fund covers, how to make a claim, and the approval process.', category: 'Policy', fileUrl: '#', format: 'PDF' },
  { id: 'res-mentorship-pack', title: 'Mentorship programme pack', description: 'Structure, expectations and the session record sheet for both parties.', category: 'Template', fileUrl: '#', format: 'DOCX' },
  { id: 'res-syllabus', title: 'ICAN professional examination syllabus', description: 'Current syllabus for candidates and members supporting students.', category: 'Syllabus', fileUrl: '#', format: 'PDF' },
]

export const committees: Committee[] = [
  {
    id: 'com-technical',
    name: 'Technical and Research',
    slug: 'technical-and-research',
    remit: 'Curates the technical programme, prepares chapter positions on exposure drafts, and publishes the technical bulletin.',
    chair: 'Ngozi Francisca Ashinze, FCA',
    focusAreas: ['Corporate reporting', 'Sustainability and ESG', 'Taxation and fiscal policy', 'Audit and assurance'],
    meetingCadence: 'Monthly',
  },
  {
    id: 'com-membership',
    name: 'Membership and Records',
    slug: 'membership-and-records',
    remit: 'Maintains the chapter roll, onboards newly inducted members, and runs the annual membership drive.',
    chair: 'Nsini Bassey, FCA',
    focusAreas: ['Roll maintenance', 'Induction onboarding', 'Directory accuracy', 'Re-engagement of lapsed members'],
    meetingCadence: 'Monthly',
  },
  {
    id: 'com-cpd',
    name: 'Professional Development',
    slug: 'professional-development',
    remit: 'Plans the CPD calendar, accredits chapter sessions, and liaises with the ICAN Members Professional Development directorate.',
    chair: 'Dr Maryam Danna Mohammed, FCA',
    focusAreas: ['CPD calendar', 'Session accreditation', 'Speaker sourcing', 'Post-event evaluation'],
    meetingCadence: 'Monthly',
  },
  {
    id: 'com-mentorship',
    name: 'Mentorship and Career',
    slug: 'mentorship-and-career',
    remit: 'Runs the mentorship cohorts, the newly inducted clinic, and the chapter job board.',
    chair: 'Aisha Bello Oroche, FCA',
    focusAreas: ['Mentor matching', 'Career clinics', 'Job board curation', 'Return-to-work support'],
    meetingCadence: 'Quarterly',
  },
  {
    id: 'com-practice',
    name: 'Women in Practice',
    slug: 'women-in-practice',
    remit: 'Supports members founding and running firms, hosts the licensing clinic, and maintains the practice attachment register.',
    chair: 'Charity Okongwu, FCA',
    focusAreas: ['Licensing clinic', 'Practice attachment register', 'Firm governance', 'Small practice economics'],
    meetingCadence: 'Quarterly',
  },
  {
    id: 'com-welfare',
    name: 'Welfare',
    slug: 'welfare',
    remit: 'Administers the welfare fund and coordinates the chapter response to bereavement, illness and major life events.',
    chair: 'Taiye Fasan, ACA',
    focusAreas: ['Claims administration', 'Member visits', 'Fund reporting'],
    meetingCadence: 'As required',
  },
  {
    id: 'com-outreach',
    name: 'Community Outreach',
    slug: 'community-outreach',
    remit: 'Plans and delivers community service, financial literacy sessions and the chapter education placements.',
    chair: 'Oluwakemi Toluwani, FCA',
    focusAreas: ['Financial literacy', 'Community visits', 'Education placements', 'Partner coordination'],
    meetingCadence: 'Quarterly',
  },
  {
    id: 'com-publicity',
    name: 'Publicity and Communications',
    slug: 'publicity-and-communications',
    remit: 'Runs chapter communications, the website and social channels, and media relations for chapter events.',
    chair: 'Oluwakemi Toluwani, FCA',
    focusAreas: ['Website and portal', 'Social channels', 'Media relations', 'Newsletter'],
    meetingCadence: 'Monthly',
  },
]

export const trainings: Training[] = [
  { id: 'trn-esg', title: 'Sustainability reporting: preparing a first IFRS S1 disclosure', provider: 'SWAN Abuja', deliveryMode: 'Hybrid', date: '2026-09-18', cpdHours: 4, fee: 35000, memberFee: 20000, seatsLeft: 22 },
  { id: 'trn-tax', title: 'Tax reform workshop for practitioners', provider: 'SWAN Abuja', deliveryMode: 'Physical', date: '2026-09-30', cpdHours: 5, fee: 45000, memberFee: 25000, seatsLeft: 8 },
  { id: 'trn-forensic', title: 'Forensic accounting and fraud risk in public finance', provider: 'Faculty', deliveryMode: 'Virtual', date: '2026-10-14', cpdHours: 3, fee: 25000, memberFee: 15000, seatsLeft: 46 },
  { id: 'trn-ethics', title: 'Professional ethics and the revised code', provider: 'ICAN MPD', deliveryMode: 'Virtual', date: '2026-10-22', cpdHours: 2, fee: 15000, memberFee: 10000, seatsLeft: 60 },
  { id: 'trn-datax', title: 'Data analytics for the audit file', provider: 'Faculty', deliveryMode: 'Hybrid', date: '2026-11-06', cpdHours: 6, fee: 55000, memberFee: 32000, seatsLeft: 18 },
  { id: 'trn-leadership', title: 'Board readiness for senior finance professionals', provider: 'SWAN Abuja', deliveryMode: 'Physical', date: '2026-11-20', cpdHours: 5, fee: 60000, memberFee: 35000, seatsLeft: 12 },
]

export function findCommittee(slug: string): Committee | undefined {
  return committees.find((c) => c.slug === slug)
}
