# Development Roadmap & Sprint Task Checklist

This document serves as the single source of truth for the step-by-step implementation of the Help Desk & Ticket Management System. Each milestone contains actionable tasks, key requirements, and a clear Definition of Done.

---

## 🗺️ Master Sprint Overview

```text
Milestone 1: Project Scaffolding & Database Schema (Foundation)
       ↓
Milestone 2: Authentication & User Profile (Sanctum Tokens)
       ↓
Milestone 3: Ticket Categories Module (CRUD & Policies Practice)
       ↓
Milestone 4: Core Ticket Creation & Listing (Pagination & Numbering)
       ↓
Milestone 5: Ticket Lifecycle, Assignment & Activity History
       ↓
Milestone 6: Ticket Comments Module
       ↓
Milestone 7: Admin User Management & Dashboard Metrics
       ↓
Milestone 8: Frontend (Quasar SPA) Integration
```

---

## 📦 Milestone 1: Scaffolding & Database Migrations
**Goal:** Initialize the Laravel API backend, configure the MySQL database, and migrate all 5 core tables with constraints and indexes.

### Tasks:
- [Done] Create the Laravel project inside `backend/` (`composer create-project laravel/laravel backend`).
- [Done] Configure `backend/.env` with local MySQL database credentials.
- [Done] Create the PHP 8.4 Backed Enum: `app/Enums/UserRole.php` (`ADMIN = 'admin'`, `SUPPORT_AGENT = 'support_agent'`, `EMPLOYEE = 'employee'`).
- [Done] Create migration for `users` table:
  - Add `role` column (`string('role', 30)->default('employee')`).
  - Add `is_active` column (`boolean('is_active')->default(true)`).
- [Done] Create migration for `ticket_categories` table:
  - `id`, `name` (unique), `description` (nullable), `is_active` (default true), `timestamps`.
- [Done] Create migration for `tickets` table:
  - `id`, `ticket_number` (string, 30, unique).
  - `requester_id` (foreign key ➔ `users.id`, `restrictOnDelete`).
  - `assignee_id` (nullable foreign key ➔ `users.id`, `nullOnDelete`).
  - `category_id` (foreign key ➔ `ticket_categories.id`, `restrictOnDelete`).
  - `subject`, `description` (text), `status` (string, 20), `priority` (string, 20).
  - `resolution_notes` (text, nullable), `resolved_at` (nullable), `closed_at` (nullable), `timestamps`.
  - Composite indexes: `['assignee_id', 'status']`, `['status', 'created_at']`.
- [Done] Create migration for `ticket_comments` table:
  - `id`, `ticket_id` (foreign key ➔ `tickets.id`, `cascadeOnDelete`).
  - `user_id` (foreign key ➔ `users.id`, `restrictOnDelete`).
  - `body` (text), `timestamps`.
- [Done] Create migration for `ticket_activities` table:
  - `id`, `ticket_id` (foreign key ➔ `tickets.id`, `cascadeOnDelete`).
  - `user_id` (nullable foreign key ➔ `users.id`, `nullOnDelete`).
  - `event_type` (string, 50), `old_value` (nullable), `new_value` (nullable), `description` (nullable).
  - `created_at` (timestamp only, no `updated_at`).
  - Indexes: `ticket_id`, `created_at`.
- [Done] Create Seeders (`UserSeeder`, `CategorySeeder`) with dummy test accounts for each role (`admin@example.com`, `agent@example.com`, `employee@example.com`).
- [Done] Run `php artisan migrate:fresh --seed`.

### Definition of Done:
- [Done] Running `php artisan migrate:fresh --seed` runs completely without SQL errors.
- [Done] `SHOW CREATE TABLE tickets;` in MySQL confirms foreign key constraints and composite indexes.
- [Done] Database contains seeded users for all three roles and active categories.

---

## 🔐 Milestone 2: Authentication & User Profile (Sanctum Tokens)
**Goal:** Implement token-based authentication via Laravel Sanctum Bearer tokens and verify active user checks.

### Tasks:
- [Done] Ensure `HasApiTokens` trait is present on the `User` model.
- [Done] Create Form Request `app/Http/Requests/Api/V1/LoginRequest.php` (email and password required).
- [Done] Create `app/Http/Resources/Api/V1/UserResource.php`.
- [Done] Create `AuthController` under `app/Http/Controllers/Api/V1/`.
- [Done] Implement `POST /api/v1/login`:
  - Validate credentials.
  - Check `is_active === true` directly in auth attempt.
  - Return plaintext Sanctum token and user profile resource.
