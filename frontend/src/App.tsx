import { BrowserRouter } from 'react-router-dom'
import { AuthProvider } from '@/context/AuthContext'
import { SettingsProvider } from '@/context/SettingsContext'
import { ThemeProvider } from '@/context/ThemeContext'
import { AppRouter } from '@/routes/AppRouter'

function App() {
  return (
    <ThemeProvider>
      <BrowserRouter>
        <SettingsProvider>
          <AuthProvider>
            <AppRouter />
          </AuthProvider>
        </SettingsProvider>
      </BrowserRouter>
    </ThemeProvider>
  )
}

export default App
