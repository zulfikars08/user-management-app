export default function PaginationControls({ currentPage, lastPage, total, onPageChange }) {
  return <nav className="pagination-bar" aria-label="User list pagination"><button className="btn btn-light btn-sm" disabled={currentPage <= 1} onClick={() => onPageChange(currentPage - 1)}>Previous</button><span>Page <strong>{currentPage}</strong> of <strong>{lastPage || 1}</strong> · {total} users</span><button className="btn btn-light btn-sm" disabled={currentPage >= lastPage} onClick={() => onPageChange(currentPage + 1)}>Next</button></nav>
}
