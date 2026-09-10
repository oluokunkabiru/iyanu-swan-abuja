import { Navigate, Route, Routes } from 'react-router-dom'
import { Layout } from '@/components/layout/Layout'
import { MembersLayout } from '@/components/layout/MembersLayout'
import { ProtectedRoute } from '@/routes/ProtectedRoute'

import About from '@/pages/About'
import Announcements from '@/pages/Announcements'
import Committees, { CommitteeDetail } from '@/pages/Committees'
import Constitution from '@/pages/Constitution'
import Contact from '@/pages/Contact'
import Cpd from '@/pages/Cpd'
import Directory from '@/pages/Directory'
import EventDetail from '@/pages/EventDetail'
import Events from '@/pages/Events'
import Faqs from '@/pages/Faqs'
import Firms from '@/pages/Firms'
import Gallery from '@/pages/Gallery'
import Governance from '@/pages/Governance'
import Home from '@/pages/Home'
import Jobs from '@/pages/Jobs'
import Login from '@/pages/Login'
import Membership from '@/pages/Membership'
import MembershipRegister from '@/pages/MembershipRegister'
import Mentorship from '@/pages/Mentorship'
import News from '@/pages/News'
import NewsDetail from '@/pages/NewsDetail'
import NotFound from '@/pages/NotFound'
import PaymentCallback from '@/pages/PaymentCallback'
import Practice from '@/pages/Practice'
import Publications from '@/pages/Publications'
import Resources from '@/pages/Resources'
import Students from '@/pages/Students'
import Trainings from '@/pages/Trainings'

import MembersCpd from '@/pages/members/Cpd'
import MembersOverview from '@/pages/members/Overview'
import MembersProfile from '@/pages/members/Profile'
import MembersSubscription from '@/pages/members/Subscription'
import MembersTickets from '@/pages/members/Tickets'

export function AppRouter() {
  return (
    <Routes>
      <Route element={<Layout />}>
        <Route index element={<Home />} />

        {/* The chapter */}
        <Route path="about" element={<About />} />
        <Route path="governance" element={<Governance />} />
        <Route path="constitution" element={<Constitution />} />
        <Route path="committees" element={<Committees />} />
        <Route path="committees/:slug" element={<CommitteeDetail />} />
        <Route path="faqs" element={<Faqs />} />
        <Route path="contact" element={<Contact />} />

        {/* Membership */}
        <Route path="membership" element={<Membership />} />
        <Route path="membership/register" element={<MembershipRegister />} />
        <Route path="mentorship" element={<Mentorship />} />
        <Route path="students" element={<Students />} />

        {/* Professional development */}
        <Route path="cpd" element={<Cpd />} />
        <Route path="cpd/trainings" element={<Trainings />} />
        <Route path="practice" element={<Practice />} />
        <Route path="jobs" element={<Jobs />} />

        {/* Events and news */}
        <Route path="events" element={<Events />} />
        <Route path="events/:slug" element={<EventDetail />} />
        <Route path="news" element={<News />} />
        <Route path="news/:slug" element={<NewsDetail />} />
        <Route path="gallery" element={<Gallery />} />
        <Route path="announcements" element={<Announcements />} />

        {/* Directories and library */}
        <Route path="directory" element={<Directory />} />
        <Route path="directory/firms" element={<Firms />} />
        <Route path="publications" element={<Publications />} />
        <Route path="resources" element={<Resources />} />

        {/* Account */}
        <Route path="login" element={<Login />} />
        <Route path="payments/callback" element={<PaymentCallback />} />
        <Route path="register" element={<Navigate to="/membership/register" replace />} />
        <Route path="dashboard" element={<Navigate to="/members" replace />} />

        <Route
          path="members"
          element={
            <ProtectedRoute>
              <MembersLayout />
            </ProtectedRoute>
          }
        >
          <Route index element={<MembersOverview />} />
          <Route path="cpd" element={<MembersCpd />} />
          <Route path="subscription" element={<MembersSubscription />} />
          <Route path="tickets" element={<MembersTickets />} />
          <Route path="profile" element={<MembersProfile />} />
        </Route>

        <Route path="*" element={<NotFound />} />
      </Route>
    </Routes>
  )
}
