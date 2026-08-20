import { useEffect, useState } from 'react'

const blank = { name: '', email: '', password: '', role: 'user' }

export default function UserFormModal({ user, open, busy, apiErrors, onSubmit, onClose }) {
  const [form, setForm] = useState(blank)
  const [localErrors, setLocalErrors] = useState({})

  useEffect(() => {
    // oxlint-disable-next-line react-hooks/set-state-in-effect -- reset draft when selected record changes
    if (open) setForm(user ? { name: user.name, email: user.email, password: '', role: user.role } : blank)
    setLocalErrors({})
  }, [open, user])

  if (!open) return null
  const change = (event) => setForm((value) => ({ ...value, [event.target.name]: event.target.value }))
  const submit = (event) => {
    event.preventDefault()
    const errors = {}
    if (!form.name.trim()) errors.name = ['Name is required.']
    if (!/^\S+@\S+\.\S+$/.test(form.email)) errors.email = ['Enter a valid email address.']
    if (!user && form.password.length < 8) errors.password = ['Password must contain at least 8 characters.']
    if (user && form.password && form.password.length < 8) errors.password = ['Password must contain at least 8 characters.']
    if (Object.keys(errors).length) return setLocalErrors(errors)
    const payload = { ...form }
    if (user && !payload.password) delete payload.password
    onSubmit(payload)
  }
  const errors = { ...localErrors, ...apiErrors }
  return <div className="modal-layer" role="dialog" aria-modal="true" aria-labelledby="form-title"><div className="modal-card"><div className="modal-header"><div><span className="eyebrow">{user ? 'Edit record' : 'New record'}</span><h2 id="form-title">{user ? 'Update user' : 'Add user'}</h2></div><button className="btn-close" onClick={onClose} aria-label="Close" /></div><form onSubmit={submit}><div className="modal-body form-grid">
    <div><label className="form-label" htmlFor="name">Name</label><input id="name" name="name" className={`form-control ${errors.name ? 'is-invalid' : ''}`} value={form.name} onChange={change} autoFocus />{errors.name && <div className="invalid-feedback">{errors.name[0]}</div>}</div>
    <div><label className="form-label" htmlFor="email">Email</label><input id="email" name="email" type="email" className={`form-control ${errors.email ? 'is-invalid' : ''}`} value={form.email} onChange={change} />{errors.email && <div className="invalid-feedback">{errors.email[0]}</div>}</div>
    <div><label className="form-label" htmlFor="password">Password {user && <span className="text-secondary">(leave blank to keep current)</span>}</label><input id="password" name="password" type="password" className={`form-control ${errors.password ? 'is-invalid' : ''}`} value={form.password} onChange={change} autoComplete="new-password" />{errors.password && <div className="invalid-feedback">{errors.password[0]}</div>}</div>
    <div><label className="form-label" htmlFor="role">Role</label><select id="role" name="role" className={`form-select ${errors.role ? 'is-invalid' : ''}`} value={form.role} onChange={change}><option value="user">User</option><option value="admin">Admin</option></select>{errors.role && <div className="invalid-feedback">{errors.role[0]}</div>}</div>
  </div><div className="modal-footer"><button type="button" className="btn btn-light" onClick={onClose} disabled={busy}>Cancel</button><button className="btn btn-primary" disabled={busy}>{busy ? 'Saving…' : user ? 'Save changes' : 'Create user'}</button></div></form></div></div>
}
