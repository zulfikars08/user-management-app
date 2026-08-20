# User Management App

Full-stack coding-test application for authenticated, role-based user administration.

## Features

- React login and protected user-management interface
- Laravel Sanctum Bearer-token authentication
- Admin/user role authorization
- User create, read, update, and delete
- Name or numeric-ID search
- Server-side pagination
- Backend and frontend validation/error handling
- Responsive Bootstrap layout
- Swagger/OpenAPI documentation
- Reproducible local demo accounts

## Tech Stack

- Backend: PHP 8.3, Laravel 12, MySQL 8, Sanctum, L5-Swagger
- Frontend: React 19, Vite 8, Axios, React Router, Bootstrap 5

## Prerequisites

- PHP 8.2+ with Laravel-required extensions
- Composer
- MySQL 8
- Node.js 20.19+ or 22.12+
- npm

## Repository Structure

```text
backend/   Laravel API, migrations, tests, Swagger, demo seeder
frontend/  React/Vite application
docs/      Reserved project documentation
```

## Backend Setup

```bash
cd backend
composer install
```

Copy `.env.example` to `.env`, then set local `APP_URL`, MySQL credentials, and demo passwords. Never commit `.env`.

Create the database:

```sql
CREATE DATABASE user_management_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Then run:

```bash
php artisan key:generate
php artisan migrate
php artisan l5-swagger:generate
php artisan serve --host=127.0.0.1 --port=8000
```

Backend URL: `http://127.0.0.1:8000`

## Demo Users

Safe default identities are defined in `.env.example`; passwords are required from ignored `backend/.env`:

```text
DEMO_ADMIN_EMAIL=admin@user-management.test
DEMO_ADMIN_PASSWORD=
DEMO_USER_EMAIL=user@user-management.test
DEMO_USER_PASSWORD=
```

Create or update only these identities:

```bash
php artisan db:seed --class=DemoUserSeeder
```

Seeder is idempotent and rejects empty passwords.

## Frontend Setup

```bash
cd frontend
npm ci
```

Copy `.env.example` to `.env` when API URL differs. Vite variables are public client configuration; never place secrets in them.

```bash
npm run dev
```

Frontend URL: `http://localhost:5173`

## Authentication and Roles

`POST /api/login` returns a Sanctum Bearer token. Frontend keeps it in `sessionStorage`, restores the session through `GET /api/me`, and revokes current token through `POST /api/logout`. Login is limited to five attempts per minute.

| Action | Admin | User | Guest |
| --- | --- | --- | --- |
| List/show users | Yes | Yes | 401 |
| Create/update/delete | Yes | 403 | 401 |

Frontend hiding improves UX only; Laravel middleware enforces security.

## User API

| Method | Endpoint | Purpose |
| --- | --- | --- |
| POST | `/api/login` | Authenticate |
| GET | `/api/me` | Current user |
| POST | `/api/logout` | Revoke current token |
| GET | `/api/users` | List/search/paginate users |
| POST | `/api/users` | Create user; admin only |
| GET | `/api/users/{user}` | Show user |
| PUT/PATCH | `/api/users/{user}` | Update user; admin only |
| DELETE | `/api/users/{user}` | Delete user; admin only |

Search with `?search=<name-or-id>`. Paginate with `?page=1&per_page=10`; `per_page` range is 1–100. Form Requests enforce email uniqueness, role values, required fields, and password length. API errors use consistent safe JSON for 401, 403, 404, 422, and 500 responses.

## API Documentation

Generate docs:

```bash
cd backend
php artisan l5-swagger:generate
```

With Laravel running, open `http://127.0.0.1:8000/api/documentation`. Protected operations use Swagger UI **Authorize** with a Sanctum Bearer token. Never save real tokens in source or documentation.

## Quality Checks

```bash
cd backend
php artisan test

cd ../frontend
npm run lint
npm run build
npm audit
```

## Coding Test Requirement Mapping

| Requirement | Implementation |
| --- | --- |
| React + Laravel | `frontend/`, `backend/` |
| CRUD / SQL operations | `UserController.php`, Eloquent/MySQL, `UsersPage.jsx` |
| Parent-child props | `UsersPage` passes real data/callbacks to search, table, forms, delete, pagination, error/loading children |
| Authentication | Sanctum, `AuthController`, `AuthContext` |
| Input validation | Laravel Form Requests and frontend form feedback |
| Protected role access | `auth:sanctum`, `EnsureUserHasRole` |
| Global exceptions | `backend/bootstrap/app.php` |
| API errors | `errorMessage.js`, visible alerts and form errors |
| Responsive CSS framework | Bootstrap 5, `ResponsiveLayout.jsx`, `index.css` |
| API fetching | Shared Axios client and `GET /api/users` |
| Filter | Backend name/numeric-ID search and `SearchBar` |
| Pagination | Laravel paginator and `PaginationControls` |
| Swagger | `OpenApiSpec.php`, generated specification, `/api/documentation` |

## Deployment

No deployment configuration or hosted URL is included. Repository and local demo application are current deliverables.
