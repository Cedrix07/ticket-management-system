
# Help Desk & Ticket Management System

## 1. Project Overview

### Project Description

The Help Desk & Ticket Management System is a web application that allows employees to report technical issues and request assistance from a support team. Support agents can manage, prioritize, assign, and resolve tickets, while administrators manage users, categories, and system settings.

The goal is to build a practical, portfolio-ready application that demonstrates standard business application architecture, REST API development, authentication, role-based authorization, and frontend development.

### Problem Statement

Without a centralized ticketing system, support requests may be handled through scattered messages or verbal communication. This makes it difficult to track requests, prioritize urgent issues, monitor workloads, and review resolution history.

### Project Goals

- Allow employees to submit and track support tickets.
- Allow support agents to manage and resolve tickets.
- Allow administrators to manage users and ticket categories.
- Maintain a history of ticket activities and comments.
- Provide dashboards and filters for monitoring ticket status.
- Practice building a maintainable Laravel API and Vue 3 frontend.
- Apply practical software development best practices without unnecessary complexity.

---

## 2. Technology Stack

### Backend

- **Language & Runtime:** PHP 8.4+
- **Framework:** Laravel 13.x
- **Architecture:** Laravel REST API (monorepo layout in `backend/`)
- **Authentication:** Laravel Sanctum API Tokens (Bearer tokens)
- **Database:** MySQL 8.0 (InnoDB)
- **API Responses:** Laravel API Resources
- **Validation:** Form Request classes
- **Authorization:** PHP 8.4 Backed Enums (`UserRole`) with Laravel Policies and Gates
- **Automated Testing:** Laravel feature and unit tests where appropriate

### Frontend

- **Framework:** Vue 3
- **Language:** JavaScript
- **Component API:** Composition API with `<script setup>`
- **UI Library:** Quasar Framework
- **Routing:** Vue Router
- **State Management:** Pinia, where shared state is necessary
- **HTTP Client:** Axios
- **Frontend Testing:** Vitest and Vue Test Utils where appropriate

### Development Tools

- Git and GitHub for version control
- Postman for API testing
- Trello for task tracking
- Draw.io for ERD and system diagrams
- Markdown documentation for project specifications and technical decisions

### Architecture

The project is structured as a **decoupled monorepo** within a single root repository:

```text
ticket-management-system/
├── backend/          <-- Laravel REST API (port 8000)
└── frontend/         <-- Vue 3 + Quasar SPA (port 5173)
```

Data flow:
```text
Vue 3 + Quasar SPA (frontend/)
        |
        | HTTP requests with Authorization: Bearer <token>
        v
Laravel REST API (backend/)
        |
        | Eloquent ORM
        v
      MySQL
```

The frontend communicates with the backend through documented API endpoints. Business rules, validation, authorization, and data integrity must be enforced by the backend rather than relying on frontend checks alone.

---

## 3. Frontend UI Strategy

### UI Library

Use **Quasar Framework** as the primary UI component library.

Official documentation: https://quasar.dev/

The goal is to accelerate frontend development by using existing components rather than implementing common interface elements from scratch.

### Design Principles

- Use a clean, minimalist business application design.
- Prioritize usability and clarity over decorative elements.
- Maintain consistent spacing, typography, colors, and component behavior.
- Make the interface responsive for desktop, tablet, and mobile screens.
- Use consistent visual indicators for ticket statuses and priorities.
- Provide loading, empty, success, validation-error, and general-error states.
- Keep forms and workflows simple and predictable.
- Avoid unnecessary animations and overly complex UI patterns.

### Layout

Use a shared application layout containing:

- Sidebar navigation for primary modules.
- Top toolbar for the page title and user menu.
- Main content area for dashboards, tables, and forms.
- Responsive navigation behavior for smaller screens.

Use Quasar layout components such as `QLayout`, `QHeader`, `QDrawer`, `QPageContainer`, and `QPage` where appropriate.

### Reusable UI Components

Reuse Quasar components for:

- Data tables and ticket listings.
- Search fields, filters, and pagination.
- Form inputs, selects, and validation messages.
- Dialogs for confirmations and editing.
- Notifications for success and error feedback.
- Loading indicators and skeleton states.
- Status badges and priority indicators.
- Navigation menus and responsive layouts.

