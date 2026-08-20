export default function LoadingState({ label = 'Loading…', fullPage = false }) {
  return <div className={fullPage ? 'loading-page' : 'loading-state'} role="status"><span className="spinner-border spinner-border-sm" aria-hidden="true" /> <span>{label}</span></div>
}
