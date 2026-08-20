import { useState } from 'react'
import { Navigate, useLocation, useNavigate } from 'react-router-dom'
import { apiError } from '../api/errorMessage'
import ErrorAlert from '../components/ErrorAlert'
import LoadingState from '../components/LoadingState'
import { useAuth } from '../context/useAuth'

function EyeIcon({ hidden }) {
  return hidden
    ? <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3l18 18M10.6 10.7a2 2 0 002.7 2.7M9.9 4.2A10.8 10.8 0 0112 4c5.5 0 9 6 9 6a16 16 0 01-2.2 2.8M6.6 6.6C4.3 8.1 3 10 3 10s3.5 6 9 6a9.8 9.8 0 004.1-.9" /></svg>
    : <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12s3.5-6 9-6 9 6 9 6-3.5 6-9 6-9-6-9-6z" /><circle cx="12" cy="12" r="2.5" /></svg>
}

export default function LoginPage() {
  const { user, loading, restoreError, login } = useAuth()
  const navigate = useNavigate()
  const location = useLocation()
  const [form, setForm] = useState({ email: '', password: '' })
  const [showPassword, setShowPassword] = useState(false)
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

  return <main className="login-page"><section className="login-panel"><div className="login-panel-inner"><div className="login-brand"><span className="brand-mark">UM</span><span>User Management App</span></div><div className="login-heading"><span className="eyebrow">Secure workspace</span><h1>Welcome back</h1><p>Sign in to manage user records and access controls.</p></div><ErrorAlert message={error} onDismiss={() => setError('')} />{restoreError && <ErrorAlert message={restoreError} />}<form onSubmit={submit} noValidate>
    <div className="mb-3"><label className="form-label" htmlFor="login-email">Email address</label><input id="login-email" type="email" className={`form-control form-control-lg ${errors.email ? 'is-invalid' : ''}`} value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} autoComplete="username" />{errors.email && <div className="invalid-feedback">{errors.email}</div>}</div>
    <div className="mb-4"><label className="form-label" htmlFor="login-password">Password</label><div className="password-control"><input id="login-password" type={showPassword ? 'text' : 'password'} className={`form-control form-control-lg ${errors.password ? 'is-invalid' : ''}`} value={form.password} onChange={(e) => setForm({ ...form, password: e.target.value })} autoComplete="current-password" /><button type="button" className="password-toggle" onClick={() => setShowPassword((visible) => !visible)} aria-label={showPassword ? 'Hide password' : 'Show password'} aria-pressed={showPassword}><EyeIcon hidden={showPassword} /></button></div>{errors.password && <div className="invalid-feedback d-block">{errors.password}</div>}</div>
    <button className="btn btn-primary btn-lg w-100" disabled={busy}>{busy ? 'Signing in…' : 'Sign in'}</button>
  </form><p className="login-note">Access is limited to authorized users.</p></div></section><aside className="login-aside" aria-hidden="true"><div className="login-aside-inner"><div><span className="status-dot" />API connected workspace</div><blockquote>Simple user administration with clear roles, safe access, and reliable records.</blockquote></div></aside></main>
}