Prefer Quasar's `QTable`, `QForm`, `QInput`, `QSelect`, `QDialog`, `QBadge`, and `QNotify` functionality where appropriate.

Before creating a custom component, check whether Quasar already provides a suitable component.

Create custom reusable components only when they improve consistency or encapsulate project-specific behavior.

### Table Behavior

Ticket listings should support:

- Server-side pagination.
- Searching by ticket number or subject.
- Filtering by status, priority, category, and assignee where applicable.
- Sorting by supported fields.
- Loading and empty states.
- Clear feedback when a request fails.

Do not load every ticket into the browser just to implement pagination or filtering. Use backend query parameters and database queries.

### Theme

Use Quasar's theme configuration for consistent colors and styling.

The interface should have:

- A consistent primary color.
- Readable text and sufficient contrast.
- Clear status and priority indicators.
- Consistent spacing and typography.
- Accessible labels and keyboard-friendly controls.

Avoid adding another UI component library unless there is a clear requirement Quasar cannot reasonably satisfy.

---

## 4. MVP Scope

### Included Features

#### Authentication and Authorization

- User login and logout.
- Authenticated API access using Laravel Sanctum.
- Role-based access control.
- Protected frontend routes.
- Backend authorization for protected operations.

#### User Management

- Administrator can view users.
- Administrator can create users.
- Administrator can edit user details and roles.
- Administrator can activate or deactivate users when appropriate.

#### Ticket Categories

- Administrator can create categories.
- Administrator can view categories.
- Administrator can update categories.
- Administrator can deactivate categories that are no longer available for new tickets.

#### Ticket Management

- Employees can create tickets.
- Employees can view their own tickets.
- Employees can view ticket details and activity history.
- Employees can add comments to their tickets.
- Support agents can view and manage tickets they are authorized to access.
- Support agents can assign tickets to themselves or other authorized agents, subject to the chosen workflow.
- Support agents can update ticket status and priority.
- Support agents can add comments and resolution details.
- Users can filter and search tickets.

#### Ticket Activity History

- Record important ticket events.
- Track status changes.
- Track assignment changes.
- Track priority changes.
- Track comments and resolution-related events as appropriate.

#### Dashboard

- Display useful ticket counts.
- Show open and in-progress tickets.
- Show resolved and closed tickets.
- Provide role-appropriate summaries.

### Excluded from the Initial MVP

To keep the project achievable, defer these features:

- Email and SMS notifications.
- Real-time updates using WebSockets.
- Live chat.
- File attachments.
- SLA timers and escalation rules.
- Advanced analytics and report exports.
- External integrations.
- Complex automation rules.
- Multi-company or multi-tenant support.

These features may be added after the core workflow is stable.

---

## 5. User Roles

The system has three primary roles.

### 5.1 Administrator

Responsibilities:

- Manage user accounts.
- Assign user roles.
- Manage ticket categories.
- View tickets across the system.
- Access administrative dashboard information.

### 5.2 Support Agent

Responsibilities:

- View tickets they are authorized to manage.
- Assign or accept tickets according to the workflow.
- Update ticket status and priority.
- Communicate with employees through comments.
- Record resolution details.
- Review ticket activity history.

### 5.3 Employee

Responsibilities:

- Submit a support ticket.
- View their own tickets.
- Check ticket status.
- Add comments to their tickets.
- Review the ticket's activity history.

### Authorization Principle

The frontend may hide actions that a user is not allowed to perform, but this is not sufficient security.

Laravel must enforce authentication, role permissions, and resource-level authorization for every protected operation.

---

## 6. Ticket Statuses and Priorities

### Ticket Statuses

Use these statuses for the MVP:

| Status | Meaning |
|---|---|
| OPEN | The ticket has been submitted and is awaiting work. |
| IN_PROGRESS | A support agent is actively working on the ticket. |
| RESOLVED | The reported issue has been addressed. |
| CLOSED | The ticket is considered complete. |

The backend must validate permitted status transitions. Do not allow arbitrary status changes simply because a frontend dropdown contains the status.

