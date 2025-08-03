# Architecture Overview - Production Tracker

## Folder Structure

```
production-tracker/
├── backend/             # Laravel API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   ├── Requests/
│   │   │   └── Middleware/
│   │   ├── Services/    # Business logic services
│   │   ├── Models/
│   │   └── ...
│   ├── routes/
│   │   └── api.php
│   ├── tests/           # Feature & API tests
│   └── ...
├── frontend/            # Nuxt 3 App
│   ├── pages/
│   ├── components/
│   ├── composables/
│   ├── stores/          # Pinia auth store
│   └── ...
└── README.md
```

---

## Technologies Used

**Backend (Laravel):**

- Laravel 12
- MySQL
- Laravel Form Requests
- Service Layer Architecture
- Role-based Access Control (RBAC)
- PHPUnit for API testing

**Frontend (Nuxt 3 + Vue 3 + TypeScript):**

- Tailwind CSS
- ShadCN-Vue for UI components
- Pinia for state management
- `useFetch()` for API calls

---

## Core Domain Models

- **Product**
  - id, name, version, description, price
- **Project**
  - Belongs to a Product
  - name, description
- **ProductItem**
  - Belongs to a Project
  - name, version, status (pending, in\_progress, completed, blocked)
- **User**
  - Role: Product Manager, Project Manager, Production Engineer

---

## Role-Based Access Control

- Middleware checks user roles on protected routes (Laravel + Nuxt middleware).
- UI is dynamically adapted based on roles (using Pinia auth store).

| Role                | Permissions                            |
| ------------------- | -------------------------------------- |
| Product Manager     | Manage Products & Projects             |
| Project Manager     | View summaries, analytics, performance |
| Production Engineer | Create & update Product Items          |

## Sample Users Seeded:
| Name                | Email                  | Role                | Password |
| ------------------- | ---------------------- | ------------------- | -------- |
| Product Manager     | [pm@example.com]       | Product Manager     | password |
| Project Manager     | [pj@example.com]       | Project Manager     | password |
| Production Engineer | [engineer@example.com] | Production Engineer | password |

---

## Analytics & Summary Features

- Product-wise performance: API aggregates items grouped by project + status
- Project summary: API aggregates status of all items per project
- CSV Export support for summaries
- Search and client-side pagination

---

## Service Layer Example (Laravel)

```php
// app/Services/ProductService.php
class ProductService
{
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product;
    }
}
```

---

## Testing

- Feature tests with role checks and data assertions
- Example:

```php
public function test_product_manager_can_create_product()
{
    $user = User::factory()->create(['role' => 'product_manager']);
    $this->actingAs($user)
         ->postJson('/api/products', [...])
         ->assertStatus(201);
}
```

---

## Database Relationships

- `products` 1\:N `projects`
- `projects` 1\:N `product_items`
- `users` have roles for permissions (using enum or string role column)

---

## Implemented Features

- Role-based Access (Product Manager, Project Manager, Engineer)
- Product CRUD (Backend + UI)
- Project CRUD linked to Products
- Product Item CRUD with status update
- Summaries by Project & Product (item status breakdowns)
- Export CSV/PDF for summaries
- Search and client-side pagination (Products, Projects, Items)
- API Test Coverage (Role-protected routes, basic CRUD)
- API Documentation using Scribe (`.scribe`)
- Form Request Validation
- Service Layer for Product & Project logic
- Composable usage for data fetching
- Clean folder structure for frontend & backend

---

## Optional Enhancements (Not Implemented Due to Time)

- Real-time updates (WebSockets for status)
- Activity Log (history of changes)
- Notifications for blocked/delayed projects
- Drag-and-drop reordering
- Batch item creation templates
- Budget or costing feature
- Dockerized deployment

## Time Spent
I spent approximately 13-14 hours on this challenge, distributed over a few focused sessions. My goal was to balance clean architecture, code quality, UI/UX, and necessary business logic within the given timeframe.

## Deployment (Optional)

- Laravel served via Artisan or Sail
- Nuxt 3 served via `npm run dev` or `build` + `start`

## Running Locally
- clone the git repo: git@github.com:programmerzia/production-tracker.git or https://github.com/programmerzia/production-tracker.git
## Backend
```
    cd backend
    composer install
    cp .env.example .env
    php artisan key:generate
    php artisan migrate --seed
    php artisan serve
```
## Frontend
```
    cd frontend
    pnpm install
    pnpm run dev
```
