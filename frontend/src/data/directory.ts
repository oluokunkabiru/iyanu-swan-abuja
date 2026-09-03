import type { DirectoryMember, Firm, JobListing } from '@/types'

export const directoryMembers: DirectoryMember[] = [
  { id: 'dm-01', name: 'Patricia Chinwe Ofili', credential: 'ACA', membershipNumber: 'ICAN/041826', sector: 'Consulting', specialisation: 'Corporate reporting advisory', yearAdmitted: 2011, chapterRole: 'Chairperson' },
  { id: 'dm-02', name: 'Dr Maryam Danna Mohammed', credential: 'FCA', membershipNumber: 'ICAN/022914', sector: 'Academia', specialisation: 'Public financial management', yearAdmitted: 2003, chapterRole: 'Vice Chairperson' },
  { id: 'dm-03', name: 'Ojoma Blessing Lawal-Adewale', credential: 'ACA', membershipNumber: 'ICAN/048301', sector: 'Financial services', specialisation: 'Regulatory reporting', yearAdmitted: 2014, chapterRole: 'General Secretary' },
  { id: 'dm-04', name: 'Biola Olawoore', credential: 'FCA', membershipNumber: 'ICAN/019477', sector: 'Public practice', specialisation: 'Audit and assurance', yearAdmitted: 2001, chapterRole: 'Treasurer' },
  { id: 'dm-05', name: 'Ojoma Blessing Olaniran', credential: 'FCA', membershipNumber: 'ICAN/026118', sector: 'Public sector', specialisation: 'Budget and treasury operations', yearAdmitted: 2005, chapterRole: 'Financial Secretary' },
  { id: 'dm-06', name: 'Nsini Bassey', credential: 'FCA', membershipNumber: 'ICAN/023390', sector: 'Industry', specialisation: 'Management accounting', yearAdmitted: 2004, chapterRole: 'Membership Secretary' },
  { id: 'dm-07', name: 'Taiye Fasan', credential: 'ACA', membershipNumber: 'ICAN/051204', sector: 'Financial services', specialisation: 'Internal audit', yearAdmitted: 2016, chapterRole: 'Welfare Officer' },
  { id: 'dm-08', name: 'Oluwakemi Toluwani', credential: 'FCA', membershipNumber: 'ICAN/028855', sector: 'Consulting', specialisation: 'Risk advisory', yearAdmitted: 2006, chapterRole: 'Publicity Officer' },
  { id: 'dm-09', name: 'Aisha Bello Oroche', credential: 'FCA', membershipNumber: 'ICAN/027431', sector: 'Public sector', specialisation: 'Government audit', yearAdmitted: 2006, chapterRole: 'Assistant General Secretary' },
  { id: 'dm-10', name: 'Ngozi Francisca Ashinze', credential: 'FCA', membershipNumber: 'ICAN/021760', sector: 'Public practice', specialisation: 'Forensic accounting', yearAdmitted: 2002, chapterRole: 'Technical Secretary' },
  { id: 'dm-11', name: 'Monica Chinenye Nosike', credential: 'FCA', membershipNumber: 'ICAN/018902', sector: 'Consulting', specialisation: 'Transaction services', yearAdmitted: 2000, chapterRole: 'Immediate Past Chairperson' },
  { id: 'dm-12', name: 'Charity Okongwu', credential: 'FCA', membershipNumber: 'ICAN/017234', sector: 'Public practice', specialisation: 'Insolvency and restructuring', yearAdmitted: 1999, chapterRole: 'Ex-Officio Member' },
  { id: 'dm-13', name: 'Halima Sadiq Umar', credential: 'ACA', membershipNumber: 'ICAN/056420', sector: 'Financial services', specialisation: 'Treasury and liquidity', yearAdmitted: 2019, chapterRole: null },
  { id: 'dm-14', name: 'Grace Iyabo Adeniyi', credential: 'FCA', membershipNumber: 'ICAN/024577', sector: 'Industry', specialisation: 'Cost and inventory control', yearAdmitted: 2004, chapterRole: null },
  { id: 'dm-15', name: 'Chiamaka Nwosu', credential: 'ACA', membershipNumber: 'ICAN/059013', sector: 'Public practice', specialisation: 'Tax compliance', yearAdmitted: 2021, chapterRole: null },
  { id: 'dm-16', name: 'Rukayat Adebola Salami', credential: 'ACA', membershipNumber: 'ICAN/054766', sector: 'Public sector', specialisation: 'Grant and donor reporting', yearAdmitted: 2018, chapterRole: null },
  { id: 'dm-17', name: 'Esther Terlumun Akaa', credential: 'FCA', membershipNumber: 'ICAN/025991', sector: 'Academia', specialisation: 'Accounting education', yearAdmitted: 2005, chapterRole: null },
  { id: 'dm-18', name: 'Zainab Yusuf Bala', credential: 'ACA', membershipNumber: 'ICAN/060842', sector: 'Industry', specialisation: 'Financial planning and analysis', yearAdmitted: 2022, chapterRole: null },
]