### Ticket Priorities

| Priority | Meaning |
|---|---|
| LOW | Minor issue or request that can wait. |
| MEDIUM | Normal issue requiring attention. |
| HIGH | Significant issue that should be addressed soon. |
| URGENT | Critical issue requiring immediate attention. |

The backend must validate priority values and enforce authorization for priority changes.

---

## 7. Business Rules

### User Rules

- Every user must have a valid role.
- Only authorized administrators can manage users and roles.
- Deactivated users must not be able to authenticate or perform protected operations.
- A user's permissions must be checked by the backend.

### Ticket Creation Rules

- An authenticated employee can create a ticket.
- Every ticket must have a subject, description, and valid category.
- New tickets start with the `OPEN` status.
- The creator is recorded as the ticket requester.
- Ticket numbers must be unique.
- Ticket ownership must not be accepted from an untrusted client request without authorization and validation.

### Ticket Assignment Rules

- Only authorized support agents or administrators can assign tickets.
- An assignee must be a valid, eligible user.
- Assignment changes must be recorded in the ticket activity history.
- Unassigned tickets should remain visible to users authorized to manage the ticket queue.

### Ticket Update Rules

- Only authorized users can update ticket details.
- Employees must not modify another employee's ticket unless explicitly permitted by the system's authorization rules.
- Status changes must follow the allowed workflow.
- Priority changes must be authorized.
- Important changes must be recorded in the activity history.

### Comment Rules

- Comments must belong to a valid ticket.
- Only users authorized to access the ticket may view or add comments.
- Comments must be validated on the backend.
- The author and creation timestamp must be recorded.
- Comments should be treated as historical records rather than silently overwritten.

### Category Rules

- A ticket must reference a valid category.
- Inactive categories cannot be selected for new tickets.
- Existing tickets should retain their category history if a category becomes inactive.
- Categories referenced by existing tickets should not be deleted in a way that breaks historical records.

### Data Integrity Rules

- Use foreign keys and suitable database constraints.
- Use database transactions when multiple related changes must succeed together.
- Add indexes for frequently filtered or searched fields where appropriate.
- Use backend pagination for large collections.
- Avoid duplicating business rules between frontend and backend as the source of truth.

---

## 8. Ticket Workflow

The intended workflow is:

```text
Employee creates ticket
        |
        v
      OPEN
        |
        v
Support agent starts work
        |
        v
   IN_PROGRESS
        |
        v
      RESOLVED
        |
        v
       CLOSED
```

The exact allowed transitions should be implemented and validated by the backend.

For the initial MVP, keep the workflow simple. Do not introduce reopening, approval stages, escalation, or complicated automation until the main process works reliably.

Every important transition should create an activity record containing the actor, event type, relevant change, and timestamp.

---

## 9. Database Design

The initial database should include the following tables.

### 9.1 users

Purpose: Store employee, support agent, and administrator accounts.

Suggested fields:

- `id`
- `name`
- `email`
- `password`
- `role`
- `is_active`
- `email_verified_at`, if email verification is included
- `created_at`
- `updated_at`

Notes:

- Use Laravel's password hashing.
- Add a unique constraint to email.
- Use a native PHP 8.4 Backed Enum (`UserRole: string`) with cases: `ADMIN = 'admin'`, `SUPPORT_AGENT = 'support_agent'`, and `EMPLOYEE = 'employee'`.
- Enforce authorization strictly via Laravel Policies and Gates (no external RBAC package needed for MVP).

### 9.2 ticket_categories

Purpose: Categorize tickets.

Suggested fields:

- `id`
- `name`
- `description`, nullable
- `is_active`
- `created_at`
- `updated_at`

Notes:

- Category names should be unique if the business rules require it.
- Prefer deactivation over deletion when categories have historical usage.

### 9.3 tickets

Purpose: Store ticket details and current state.

Suggested fields:

- `id`
- `ticket_number`
- `requester_id`
- `assignee_id`, nullable
- `category_id`
- `subject`
- `description`
- `status`
- `priority`
- `resolution_notes`, nullable
- `resolved_at`, nullable
- `closed_at`, nullable
- `created_at`
- `updated_at`

