# User Management App

Initial full-stack project foundation for a user management web application.

## Planned Stack

- React
- Vite
- Laravel
- MySQL

## Repository Structure

- `backend/` — Laravel backend
- `frontend/` — React and Vite frontend
- `docs/` — API and technical-test documentation

## Status

React user-management interface, Laravel API, MySQL persistence, Sanctum authentication, and role authorization are complete. Swagger documentation and deployment are not implemented yet.

## User REST API

Laravel exposes unprotected foundation routes under `/api/users` for CRUD, name/ID search, and server-side pagination. Requests use dedicated create/update validation and consistent JSON error responses. Authentication and role authorization will be added in Step 5.

| Method | Endpoint | Purpose |
| --- | --- | --- |
| GET | `/api/users` | List, search, and paginate users |
| POST | `/api/users` | Create user |
| GET | `/api/users/{user}` | Show user |
| PUT/PATCH | `/api/users/{user}` | Update user |
| DELETE | `/api/users/{user}` | Delete user |

Search with `?search=<name-or-id>` and paginate with `?page=1&per_page=10` (`per_page` maximum: 100).

## Authentication and Authorization

`POST /api/login` returns a Sanctum Bearer token. `GET /api/me` and `POST /api/logout` require that token. Login is limited to five attempts per minute. All user routes require authentication.

| Action | Admin | User |
| --- | --- | --- |
| View users | Yes | Yes |
| Create user | Yes | No |
| Update user | Yes | No |
| Delete user | Yes | No |

Unauthenticated requests receive HTTP 401. Authenticated users without required role receive HTTP 403. Public registration is not implemented.

## Frontend

Stack: React, Vite, React Router, Axios, and Bootstrap. The responsive interface includes login, session restoration, protected routing, role-aware CRUD controls, backend search by name or ID, server pagination, loading states, and visible API error handling.

Local URLs:

```text
Frontend: http://localhost:5173
Backend:  http://127.0.0.1:8000
```

Run backend:

```bash
cd backend
php artisan serve --host=127.0.0.1 --port=8000
```

Run frontend:

```bash
cd frontend
npm install
npm run dev
```

Copy `frontend/.env.example` to `frontend/.env` when local API URL differs. Vite environment variables are client-visible and must not contain secrets.

## Coding Test Requirement Mapping

`frontend/src/pages/UsersPage.jsx` is parent component. It passes real list state and callbacks to child components including `SearchBar`, `UserTable`, `UserFormModal`, `DeleteConfirmModal`, `PaginationControls`, `ErrorAlert`, and `LoadingState`. Props include `users`, `canManageUsers`, `onEdit`, `onDelete`, search callbacks, mutation callbacks, and pagination callbacks. Axios retrieves server-paginated records from `GET /api/users`.

## Database Setup

MySQL is the active backend database. Create the `user_management_app` database, copy `backend/.env.example` to `backend/.env`, and place local database credentials only in `backend/.env`. The example file contains placeholders only.

Run migrations from `backend/`:

```bash
php artisan migrate
```
