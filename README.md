# production-tracker

A full-stack application to manage Products, Projects, and Product Items with role-based access:

- **Product Manager**: Manage products and projects  
- **Project Manager**: Manage projects and view summaries  
- **Production Engineer**: Manage product items and update statuses  

## Tech Stack

- **Backend**: Laravel 12, PHP 8.3, MySQL, Scribe (API docs)  
- **Frontend**: Nuxt 3, Vue 3, TypeScript, Tailwind CSS  

## Repo Structure
production-tracker/
├── backend/ # Laravel API
├── frontend/ # Nuxt 3 app

## Setup

### Backend

```
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### Frontend
```
cd frontend
pnpm install
pnpm run dev
```

## API Docs
Generated with Scribe

Located in .scribe/ folder (open .scribe/index.html to view)

To regenerate: php artisan scribe:generate (run in backend)

## Tests
Backend tests in backend/tests/Feature

Run with php artisan test