Relationships:

- A ticket belongs to a requester (`users`).
- A ticket may belong to an assignee (`users`).
- A ticket belongs to a category (`ticket_categories`).
- A ticket has many comments.
- A ticket has many activity records.

Notes:

- Make `ticket_number` unique, generated using a formatted sequence prefix (e.g., `TCK-YYYYMM-XXXX`).
- Index fields frequently used for filtering, such as status, priority, requester, assignee, and category.
- Avoid storing values that can be reliably derived unless there is a clear reason.

### 9.4 ticket_comments

Purpose: Store conversation entries on tickets.

Suggested fields:

- `id`
- `ticket_id`
- `user_id`
- `body`
- `created_at`
- `updated_at`

Notes:

- Use foreign keys to maintain relationships.
- Keep comments associated with their author.
- If editing comments is permitted, decide whether to record edit history.

### 9.5 ticket_activities

Purpose: Maintain a timeline of important ticket changes.

Suggested fields:

- `id`
- `ticket_id`
- `user_id`, nullable for system-generated events
- `event_type`
- `old_value`, nullable
- `new_value`, nullable
- `description`, nullable
- `created_at`

Example activity types:

- `TICKET_CREATED`
- `STATUS_CHANGED`
- `PRIORITY_CHANGED`
- `ASSIGNEE_CHANGED`
- `COMMENT_ADDED`
- `TICKET_RESOLVED`
- `TICKET_CLOSED`

Notes:

- Use a consistent event naming convention (`TICKET_CREATED`, `STATUS_CHANGED`, `PRIORITY_CHANGED`, `ASSIGNEE_CHANGED`, `COMMENT_ADDED`, `TICKET_RESOLVED`, `TICKET_CLOSED`).
- Log events explicitly through dedicated methods in `TicketService` wrapped inside a `DB::transaction()` whenever ticket state changes.
- Store only the information needed to explain the event.
- Avoid storing sensitive information in activity descriptions.

### Database Relationship Summary

```text
users
  |-- tickets (requester)
  |-- tickets (assignee)
  |-- ticket_comments
  |-- ticket_activities

ticket_categories
  |-- tickets

tickets
  |-- ticket_comments
  |-- ticket_activities
```

Before creating migrations, review the relationships, nullability, foreign keys, indexes, and deletion behavior in an ERD.

Do not begin implementation until the database design is sufficiently clear for the MVP.

---

## 10. API Design

Use versioned REST API routes under `/api/v1` where appropriate.

### Authentication

| Method | Endpoint | Purpose |
|---|---|---|
| POST | `/api/v1/login` | Authenticate a user |
| POST | `/api/v1/logout` | Log out an authenticated user |
| GET | `/api/v1/me` | Return the authenticated user's information |

Sanctum API Tokens (Personal Access Tokens) are issued upon `/api/v1/login` and revoked upon `/api/v1/logout`. The frontend Axios instance automatically attaches the token via the `Authorization: Bearer <token>` header on all requests.

### Users

| Method | Endpoint | Purpose |
|---|---|---|
| GET | `/api/v1/users` | List users |
| POST | `/api/v1/users` | Create a user |
| GET | `/api/v1/users/{id}` | View a user |
| PUT/PATCH | `/api/v1/users/{id}` | Update a user |
| PATCH | `/api/v1/users/{id}/status` | Activate or deactivate a user |

All user-management routes must enforce administrator permissions.

### Ticket Categories

| Method | Endpoint | Purpose |
|---|---|---|
| GET | `/api/v1/ticket-categories` | List categories |
| POST | `/api/v1/ticket-categories` | Create a category |
| GET | `/api/v1/ticket-categories/{id}` | View a category |
| PUT/PATCH | `/api/v1/ticket-categories/{id}` | Update a category |
| PATCH | `/api/v1/ticket-categories/{id}/status` | Activate or deactivate a category |

### Tickets

