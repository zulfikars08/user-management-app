export function apiError(error, fallback = 'Something went wrong. Please try again.') {
  if (!error.response) return { message: 'Unable to connect to the server.', errors: {} }
  const status = error.response.status
  const messages = {
    401: 'Your session has expired. Please sign in again.',
    403: 'You do not have permission to perform this action.',
    404: 'The requested user could not be found.',
    500: 'The server could not complete your request.',
  }
  return {
    message: messages[status] || error.response.data?.message || fallback,
    errors: status === 422 ? error.response.data?.errors || {} : {},
    status,
  }
}
