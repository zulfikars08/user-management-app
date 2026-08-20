import { useAuth } from '../context/useAuth'

export default function ResponsiveLayout({ children }) {
  const { user, logout } = useAuth()
  return <div className="app-shell">
    <header className="app-header">
      <div className="container-xl header-content">
        <div><div className="brand-mark">UM</div><div><strong>User Management</strong><small>Secure administration</small></div></div>
        <div className="account-actions"><div className="account-copy"><span className="account-avatar" aria-hidden="true">{user.name.charAt(0).toUpperCase()}</span><span className="account-name">{user.name}</span><span className={`role-badge role-${user.role}`}>{user.role}</span></div><button className="btn btn-outline-secondary btn-sm" onClick={logout}>Log out</button></div>
      </div>
    </header>
    <main className="container-xl py-4 py-md-5">{children}</main>
  </div>
}
