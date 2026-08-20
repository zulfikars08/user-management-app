export default function UserRow({ user, canManageUsers, onEdit, onDelete }) {
  const created = new Intl.DateTimeFormat('en', { dateStyle: 'medium' }).format(new Date(user.created_at))
  return <tr><td className="text-secondary">#{user.id}</td><td><strong>{user.name}</strong></td><td>{user.email}</td><td><span className={`role-badge role-${user.role}`}>{user.role}</span></td><td className="text-secondary text-nowrap">{created}</td>{canManageUsers && <td><div className="action-buttons"><button className="btn btn-sm btn-light" onClick={() => onEdit(user)}>Edit</button><button className="btn btn-sm btn-outline-danger" onClick={() => onDelete(user)}>Delete</button></div></td>}</tr>
}
