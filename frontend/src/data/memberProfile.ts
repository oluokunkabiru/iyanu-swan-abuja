import { Award, BookOpen, BriefcaseBusiness, Building2, ContactRound, GraduationCap, IdCard, UserRound } from 'lucide-react'
import { maritalStatuses, memberTitles, nigerianStates } from '@/data/bioData'
import type { ProfileSection } from '@/types'

export interface ProfileField {
  key: string
  label: string
  source?: 'root' | 'bio'
  type?: 'text' | 'email' | 'tel' | 'date' | 'number' | 'textarea' | 'select' | 'checkbox'
  options?: string[]
  readOnly?: boolean
  group?: string
  maxLength?: number
  min?: number
  max?: number
}

export const profileSections: { id: ProfileSection; label: string; description: string; icon: typeof UserRound; fields: ProfileField[] }[] = [
  { id: 'bio', label: 'BioData', description: 'Your personal details and next-of-kin information.', icon: UserRound, fields: [
    {key:'firstName',label:'First name',source:'root',readOnly:true},
    {key:'middleName',label:'Middle name',source:'root',readOnly:true},
    {key:'lastName',label:'Surname',source:'root',readOnly:true},
    {key:'title',label:'Title',source:'bio',type:'select',options:memberTitles},
    {key:'formerName',label:'Former / maiden name',source:'bio'},
    {key:'dateOfBirth',label:'Date of birth',source:'root',type:'date'},
    {key:'gender',label:'Gender',type:'select',options:['Female','Male','Prefer not to say']},
    {key:'maritalStatus',label:'Marital status',source:'bio',type:'select',options:maritalStatuses},
    {key:'nationality',label:'Nationality',source:'bio'},
    {key:'stateOfOrigin',label:'State of origin',source:'bio',type:'select',options:nigerianStates},
    {key:'lgaOfOrigin',label:'Local government of origin',source:'bio'},
    {key:'nextOfKinName',label:'Full name',source:'bio',group:'Next of kin'},
    {key:'nextOfKinRelationship',label:'Relationship',source:'bio',group:'Next of kin'},
    {key:'nextOfKinPhone',label:'Telephone number',source:'bio',type:'tel',group:'Next of kin'},
    {key:'nextOfKinAddress',label:'Address',source:'bio',type:'textarea',group:'Next of kin',maxLength:1000},
  ]},
  { id: 'contact', label: 'Contact details', description: 'How the chapter can reach you, at home and at work.', icon: ContactRound, fields: [
    {key:'email',label:'Registered email',source:'root',type:'email',readOnly:true},
    {key:'phone',label:'Mobile telephone number',source:'root',type:'tel'},
    {key:'alternatePhone',label:'Alternate telephone number',source:'bio',type:'tel'},
    {key:'personalEmail',label:'Personal email',source:'root',type:'email'},
    {key:'officialEmail',label:'Official email',source:'root',type:'email'},
    {key:'notificationEmailPreference',label:'Send chapter emails to',source:'root',type:'select',options:['registered','personal','official','all']},
    {key:'residentialAddress',label:'Contact address',source:'root',type:'textarea',group:'Contact address',maxLength:1000},
    {key:'city',label:'Contact city',group:'Contact address'},
    {key:'state',label:'Contact state',group:'Contact address'},
    {key:'country',label:'Contact country',group:'Contact address'},
    {key:'lga',label:'Contact address LGA',group:'Contact address'},
    {key:'placeOfWork',label:'Employer / place of work',source:'root',group:'Office address'},
    {key:'officeAddress',label:'Office address',type:'textarea',group:'Office address',maxLength:1000},
    {key:'officeCity',label:'Office city',group:'Office address'},
    {key:'officeState',label:'Office state',group:'Office address'},
    {key:'officeCountry',label:'Office country',group:'Office address'},
  ]},
  { id: 'academic', label: 'Academic details', description: 'Your highest qualification and academic background.', icon: GraduationCap, fields: [
    {key:'qualification',label:'Highest qualification'}, {key:'institution',label:'Institution'},
    {key:'course',label:'Course of study'}, {key:'graduationYear',label:'Year of graduation',type:'number',min:1950,max:2100},
  ]},
  { id: 'professional', label: 'Professional details', description: 'Your professional memberships, expertise and certifications.', icon: Award, fields: [
    {key:'credential',label:'ICAN level',source:'root',readOnly:true},
    {key:'yearAdmitted',label:'Year admitted',source:'root',type:'number',min:1950,max:2100},
    {key:'sector',label:'Sector',source:'root',type:'select',options:['Public practice','Public sector','Financial services','Industry','Academia','Consulting']},
    {key:'specialisation',label:'Area of specialisation',source:'root'},
    {key:'body',label:'Other professional body'}, {key:'membershipNumber',label:'Other professional membership number'},
    {key:'certifications',label:'Professional certifications',type:'textarea',maxLength:2000},
  ]},
  { id: 'membership', label: 'Membership details', description: 'Your chapter membership and directory preferences.', icon: IdCard, fields: [
    {key:'membershipNumber',label:'Membership number',source:'root',readOnly:true},
    {key:'credential',label:'Membership level',source:'root',readOnly:true},
    {key:'membershipStatus',label:'Membership status',source:'root',readOnly:true},
    {key:'joinedAt',label:'Member since',source:'root',type:'date',readOnly:true},
    {key:'chapterRole',label:'Chapter role',source:'root',readOnly:true},
    {key:'isDirectoryListed',label:'Show my name, photo and professional details in the member directory',source:'root',type:'checkbox'},
  ]},
  { id: 'faculty', label: 'Faculty details', description: 'Your faculty affiliation and membership information.', icon: BookOpen, fields: [
    {key:'name',label:'Faculty name'}, {key:'membershipNumber',label:'Faculty membership number'},
    {key:'joinedYear',label:'Year joined',type:'number',min:1950,max:2100},
  ]},
  { id: 'licence', label: 'Licence details', description: 'Your professional licence and practice information.', icon: Building2, fields: [
    {key:'number',label:'Licence number'}, {key:'category',label:'Licence category'},
    {key:'issuingBody',label:'Issuing body'}, {key:'practiceName',label:'Practice / firm name'},
    {key:'issuedOn',label:'Date issued',type:'date'}, {key:'expiresOn',label:'Expiry date',type:'date'},
  ]},
  { id: 'career', label: 'Career details', description: 'Your current role, experience and career background.', icon: BriefcaseBusiness, fields: [
    {key:'placeOfWork',label:'Employer / place of work',source:'root'}, {key:'jobTitle',label:'Current designation / job title'},
    {key:'employmentType',label:'Employment type',type:'select',options:['Full-time','Part-time','Self-employed','Contract','Retired','Not currently employed']},
    {key:'industry',label:'Industry'}, {key:'yearsOfExperience',label:'Years of experience',type:'number',min:0,max:80},
    {key:'summary',label:'Career summary',type:'textarea',maxLength:3000},
  ]},
]

export function profileSectionPath(section: ProfileSection) {
  return section === 'bio' ? '/members/profile' : `/members/profile/${section}`
}
