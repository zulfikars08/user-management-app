import { useState } from 'react'
import { Navigate, useLocation, useNavigate } from 'react-router-dom'
import { apiError } from '../api/errorMessage'
import ErrorAlert from '../components/ErrorAlert'
import LoadingState from '../components/LoadingState'
import { useAuth } from '../context/useAuth'

export default function LoginPage() {
  const { user, loading, restoreError, login } = useAuth()
  const navigate = useNavigate()
  const location = useLocation()
  const [form, setForm] = useState({ email: '', password: '' })
  const [error, setError] = useState('')
  const [errors, setErrors] = useState({})
  const [busy, setBusy] = useState(false)
  if (loading) return <LoadingState label="Restoring session…" />
  if (user) return <Navigate to="/users" replace />

  const submit = async (event) => {
    event.preventDefault()
    const validation = {}
    if (!/^\S+@\S+\.\S+$/.test(form.email)) validation.email = 'Enter a valid email address.'
    if (!form.password) validation.password = 'Password is required.'
    if (Object.keys(validation).length) return setErrors(validation)
    setBusy(true); setError(''); setErrors({})
    try { await login(form); navigate(location.state?.from?.pathname || '/users', { replace: true }) }
    catch (requestError) {
      const parsed = apiError(requestError, 'Invalid email or password.')
      setError(parsed.status === 401 ? 'Invalid email or password.' : parsed.message)
      setErrors(Object.fromEntries(Object.entries(parsed.errors).map(([key, value]) => [key, value[0]])))
    } finally { setBusy(false) }
  }

  return <main className="login-page"><section className="login-panel"><div className="login-brand"><span className="brand-mark">UM</span><span>User Management App</span></div><div className="login-heading"><span className="eyebrow">Secure workspace</span><h1>Welcome back</h1><p>Sign in to manage user records and access controls.</p></div><ErrorAlert message={error} onDismiss={() => setError('')} /><form onSubmit={submit} noValidate>
    {restoreError && <ErrorAlert message={restoreError} />}
    <div className="mb-3"><label className="form-label" htmlFor="login-email">Email address</label><input id="login-email" type="email" className={`form-control form-control-lg ${errors.email ? 'is-invalid' : ''}`} value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} autoComplete="username" />{errors.email && <div className="invalid-feedback">{errors.email}</div>}</div>
    <div className="mb-4"><label className="form-label" htmlFor="login-password">Password</label><input id="login-password" type="password" className={`form-control form-control-lg ${errors.password ? 'is-invalid' : ''}`} value={form.password} onChange={(e) => setForm({ ...form, password: e.target.value })} autoComplete="current-password" />{errors.password && <div className="invalid-feedback">{errors.password}</div>}</div>
    <button className="btn btn-primary btn-lg w-100" disabled={busy}>{busy ? 'Signing in…' : 'Sign in'}</button>
  </form><p className="login-note">Access is limited to authorized users.</p></section><aside className="login-aside" aria-hidden="true"><div><span className="status-dot" />API connected workspace</div><blockquote>Simple user administration with clear roles, safe access, and reliable records.</blockquote></aside></main>
}
