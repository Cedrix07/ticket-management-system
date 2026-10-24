# Project Context & Environment Specifications

## 1. Project Overview & Business Domain
- **Project Name:** Help Desk & Ticket Management System
- **Type:** Single-tenant internal IT help desk & support ticket management system (portfolio project).
- **Architecture:** Decoupled Monorepo:
  - `backend/`: Laravel REST API (serving `/api/v1/` endpoints on port 8000).
  - `frontend/`: Vue 3 SPA built with Quasar Framework (Vite-powered, port 5173).

---

## 2. Pinned Tech Stack & Versions
Always enforce syntax and patterns compatible with these exact major versions:
- **PHP:** 8.4+ (use typed class properties, backed enums, match expressions, constructor promotion)
- **Laravel:** 13.x (lean bootstrap in `bootstrap/app.php`, no legacy `Kernel.php`)
- **Database:** MySQL 8.0 (InnoDB engine, utf8mb4 collation)
- **Node.js:** 20.x LTS
- **Frontend Framework:** Vue 3.4+ (Composition API with `<script setup>` syntax only)
- **UI Framework & Library:** Quasar Framework v2 (Vite-based; leverage `QTable`, `QDialog`, `QForm`, `QSelect`, `QBadge`, `QNotify`)
- **Styling:** Quasar built-in CSS & Flex grid utilities (no Tailwind CSS to prevent style collisions)
- **State Management:** Pinia 2.x (setup store syntax: `defineStore('id', () => { ... })`)
- **HTTP Client:** Axios (configured with API base URL and Bearer Token interceptor)
- **Authentication:** Laravel Sanctum API Tokens (Bearer tokens passed via `Authorization: Bearer <token>`)
- **Authorization:** PHP 8.4 Backed Enums (`UserRole: string`) combined with Laravel Policies and Gates (no third-party RBAC packages)

---

## 3. Domain Blueprint & Key Architectural Decisions
- **User Roles:** `ADMIN = 'admin'`, `SUPPORT_AGENT = 'support_agent'`, `EMPLOYEE = 'employee'`
- **Ticket Statuses:** `OPEN`, `IN_PROGRESS`, `RESOLVED`, `CLOSED`
- **Ticket Priorities:** `LOW`, `MEDIUM`, `HIGH`, `URGENT`
- **Ticket Numbering:** Formatted sequence with prefix (e.g., `TCK-YYYYMM-XXXX`) generated during creation
- **Activity History:** Logged explicitly via `TicketService` methods wrapped in `DB::transaction()`
- **Lifecycle Permissions:**
  - Employees: Can create tickets, view their own tickets, and add comments.
  - Support Agents / Admins: Can view all/assigned tickets, assign agents, update priorities, and transition statuses (`IN_PROGRESS`, `RESOLVED`, `CLOSED`).

---

## 4. Key Installed Libraries & Ecosystem Packages
*Check this list before recommending new dependencies. Do NOT introduce new npm or Composer packages without asking first.*

### Backend (PHP / Composer):
- `laravel/sanctum`: Personal Access Token authentication (`HasApiTokens`).
- `barryvdh/laravel-debugbar`: Dev environment query and profiling tool.

### Frontend (JavaScript / npm):
- `quasar` & `@quasar/extras`: UI component library and icon system.
- `vue-router`: 4.x (history mode, route navigation guards).
- `pinia`: 2.x (centralized auth and shared ticket state).
- `axios`: HTTP client configured in boot/plugin files.

---

## 5. Key CLI Commands & Workflows
*Commands are context-sensitive based on directory:*

### Backend (`ticket-management-system/backend`):
- **Dev Server:** `php artisan serve`
- **Backend Tests:** `php artisan test`
- **Lint / Code Style:** `./vendor/bin/pint`
- **Database Migrations:** `php artisan migrate:status`

### Frontend (`ticket-management-system/frontend`):
- **Dev Server:** `npm run dev` (or `npx quasar dev`)
- **Frontend Build Check:** `npm run build` (or `npx quasar build`)