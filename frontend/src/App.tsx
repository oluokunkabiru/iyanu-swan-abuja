import { BrowserRouter, HashRouter } from 'react-router-dom'
import { AuthProvider } from '@/context/AuthContext'
import { ThemeProvider } from '@/context/ThemeContext'
import { AppRouter } from '@/routes/AppRouter'

/**
 * Set VITE_ROUTER=hash to build a standalone bundle that runs from the file
 * system or any static host without server-side rewrites. Everything else
 * uses normal history routing.
 */
const Router = import.meta.env.VITE_ROUTER === 'hash' ? HashRouter : BrowserRouter

function App() {
  return (
    <ThemeProvider>
      <Router>
        <AuthProvider>
          <AppRouter />
        </AuthProvider>
      </Router>
    </ThemeProvider>
  )
}

export default App
