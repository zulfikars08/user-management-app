export default function SearchBar({ search, onSearchChange, onSubmit, onReset }) {
  return <form className="search-form" onSubmit={onSubmit} role="search">
    <div className="flex-grow-1"><label htmlFor="user-search" className="form-label">Search by name or ID</label><input id="user-search" className="form-control" value={search} onChange={(e) => onSearchChange(e.target.value)} placeholder="e.g. Zulfikar or 10" /></div>
    <button className="btn btn-primary" type="submit">Search</button>
    <button className="btn btn-light" type="button" onClick={onReset}>Reset</button>
  </form>
}