| Method | Endpoint | Purpose |
|---|---|---|
| GET | `/api/v1/tickets` | List authorized tickets |
| POST | `/api/v1/tickets` | Create a ticket |
| GET | `/api/v1/tickets/{id}` | View an authorized ticket |
| PATCH | `/api/v1/tickets/{id}` | Update permitted ticket fields |
| PATCH | `/api/v1/tickets/{id}/assign` | Assign a ticket |
| PATCH | `/api/v1/tickets/{id}/status` | Change ticket status |
| PATCH | `/api/v1/tickets/{id}/priority` | Change ticket priority |

The backend must limit the returned tickets according to the authenticated user's permissions.

### Comments and Activity

| Method | Endpoint | Purpose |
|---|---|---|
| GET | `/api/v1/tickets/{id}/comments` | List ticket comments |
| POST | `/api/v1/tickets/{id}/comments` | Add a comment |
| GET | `/api/v1/tickets/{id}/activities` | View ticket activity history |

### Dashboard

| Method | Endpoint | Purpose |
|---|---|---|
| GET | `/api/v1/dashboard/summary` | Return role-appropriate dashboard counts |

### API Conventions

- Use Form Request classes for validation where appropriate.
- Use API Resources for consistent response serialization.
- Return appropriate HTTP status codes.
- Use a consistent JSON response structure.
- Return field-level validation errors in a frontend-friendly format.
- Use query parameters for pagination, search, filtering, and sorting.
- Validate allowed filter and sort fields instead of accepting arbitrary column names.
- Never rely on frontend route guards as the only authorization mechanism.
- Avoid returning password hashes or unnecessary internal fields.
- Document the API contract before implementing dependent frontend screens.

The endpoint list is a starting proposal. Review and finalize it alongside the business rules and ERD before implementation.

---

## 11. Frontend Pages

### Public Pages

- Login page.

### Shared Authenticated Layout

- Responsive sidebar.
- Top toolbar.
- Current user menu.
- Logout action.
- Main content area.

### Employee Pages

- Employee dashboard.
- My tickets list.
- Create ticket form.
- Ticket details page.
- Ticket comments and activity timeline.

### Support Agent Pages

- Support dashboard.
- Ticket queue.
- Ticket details and management.
- Assignment controls.
- Status and priority controls.
- Comments and activity timeline.

### Administrator Pages

- Administrator dashboard.
- User management.
- Create and edit user forms.
- Ticket category management.
- System-wide ticket list.

### Frontend Behavior

Every page that fetches API data should handle:

- Initial loading.
- Successful response.
- Empty data.
- Validation errors.
- Unauthorized or forbidden responses.
- Network and server errors.
- Expired or invalid authentication.

Do not treat a failed request as an empty successful response.

Use Vue Router for navigation and route guards where useful. The backend remains responsible for authorization.

Use Pinia when state needs to be shared across multiple components or pages. Avoid creating stores for simple local form state that can remain inside a component.

Use Axios through a shared configured instance rather than duplicating API configuration across every component.

---

## 12. Frontend Project Structure

A possible Vue 3 + Quasar structure:

```text
src/
├── assets/
├── boot/
│   └── axios.js
├── components/
│   ├── common/
│   └── tickets/
├── layouts/
│   └── MainLayout.vue
├── pages/
│   ├── auth/
│   │   └── LoginPage.vue
│   ├── dashboard/
│   │   └── DashboardPage.vue
│   ├── tickets/
│   │   ├── TicketListPage.vue
│   │   ├── TicketCreatePage.vue
│   │   └── TicketDetailsPage.vue
│   ├── users/
│   └── categories/
├── router/
├── stores/
│   ├── auth.js
│   └── tickets.js
├── services/
│   ├── authService.js
│   ├── ticketService.js
│   ├── userService.js
│   └── categoryService.js
├── utils/
└── App.vue
```

This is a suggested starting structure, not a requirement to create every folder immediately.

Adapt it to the actual Quasar project scaffold and add files only when they are needed.

### Component Responsibilities

- **Pages:** Coordinate page-level data loading and compose components.
- **Components:** Render reusable interface elements and handle focused UI interactions.
- **Services:** Encapsulate API requests.
- **Stores:** Manage shared client-side state when necessary.
- **Layouts:** Define shared application structure.
- **Router:** Define routes, layouts, and navigation guards.
- **Utils:** Hold genuinely reusable helper functions.

