import UserRow from './UserRow'

export default function UserTable({ users, canManageUsers, onEdit, onDelete }) {
  if (!users.length) return <div className="empty-state"><strong>No users found.</strong><span>Try another name or ID.</span></div>
  return <div className="table-responsive"><table className="table align-middle mb-0"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Created</th>{canManageUsers && <th>Actions</th>}</tr></thead><tbody>{users.map((user) => <UserRow key={user.id} user={user} canManageUsers={canManageUsers} onEdit={onEdit} onDelete={onDelete} />)}</tbody></table></div>
}
