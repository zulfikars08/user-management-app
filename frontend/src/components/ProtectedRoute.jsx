import { Navigate, useLocation } from 'react-router-dom'
import { useAuth } from '../context/useAuth'
import LoadingState from './LoadingState'

export default function ProtectedRoute({ children }) {
  const { user, loading } = useAuth()
  const location = useLocation()
  if (loading) return <LoadingState fullPage label="Restoring your session…" />
  return user ? children : <Navigate to="/login" replace state={{ from: location }} />
}
