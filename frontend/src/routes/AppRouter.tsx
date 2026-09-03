import { Route, Routes } from 'react-router-dom'
import { Layout } from '@/components/layout/Layout'
import About from '@/pages/About'
import Contact from '@/pages/Contact'
import Dashboard from '@/pages/Dashboard'
import EventDetail from '@/pages/EventDetail'
import Events from '@/pages/Events'
import Gallery from '@/pages/Gallery'
import Home from '@/pages/Home'
import Login from '@/pages/Login'
import News from '@/pages/News'
import NewsDetail from '@/pages/NewsDetail'
import NotFound from '@/pages/NotFound'
import Register from '@/pages/Register'
import { ProtectedRoute } from '@/routes/ProtectedRoute'

export function AppRouter() {
  return (
    <Routes>
      <Route element={<Layout />}>
        <Route index element={<Home />} />
        <Route path="about" element={<About />} />
        <Route path="events" element={<Events />} />
        <Route path="events/:slug" element={<EventDetail />} />
        <Route path="gallery" element={<Gallery />} />
        <Route path="news" element={<News />} />
        <Route path="news/:slug" element={<NewsDetail />} />
        <Route path="contact" element={<Contact />} />
        <Route path="register" element={<Register />} />
        <Route path="login" element={<Login />} />
        <Route
          path="dashboard"
          element={
            <ProtectedRoute>
              <Dashboard />
            </ProtectedRoute>
          }
        />
        <Route path="*" element={<NotFound />} />
      </Route>
    </Routes>
  )
}
