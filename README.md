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

React user-management interface, Laravel API, MySQL persistence, Sanctum authentication, role authorization, and Swagger documentation are complete. Deployment is not implemented.

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

## API Documentation

Generate Swagger after backend dependencies are installed:

```bash
cd backend
php artisan l5-swagger:generate
```

With Laravel running, open `http://127.0.0.1:8000/api/documentation`. Protected operations use Swagger UI's **Authorize** control with a Sanctum Bearer token. Never store that token in project files.

## Demo Users

Set local passwords in ignored `backend/.env`; empty passwords are rejected:

```text
DEMO_ADMIN_EMAIL=admin@user-management.test
DEMO_ADMIN_PASSWORD=
DEMO_USER_EMAIL=user@user-management.test
DEMO_USER_PASSWORD=
```

Run the idempotent seeder:

```bash
cd backend
php artisan db:seed --class=DemoUserSeeder
```

The seeder updates only these two identities, assigns `admin` and `user` roles, and never truncates unrelated users.

## Coding Test Requirement Mapping

`frontend/src/pages/UsersPage.jsx` is parent component. It passes real list state and callbacks to child components including `SearchBar`, `UserTable`, `UserFormModal`, `DeleteConfirmModal`, `PaginationControls`, `ErrorAlert`, and `LoadingState`. Props include `users`, `canManageUsers`, `onEdit`, `onDelete`, search callbacks, mutation callbacks, and pagination callbacks. Axios retrieves server-paginated records from `GET /api/users`.

| Requirement | Implementation |
| --- | --- |
| CRUD | `UserController.php`, `UsersPage.jsx`, form/delete children |
| MySQL | Laravel MySQL configuration and migrations |
| React + Laravel | `frontend/`, `backend/` |
| CSS framework | Bootstrap 5 |
| Responsive layout | `ResponsiveLayout.jsx`, `frontend/src/index.css` |
| Swagger | `OpenApiSpec.php`, `/api/documentation` |
| Login | `LoginPage.jsx`, `POST /api/login` |
| Authentication/validation | Sanctum, Form Requests, frontend errors |
| Filter | Name/numeric-ID API search and `SearchBar` |
| Pagination | Laravel paginator and `PaginationControls` |
| Parent-child props | `UsersPage` and real child components |
| API fetch | Shared Axios client |
| Global exceptions | `backend/bootstrap/app.php` |
| Role security | `auth:sanctum`, `EnsureUserHasRole` |

## Database Setup

MySQL is the active backend database. Create the `user_management_app` database, copy `backend/.env.example` to `backend/.env`, and place local database credentials only in `backend/.env`. The example file contains placeholders only.

Run migrations from `backend/`:

```bash
php artisan migrate
```
