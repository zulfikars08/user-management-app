export default function DeleteConfirmModal({ user, busy, onConfirm, onClose }) {
  if (!user) return null
  return <div className="modal-layer" role="dialog" aria-modal="true" aria-labelledby="delete-title"><div className="modal-card modal-card-sm"><div className="modal-header"><h2 id="delete-title">Delete user?</h2><button className="btn-close" onClick={onClose} aria-label="Close" /></div><div className="modal-body"><p>This permanently removes <strong>{user.name}</strong> ({user.email}).</p></div><div className="modal-footer"><button className="btn btn-light" onClick={onClose} disabled={busy}>Cancel</button><button className="btn btn-danger" onClick={onConfirm} disabled={busy}>{busy ? 'Deleting…' : 'Delete user'}</button></div></div></div>
}