Keep business-critical rules in the backend. Do not duplicate complex backend logic in frontend services or stores.

---

## 13. Backend Project Structure

Use Laravel's standard structure and introduce additional layers only when they provide clear value.

Suggested organization:

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── V1/
│   ├── Requests/
│   └── Resources/
├── Models/
├── Policies/
└── Services/
```

### Responsibilities

- **Controllers:** Handle HTTP requests and coordinate the response.
- **Form Requests:** Validate incoming data and authorize requests when appropriate.
- **Services:** Contain business operations that require coordinated logic or transactions (e.g., `TicketService` coordinates ticket status transitions, assignments, and writes to `ticket_activities` within `DB::transaction()`).
- **Models:** Represent database entities and relationships.
- **Policies:** Centralize resource-level authorization.
- **API Resources:** Format API output consistently.

Do not create repositories or extra abstraction layers automatically. Start with a clear controller-service-model approach and introduce additional abstractions only when justified.

Use database transactions for operations that require multiple related database changes to succeed or fail together.

---

## 14. Security and Data Integrity

- Hash passwords using Laravel's supported mechanisms.
- Use Sanctum according to the selected authentication approach.
- Enforce authorization on the backend.
- Validate every incoming request.
- Prevent users from accessing tickets they are not authorized to view.
- Prevent mass assignment vulnerabilities through appropriate model configuration and validated input.
- Avoid exposing sensitive data in API responses and logs.
- Use CSRF protection when required by the chosen authentication method.
- Configure CORS for the actual frontend origin.
- Use foreign keys and database transactions where appropriate.
- Use environment variables for secrets and environment-specific settings.
- Never commit production credentials or `.env` secrets.
- Use pagination for collections.
- Add indexes based on actual query patterns.
- Review deletion behavior to preserve historical records.

---

## 15. Testing Strategy

### Backend Tests

Test at least the following:

- Login succeeds with valid credentials.
- Invalid credentials are rejected.
- Unauthenticated users cannot access protected endpoints.
- Users cannot access tickets outside their permissions.
- Employees can create tickets with valid input.
- Invalid ticket input returns validation errors.
- New tickets receive the correct initial status.
- Unauthorized users cannot change ticket status or priority.
- Ticket assignment validates the assignee.
- Ticket comments are visible only to authorized users.
- Important changes create activity records.
- Pagination and filtering behave correctly.
- Deactivated users cannot access protected operations.

### Frontend Tests

Where appropriate, test:

- Login form validation and feedback.
- Route protection.
- Ticket list loading and error states.
- Search and filter behavior.
- Ticket creation validation.
- Status and priority display.
- Permission-aware UI behavior.
- Confirmation dialogs for important actions.

Frontend tests do not replace backend authorization tests.

### Manual Testing

Use Postman to verify the API contract and authorization rules before integrating complex frontend workflows.

Test the complete ticket lifecycle using accounts for each role.

---

## 16. Development Plan

Build the system incrementally. Complete and understand each stage before moving to the next.

### Phase 1: Planning

- [ ] Finalize project scope.
- [ ] Confirm roles and permissions.
- [ ] Finalize ticket statuses and transitions.
- [ ] Define business rules.
- [ ] Create the ERD.
- [ ] Review database relationships and constraints.
- [ ] Define the API contract.
- [ ] Identify frontend pages and UI states.
- [ ] Create Trello tasks.

### Phase 2: Backend Foundation

- [ ] Create the Laravel API project.
- [ ] Configure the MySQL database.
- [ ] Configure Sanctum API Bearer Token authentication.
- [ ] Create migrations and models.
- [ ] Define relationships and database constraints.
- [ ] Seed development users and categories.
- [ ] Implement login, logout, and authenticated-user endpoints.
- [ ] Test authentication with Postman.

### Phase 3: Core Backend Features

- [ ] Implement ticket categories.
- [ ] Implement ticket creation and listing.
- [ ] Implement ticket details and updates.
- [ ] Implement assignment, status, and priority changes.
- [ ] Implement ticket comments.
- [ ] Implement ticket activity history.
- [ ] Implement user management.
- [ ] Implement dashboard summaries.
- [ ] Add validation, authorization, and automated tests.
- [ ] Document the API contract and actual responses.

### Phase 4: Frontend Foundation

- [ ] Create the Vue 3 + Quasar project.
- [ ] Configure the Axios instance and API base URL.
- [ ] Configure routing and the shared layout.
- [ ] Configure the Axios request interceptor for Sanctum Bearer tokens.
- [ ] Implement the login page.
- [ ] Implement authentication state.
- [ ] Add route guards.
- [ ] Create shared UI conventions for loading, errors, and notifications.

### Phase 5: Frontend Features

- [ ] Implement the employee ticket list.
- [ ] Implement ticket creation.
- [ ] Implement ticket details, comments, and activity history.
- [ ] Implement support agent ticket queue and management.
- [ ] Implement administrator user management.
- [ ] Implement category management.
- [ ] Implement dashboards.
- [ ] Add search, filtering, sorting, and pagination.
- [ ] Make layouts responsive.

### Phase 6: Integration and Review

- [ ] Test each role's complete workflow.
- [ ] Test authorization failures and invalid inputs.
- [ ] Test empty, loading, and error states.
- [ ] Check responsive layouts.
- [ ] Fix integration issues.
- [ ] Review code organization and duplicated logic.
- [ ] Write setup and usage documentation.
- [ ] Prepare the project for a portfolio demonstration.

A two-week target is a planning estimate, not a guarantee. Prioritize the core ticket lifecycle over optional features.

---

## 17. Documentation

Maintain the following documentation as the project evolves:

- `project-specification.md` — project scope, requirements, rules, and technology decisions.
- `docs/erd.md` — database relationships and design decisions.
- `docs/api-specification.md` — endpoints, parameters, response formats, and error cases.
- `README.md` — setup instructions, environment configuration, and how to run the project.
- `docs/technical-decisions.md` — important architecture decisions and their reasoning.

Keep documentation synchronized with the implementation. Update it when requirements or API behavior change.

---

## 18. Definition of Done

A feature is complete when:

- The acceptance criteria are met.
- Backend validation and authorization are implemented.
- API responses follow the documented conventions.
- Relevant automated tests pass.
- Frontend loading, success, empty, and error states are handled.
- The interface uses existing Quasar components where appropriate.
- The feature works at desktop and mobile viewport sizes.
- No unrelated or unnecessary abstractions have been introduced.
- Documentation is updated when needed.
- The developer can explain how the feature works and why the implementation was chosen.

---

## 19. Future Enhancements

Consider these only after the MVP is stable:

- Email notifications.
- File attachments.
- SLA tracking and escalation.
- Ticket reopening.
- Saved filters.
- Advanced reporting.
- Audit log improvements.
- Real-time updates.
- Knowledge base articles.
- Additional roles and permissions.
- Multiple organizations or tenants.

---

## 20. Development Principles for AI-Assisted Work

Use AI tools, including Antigravity, as a development assistant and learning partner.

### Before Implementing a Feature

- Read the relevant specification and existing code.
- Identify unclear requirements and ask questions before making assumptions.
- Propose a small implementation plan.
- Identify the files likely to change.
- Explain relevant architecture and design decisions.
- Check whether the feature already exists before creating duplicate code.

### During Implementation

- Work in small, reviewable steps.
- Follow existing project conventions unless there is a good reason to change them.
- Prefer straightforward solutions over unnecessary abstractions.
- Reuse Quasar components when they fit the requirement.
- Follow Laravel and Vue 3 best practices.
- Avoid introducing additional dependencies without a clear need.
- Keep frontend and backend responsibilities clear.
- Do not silently change requirements or API contracts.

### After Implementation

- Explain what changed and why.
- Identify important code paths and responsibilities.
- Explain trade-offs and any unfamiliar syntax.
- Run relevant tests and report their actual results.
- Identify remaining risks or incomplete work.
- Suggest what the developer should review or learn next.

### Learning Principle

The objective is not simply to generate a working application. The objective is to build a working application while understanding its architecture, business rules, data flow, and implementation decisions.

The developer should review and understand AI-generated changes before accepting them.