- [Done] Implement `POST /api/v1/logout`:
  - Revoke current token (`$request->user()->currentAccessToken()->delete()`).
- [Done] Implement `GET /api/v1/me`:
  - Return authenticated user data wrapped in `UserResource`.
- [Done] Build a Postman collection to test login, logout, and protected routes.

### Definition of Done:
- [ ] Invalid credentials return `401 Unauthorized` or `422 Unprocessable Content`.
- [ ] Deactivated users (`is_active = false`) are rejected during login.
- [ ] Login returns `{ "token": "...", "user": { ... } }`.
- [ ] Requests with `Authorization: Bearer <token>` succeed on `GET /api/v1/me`.
- [ ] Unauthenticated requests to protected endpoints return `401`.
- [ ] Logout invalidates the token so it cannot be reused.

---

## 🏷️ Milestone 3: Ticket Categories Module
**Goal:** Build category management to practice Form Requests, API Resources, and Policy authorization.

### Tasks:
- [ ] Create `CategoryPolicy` (only `ADMIN` can create, update, or deactivate; all authenticated users can view).
- [ ] Create `StoreCategoryRequest` and `UpdateCategoryRequest` (validate unique name, optional description).
- [ ] Create `CategoryResource`.
- [ ] Create `CategoryController` under `app/Http/Controllers/Api/V1/` (`index`, `store`, `show`, `update`).
- [ ] Implement `PATCH /api/v1/ticket-categories/{id}/status` to toggle active/inactive state.
- [ ] Register routes in `routes/api.php` under `/api/v1/` protected by `auth:sanctum`.

### Definition of Done:
- [ ] `GET /api/v1/ticket-categories` is accessible by all authenticated roles.
- [ ] `POST`, `PUT/PATCH`, and status changes by non-admins return `403 Forbidden`.
- [ ] Duplicate category names are rejected with `422 Unprocessable Content`.
- [ ] Inactive categories remain in the database for historical tickets.

---

## 🎫 Milestone 4: Ticket Creation & Listing
**Goal:** Allow employees to file tickets with formatted sequence numbers, and support filtered ticket listings.

### Tasks:
- [ ] Create `TicketService` under `app/Services/`.
- [ ] Implement unique ticket number generation (e.g., `TCK-YYYYMM-XXXX`).
- [ ] Create `StoreTicketRequest` (validates `subject`, `description`, and ensures `category_id` references an active category).
- [ ] Implement `POST /api/v1/tickets`:
  - Explicitly assign `requester_id` from `auth()->id()`.
  - Default status to `OPEN`.
  - Record `TICKET_CREATED` in `ticket_activities` within `DB::transaction()`.
- [ ] Create `TicketPolicy` (Employees view only own tickets; Agents & Admins view all).
- [ ] Implement `GET /api/v1/tickets`:
  - Server-side pagination (`paginate(15)`).
  - Query filters: `status`, `category_id`, and search by subject/ticket number.
  - Eager load relationships (`category`, `requester:id,name`, `assignee:id,name`).
- [ ] Create `TicketResource` to format ticket output.

### Definition of Done:
- [ ] Employees can create a ticket with valid inputs.
- [ ] Creating a ticket with an inactive category returns a validation error.
- [ ] `ticket_number` is auto-generated and unique.
- [ ] Employees calling `GET /api/v1/tickets` only see their own tickets.
- [ ] Agents calling `GET /api/v1/tickets` see all tickets with pagination metadata.

---

## 🔄 Milestone 5: Ticket Lifecycle, Assignment & Activity History
**Goal:** Support agents manage ticket workflows while capturing an immutable audit trail.

### Tasks:
- [ ] In `TicketService`, implement methods wrapped in `DB::transaction()`:
  - `assignTicket(Ticket $ticket, User $assignee, User $actor)`
  - `updateStatus(Ticket $ticket, TicketStatus $newStatus, User $actor)`
  - `updatePriority(Ticket $ticket, TicketPriority $newPriority, User $actor)`
- [ ] Validate allowable status transitions: `OPEN` ➔ `IN_PROGRESS` ➔ `RESOLVED` ➔ `CLOSED`.
- [ ] Create Form Requests: `AssignTicketRequest`, `UpdateStatusRequest`, `UpdatePriorityRequest`.
- [ ] Implement endpoints:
  - `PATCH /api/v1/tickets/{id}/assign`
  - `PATCH /api/v1/tickets/{id}/status`
  - `PATCH /api/v1/tickets/{id}/priority`
  - `GET /api/v1/tickets/{id}/activities`
