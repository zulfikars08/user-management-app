export default function UserSummary({ total, currentPage, lastPage, role }) {
  const isAdmin = role === 'admin'
  const cards = [
    { value: total, label: 'Total Users' },
    { value: `${currentPage} / ${lastPage || 1}`, label: 'Current Page' },
    { value: isAdmin ? 'Admin' : 'User', label: isAdmin ? 'Full Management' : 'Read Only' },
  ]

  return <section className="summary-grid" aria-label="User directory summary">
    {cards.map((card) => <div className="summary-card" key={card.label}><strong>{card.value}</strong><span>{card.label}</span></div>)}
  </section>
}
