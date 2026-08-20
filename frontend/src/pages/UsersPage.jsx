import { useCallback, useEffect, useRef, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { createUser, deleteUser, getUsers, updateUser } from '../api/usersApi'
import { apiError } from '../api/errorMessage'
import DeleteConfirmModal from '../components/DeleteConfirmModal'
import ErrorAlert from '../components/ErrorAlert'
import LoadingState from '../components/LoadingState'
import PaginationControls from '../components/PaginationControls'
import ResponsiveLayout from '../components/ResponsiveLayout'
import SearchBar from '../components/SearchBar'
import UserFormModal from '../components/UserFormModal'
import UserSummary from '../components/UserSummary'
import UserTable from '../components/UserTable'
import { useAuth } from '../context/useAuth'

const emptyPagination = { currentPage: 1, lastPage: 1, perPage: 10, total: 0 }

export default function UsersPage() {
  const { user, clearSession } = useAuth()
  const navigate = useNavigate()
  const [users, setUsers] = useState([])
  const [searchInput, setSearchInput] = useState('')
  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)
  const [pagination, setPagination] = useState(emptyPagination)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [success, setSuccess] = useState('')
  const [formOpen, setFormOpen] = useState(false)
  const [editing, setEditing] = useState(null)
  const [deleting, setDeleting] = useState(null)
  const [busy, setBusy] = useState(false)
  const [formErrors, setFormErrors] = useState({})
  const requestId = useRef(0)
  const canManageUsers = user.role === 'admin'

  const handleError = useCallback((requestError) => {
    const parsed = apiError(requestError)
    if (parsed.status === 401) { clearSession(); navigate('/login', { replace: true, state: { message: parsed.message } }) }
    else setError(parsed.message)
    return parsed
  }, [clearSession, navigate])

  const loadUsers = useCallback(async () => {
    const currentRequest = ++requestId.current
    setLoading(true); setError('')
    try {
      const { data } = await getUsers({ page, per_page: 10, ...(search && { search }) })
      if (currentRequest !== requestId.current) return
      setUsers(data.data)
      setPagination({ currentPage: data.meta.current_page, lastPage: data.meta.last_page, perPage: data.meta.per_page, total: data.meta.total })
      if (!data.data.length && page > 1) setPage(page - 1)
    } catch (requestError) {
      if (currentRequest === requestId.current) handleError(requestError)
    } finally {
      if (currentRequest === requestId.current) setLoading(false)
    }
  }, [page, search, handleError])

  // oxlint-disable-next-line react-hooks/set-state-in-effect -- API synchronization belongs to route state changes
  useEffect(() => { loadUsers() }, [loadUsers])

  const submitSearch = (event) => { event.preventDefault(); setPage(1); setSearch(searchInput.trim()) }
  const resetSearch = () => { setSearchInput(''); setSearch(''); setPage(1) }
  const openCreate = () => { setEditing(null); setFormErrors({}); setFormOpen(true) }
  const openEdit = (selected) => { setEditing(selected); setFormErrors({}); setFormOpen(true) }
  const saveUser = async (payload) => {
    setBusy(true); setFormErrors({}); setError('')
    try {
      if (editing) { await updateUser(editing.id, payload); setSuccess('User updated successfully.') }
      else { await createUser(payload); setSuccess('User created successfully.') }
      setFormOpen(false)
      if (!editing && page !== 1) setPage(1)
      else await loadUsers()
    } catch (requestError) { const parsed = handleError(requestError); setFormErrors(parsed.errors) } finally { setBusy(false) }
  }
  const confirmDelete = async () => {
    setBusy(true); setError('')
    try { await deleteUser(deleting.id); setDeleting(null); setSuccess('User deleted successfully.'); await loadUsers() }
    catch (requestError) { handleError(requestError) } finally { setBusy(false) }
  }

  return <ResponsiveLayout><div className="page-heading"><div><span className="eyebrow">Directory</span><h1>Users</h1><p>Search, review, and manage account access.</p></div>{canManageUsers && <button className="btn btn-primary" onClick={openCreate}>Add user</button>}</div>
    {success && <div className="alert alert-success alert-dismissible" role="status">{success}<button className="btn-close" onClick={() => setSuccess('')} aria-label="Dismiss success" /></div>}
    <ErrorAlert message={error} onDismiss={() => setError('')} />
    <UserSummary total={pagination.total} currentPage={pagination.currentPage} lastPage={pagination.lastPage} role={user.role} />
    <section className="content-card"><div className="card-toolbar"><SearchBar search={searchInput} onSearchChange={setSearchInput} onSubmit={submitSearch} onReset={resetSearch} /></div>{loading ? <LoadingState label="Loading users…" /> : <UserTable users={users} canManageUsers={canManageUsers} onEdit={openEdit} onDelete={setDeleting} />}<PaginationControls currentPage={pagination.currentPage} lastPage={pagination.lastPage} total={pagination.total} onPageChange={setPage} /></section>
    <UserFormModal user={editing} open={formOpen} busy={busy} apiErrors={formErrors} onSubmit={saveUser} onClose={() => setFormOpen(false)} />
    <DeleteConfirmModal user={deleting} busy={busy} onConfirm={confirmDelete} onClose={() => setDeleting(null)} />
  </ResponsiveLayout>
}