export const firms: Firm[] = [
  { id: 'fm-01', name: 'Olawoore & Co. (Chartered Accountants)', principal: 'Biola Olawoore, FCA', licenceNumber: 'PL/2009/0431', services: ['Statutory audit', 'Assurance', 'Advisory'], area: 'Wuse II', licenceStatus: 'Active' },
  { id: 'fm-02', name: 'Ashinze Forensic Partners', principal: 'Ngozi Francisca Ashinze, FCA', licenceNumber: 'PL/2012/0788', services: ['Forensic accounting', 'Investigations', 'Expert witness'], area: 'Garki', licenceStatus: 'Active' },
  { id: 'fm-03', name: 'Okongwu Restructuring Advisers', principal: 'Charity Okongwu, FCA', licenceNumber: 'PL/2007/0219', services: ['Insolvency', 'Restructuring', 'Corporate recovery'], area: 'Maitama', licenceStatus: 'Active' },
  { id: 'fm-04', name: 'Nwosu Tax & Compliance', principal: 'Chiamaka Nwosu, ACA', licenceNumber: 'PL/2023/1904', services: ['Tax compliance', 'Transfer pricing', 'Revenue disputes'], area: 'Jabi', licenceStatus: 'Renewal due' },
  { id: 'fm-05', name: 'Adeniyi & Associates', principal: 'Grace Iyabo Adeniyi, FCA', licenceNumber: 'PL/2010/0552', services: ['Bookkeeping', 'Management accounts', 'Payroll'], area: 'Gwarinpa', licenceStatus: 'Active' },
  { id: 'fm-06', name: 'Sadiq Umar Consulting', principal: 'Halima Sadiq Umar, ACA', licenceNumber: 'PL/2022/1671', services: ['Treasury advisory', 'Financial modelling'], area: 'Central Business District', licenceStatus: 'Active' },
]

export const jobs: JobListing[] = [
  { id: 'job-01', title: 'Head of Internal Audit', organisation: 'Federal agency, FCT', location: 'Abuja', type: 'Full-time', level: 'Executive', postedAt: '2026-08-26', closesAt: '2026-09-20', summary: 'Lead the internal audit function across programme and grant spending. Chartered qualification and ten years post-qualification experience required.' },
  { id: 'job-02', title: 'Financial Reporting Manager', organisation: 'Commercial bank', location: 'Abuja', type: 'Full-time', level: 'Senior', postedAt: '2026-08-19', closesAt: '2026-09-15', summary: 'Own the IFRS reporting cycle and regulatory returns. Experience with prudential reporting is an advantage.' },
  { id: 'job-03', title: 'Tax Advisory Associate', organisation: 'Mid-tier practice', location: 'Abuja', type: 'Full-time', level: 'Mid', postedAt: '2026-08-14', closesAt: '2026-09-12', summary: 'Advisory work across corporate and indirect tax with a client base spanning energy, telecoms and public sector contractors.' },
  { id: 'job-04', title: 'Grant Finance Officer', organisation: 'International development partner', location: 'Abuja', type: 'Contract', level: 'Mid', postedAt: '2026-08-08', closesAt: '2026-09-05', summary: 'Donor reporting, sub-grantee assurance and compliance monitoring across a multi-state portfolio.' },
  { id: 'job-05', title: 'Audit Senior', organisation: 'Chartered accountants, Wuse', location: 'Abuja', type: 'Full-time', level: 'Mid', postedAt: '2026-07-30', closesAt: '2026-09-01', summary: 'Run field teams on statutory audits for medium-sized entities. Newly qualified members encouraged to apply.' },
  { id: 'job-06', title: 'Lecturer, Accounting', organisation: 'Private university, FCT', location: 'Abuja', type: 'Part-time', level: 'Mid', postedAt: '2026-07-22', closesAt: '2026-09-30', summary: 'Teach financial reporting and audit at undergraduate level. Suits a practitioner seeking a teaching commitment alongside practice.' },
]
