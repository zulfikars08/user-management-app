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

Backend User REST API complete. Authentication, authorization, React interface, and Swagger documentation are not implemented yet.

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

## Database Setup

MySQL is the active backend database. Create the `user_management_app` database, copy `backend/.env.example` to `backend/.env`, and place local database credentials only in `backend/.env`. The example file contains placeholders only.

Run migrations from `backend/`:

```bash
php artisan migrate
```
