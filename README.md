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

Initial setup.

## Database Setup

MySQL is the active backend database. Create the `user_management_app` database, copy `backend/.env.example` to `backend/.env`, and place local database credentials only in `backend/.env`. The example file contains placeholders only.

Run migrations from `backend/`:

```bash
php artisan migrate
```
