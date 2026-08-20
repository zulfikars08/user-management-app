import { createContext, useCallback, useEffect, useState } from 'react'
import { loginRequest, logoutRequest, meRequest } from '../api/authApi'
import { TOKEN_KEY } from '../api/apiClient'
import { apiError } from '../api/errorMessage'

const AuthContext = createContext(null)

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null)
  const [loading, setLoading] = useState(true)
  const [restoreError, setRestoreError] = useState('')

  const clearSession = useCallback(() => {
    sessionStorage.removeItem(TOKEN_KEY)
    setUser(null)
  }, [])

  useEffect(() => {
    const restore = async () => {
      if (!sessionStorage.getItem(TOKEN_KEY)) return setLoading(false)
      try { setUser((await meRequest()).data.data) }
      catch (error) {
        const parsed = apiError(error)
        if (parsed.status === 401) clearSession()
        else setRestoreError(parsed.message)
      } finally { setLoading(false) }
    }
    restore()
  }, [clearSession])

  const login = async (credentials) => {
    const { data } = await loginRequest(credentials)
    sessionStorage.setItem(TOKEN_KEY, data.data.token)
    setUser(data.data.user)
  }

  const logout = useCallback(async () => {
    try { await logoutRequest() } finally { clearSession() }
  }, [clearSession])

  return <AuthContext.Provider value={{ user, loading, restoreError, login, logout, clearSession }}>{children}</AuthContext.Provider>
}

export { AuthContext }
