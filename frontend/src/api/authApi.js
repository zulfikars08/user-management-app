import apiClient from './apiClient'

export const loginRequest = (credentials) => apiClient.post('/login', credentials)
export const meRequest = () => apiClient.get('/me')
export const logoutRequest = () => apiClient.post('/logout')