- [ ] Enforce policies so only Agents and Admins can perform lifecycle changes.

### Definition of Done:
- [ ] Employees attempting to change ticket status or assign tickets receive `403 Forbidden`.
- [ ] Invalid status jumps (e.g., jumping from `OPEN` directly to `CLOSED`) are rejected.
- [ ] Assigning a non-agent user (e.g., assigning another employee) is rejected.
- [ ] Every transition inserts an activity record with `old_value`, `new_value`, and the acting `user_id`.
- [ ] `GET /api/v1/tickets/{id}/activities` returns the timeline in chronological order.

---

## 💬 Milestone 6: Ticket Comments
**Goal:** Enable communication on tickets while preserving conversation history.

### Tasks:
- [ ] Create `StoreCommentRequest` (validates `body` is required).
- [ ] Create `CommentPolicy` (user can comment only if they own the ticket or are an Agent/Admin).
- [ ] Implement `POST /api/v1/tickets/{id}/comments`:
  - Saves comment with `user_id = auth()->id()`.
  - Records `COMMENT_ADDED` in `ticket_activities`.
- [ ] Implement `GET /api/v1/tickets/{id}/comments`:
  - Eager load author (`with('user:id,name,role')`) to prevent N+1 queries.
- [ ] Create `CommentResource`.

### Definition of Done:
- [ ] An employee cannot view or add comments on another employee's ticket (`403 Forbidden`).
- [ ] Comments are returned with user details without causing N+1 database queries.
- [ ] Adding a comment logs an entry in `ticket_activities`.

---

## 📊 Milestone 7: Admin User Management & Dashboard Metrics
**Goal:** Admin user administration and high-performance dashboard summary counts.

### Tasks:
- [ ] Create `UserPolicy` (all actions restricted to `ADMIN` only).
- [ ] Implement admin user routes:
  - `GET /api/v1/users` (list users with pagination and role filters).
  - `POST /api/v1/users` (create user with role).
  - `PATCH /api/v1/users/{id}` (update user details and role).
  - `PATCH /api/v1/users/{id}/status` (activate or deactivate user).
- [ ] Implement `GET /api/v1/dashboard/summary`:
  - Return counts for `open_tickets`, `in_progress_tickets`, `resolved_tickets`, and `my_assigned_tickets`.
  - Use single aggregate queries / `withCount()` to avoid multiple full table scans.

### Definition of Done:
- [ ] Non-admin users cannot access user management endpoints (`403 Forbidden`).
- [ ] Deactivating an admin or user immediately prevents them from logging in.
- [ ] Dashboard metrics endpoint executes efficiently in 1–2 SQL queries.

---

## 🖥️ Milestone 8: Frontend (Quasar Vue 3 SPA)
**Goal:** Build the single-page application and integrate with the Laravel API.

### Tasks:
- [ ] Initialize Quasar project in `frontend/`.
- [ ] Configure Axios boot file (`src/boot/axios.js`):
  - Base URL: `http://localhost:8000/api/v1`.
  - Request interceptor attaching `Authorization: Bearer <token>`.
  - Response interceptor handling `401` by clearing auth state and redirecting to `/login`.
- [ ] Create Pinia `auth` store:
  - State: `user`, `token`, `isAuthenticated`.
  - Actions: `login`, `logout`, `fetchUser`.
  - Persist token in `localStorage`.
- [ ] Setup Vue Router guards:
  - Protect authenticated routes from guest access.
  - Redirect authenticated users away from `/login`.
  - Enforce role-based route access where appropriate.
- [ ] Build UI Screens using Quasar components:
  - `LoginPage.vue`: Form with validation and error notification.
  - `TicketListPage.vue`: `<q-table>` with server-side pagination, filters, and status badges.
  - `TicketCreatePage.vue`: Form with category `<q-select>` and text inputs.
  - `TicketDetailPage.vue`: Ticket details, status controls, comments thread, and `<q-timeline>` for activities.
  - `DashboardPage.vue`: Summary cards with ticket counts.
  - Admin management views for users and categories.

### Definition of Done:
- [ ] Complete end-to-end flow works: Employee logs in, creates a ticket, views it in the list.
- [ ] Support agent logs in, sees unassigned ticket, assigns to self, updates status to `IN_PROGRESS`, adds comment, and resolves ticket.
- [ ] Activity timeline visually reflects all state transitions.
- [ ] Network failures and validation errors show friendly notification banners (`QNotify`).
