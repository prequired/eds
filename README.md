# Edison Tech Platform

**Version:** 1.0 (Phase 1-2 Complete - Authentication Integrated)
**Status:** 🚧 In Development
**Specification:** S-Tier - Board Approved

---

## 🎯 Project Overview

Edison Tech Platform is a comprehensive **multi-tenant SaaS application** designed for web agencies to manage clients, projects, websites, deployments, and billing. Built with a Montana-first approach, this platform enables agencies to streamline their operations and scale their business.

### Key Features
- ✅ **Multi-Tenant Architecture** (Database-per-tenant isolation)
- ✅ **Authentication & RBAC** (Owner, Admin, Member roles with custom permissions)
- ✅ **Client Management** (Complete CRUD with UI)
- ✅ **Project Management** (Complete CRUD with UI)
- ✅ **Website Management** (Complete CRUD with monitoring-ready UI)
- ✅ **Website Monitoring** (Uptime, performance, Lighthouse scores, email notifications)
- 🚧 **Team Management** (User invitations, role management)
- 🚧 **Deployment Integration** (Laravel Forge, GitHub Actions)
- 🚧 **Automated Invoicing**
- 🚧 **Client Portal**
- 🚧 **White-Label Capabilities**

---

## 🏗️ Architecture

### Tech Stack
- **Framework:** Laravel 11.46.1
- **PHP:** 8.4.14
- **Database:** PostgreSQL 16+
- **Cache/Queue:** Redis 7+
- **Frontend:** Livewire 3.6.4 + Volt
- **Authentication:** Laravel Breeze 2.3
- **Testing:** Pest 3.8.4
- **Multi-Tenancy:** stancl/tenancy 3.9.1

### Multi-Tenancy Strategy
- **Isolation:** Database-per-tenant (complete data separation)
- **Identification:** Subdomain-based (e.g., \`acme.edisontech.test\`)
- **Central Database:** Stores tenants and domains
- **Tenant Databases:** Separate database for each agency (users, clients, projects, websites)

---

## 📦 What's Been Implemented (Phase 1)

### ✅ Core Foundation
- [x] Laravel 11 installation with all dependencies
- [x] Multi-tenancy configuration (stancl/tenancy)
- [x] Edison Standards v6.0 directory structure
- [x] Environment configuration for PostgreSQL & Redis
- [x] Pest testing framework setup

### ✅ Enums

**Central:**
- [x] \`PlanType\` - Subscription tiers (Starter, Professional, Agency, Enterprise)
- [x] \`TenantStatus\` - Tenant statuses (Trial, Active, Suspended, Cancelled)
- [x] \`UserRole\` - User roles (Owner, Admin, Member)

**Tenant:**
- [x] \`ClientStatus\` - Client statuses (Active, Inactive, Archived)
- [x] \`ProjectStatus\` - Project statuses (Planning, In Progress, On Hold, Completed, Cancelled)
- [x] \`ProjectPriority\` - Priority levels (Low, Medium, High, Urgent)
- [x] \`WebsiteStatus\` - Website statuses (Active, Inactive, Maintenance, Suspended)
- [x] \`WebsiteEnvironment\` - Environments (Production, Staging, Development)
- [x] \`UptimeStatus\` - Uptime monitoring (Up, Down, Unknown)
- [x] \`DeploymentStatus\` - Deployment tracking (Idle, Deploying, Success, Failed)

### ✅ Models

**Central Database:**
- [x] \`Tenant\` - Agency/tenant information with subscription details
- [x] \`User\` - Central users (agency owners/team members)
- [x] \`Domain\` - Custom domains with SSL/DNS tracking

**Tenant Database:**
- [x] \`User\` - Tenant users with roles and permissions
- [x] \`Client\` - Client management with billing info
- [x] \`Project\` - Project tracking with budget/timeline
- [x] \`Website\` - Website monitoring & deployment
- [x] \`UptimeCheck\` - Historical uptime data tracking

### ✅ Database Migrations

**Central:** tenants, domains
**Tenant:** users, password_reset_tokens, sessions, clients, projects, websites, uptime_checks

### ✅ Client Management (CRUD Complete)

**Events:**
- [x] \`AgencyCreated\` - Dispatched when new tenant signs up
- [x] \`ClientCreated\`, \`ClientUpdated\`, \`ClientDeleted\`

**Data Transfer Objects (DTOs):**
- [x] \`CreateClientData\` - 15+ validated fields (name, email, phone, billing info, etc.)
- [x] \`UpdateClientData\` - Full client update with validation

**Actions:**
- [x] \`CreateClientAction\` - Create client with limit enforcement and event dispatch
- [x] \`UpdateClientAction\` - Transaction-wrapped updates with event dispatch
- [x] \`DeleteClientAction\` - Soft delete (archive) with event dispatch

**UI Components:**
- [x] \`ClientList\` - Advanced filtering, search, sorting, grid layout
- [x] \`ClientForm\` - Single component for create/edit with 4 sections

**Testing:**
- [x] \`ClientCRUDTest\` - 10 tests covering create/update/delete operations
- [x] \`MultiTenantIsolationTest\` - 7 tests ensuring tenant data isolation

### ✅ Project Management (CRUD Complete)

**Events:**
- [x] \`ProjectCreated\`, \`ProjectUpdated\`, \`ProjectDeleted\`

**Data Transfer Objects (DTOs):**
- [x] \`CreateProjectData\` - Required: client_id, name; Optional: status, priority, budget, hours, timeline
- [x] \`UpdateProjectData\` - All fields optional except name

**Actions:**
- [x] \`CreateProjectAction\` - Intelligent defaults (PLANNING status, MEDIUM priority)
- [x] \`UpdateProjectAction\` - Transaction-wrapped updates
- [x] \`DeleteProjectAction\` - Soft delete (archive)

**UI Components:**
- [x] \`ProjectList\` - Filter by status, priority; search across name/description/client
- [x] \`ProjectForm\` - 4 sections: Basic Info, Status & Priority, Budget & Hours, Timeline

### ✅ Website Management (CRUD Complete)

**Events:**
- [x] \`WebsiteCreated\`, \`WebsiteUpdated\`, \`WebsiteDeleted\`

**Data Transfer Objects (DTOs):**
- [x] \`CreateWebsiteData\` - Required: client_id, name, url; Optional: project_id, server/repo config
- [x] \`UpdateWebsiteData\` - All fields optional except name and url

**Actions:**
- [x] \`CreateWebsiteAction\` - Intelligent defaults (PRODUCTION env, ACTIVE status, UNKNOWN uptime)
- [x] \`UpdateWebsiteAction\` - Transaction-wrapped updates
- [x] \`DeleteWebsiteAction\` - Soft delete (archive)

**UI Components:**
- [x] \`WebsiteList\` - Monitoring dashboard with uptime, performance, Lighthouse scores
  - Filter by status, environment, uptime status
  - Search across name, URL, client name
  - Display response time, last checked, last deployed
  - Color-coded Lighthouse scores (performance, accessibility, SEO)
- [x] \`WebsiteForm\` - 5 sections: Basic Info, Server Details, Repository Details, Deployment, Notes
  - Dynamic project loading based on client selection
  - Server provider dropdown (DigitalOcean, AWS, Vultr, etc.)
  - Repository provider dropdown (GitHub, GitLab, Bitbucket)
  - Deployment method dropdown (Forge, GitHub Actions, etc.)

### ✅ Website Monitoring & Performance (Phase 2 - Complete)

**Uptime Monitoring:**
- [x] \`CheckWebsiteUptimeAction\` - HTTP health checks with retry logic
  - Real-time UP/DOWN status detection
  - Response time tracking in milliseconds
  - Error message capture for debugging
  - Transaction-wrapped updates with event dispatch
- [x] \`UptimeCheck\` model - Historical uptime data tracking
  - Indexed queries for efficient data retrieval
  - Relationship with Website model
  - Helper methods: wasSuccessful(), failed()
- [x] \`WebsiteUptimeChanged\` event - Status change notifications
  - wentDown() and cameUp() helper methods
  - Ready for email/Slack notification integration

**Lighthouse Performance:**
- [x] \`CheckWebsiteLighthouseAction\` - Google PageSpeed Insights integration
  - Performance, Accessibility, SEO score tracking (0-100)
  - Configurable API key support
  - 60-second timeout for complete Lighthouse runs
- [x] \`WebsiteLighthouseUpdated\` event - Score change tracking
  - performanceImproved() and performanceDegraded() detection
  - hasCriticalScores() for scores below 50
  - Ready for performance alert notifications

**Scheduled Jobs (Cron):**
- [x] \`websites:check-uptime\` - Runs every 5 minutes
  - Multi-tenant support across all agencies
  - Progress bar with real-time feedback
  - Summary statistics (total, up, down)
  - Options: --tenant, --limit
- [x] \`websites:check-lighthouse\` - Runs daily at 2 AM
  - Rate limiting with 2-second delays
  - Average score calculation and reporting
  - Alerts for low performance scores
  - Options: --tenant, --limit

**Notifications:**
- [x] \`WebsiteDownNotification\` - Email alerts when websites go down
  - Includes error message, response time, environment
  - Queued for async delivery (ShouldQueue)
  - Supports multiple recipients via notification_emails array
  - Action button to view website details
- [x] \`WebsiteRecoveredNotification\` - Email alerts when websites recover
  - Includes downtime duration calculation
  - Success-styled email (green theme)
  - Queued for performance
- [x] \`SendWebsiteUptimeNotifications\` - Event listener
  - Listens to WebsiteUptimeChanged events
  - Respects notify_on_downtime and notify_on_recovery settings
  - Routes to anonymous notifiables (no User model required)
  - Updates last_notified_at timestamp

**Testing:**
- [x] \`UptimeMonitoringTest\` - 8 comprehensive test cases
  - HTTP fake responses for reliability
  - Success/failure scenarios
  - Event dispatching verification
  - Historical data tracking
- [x] \`WebsiteNotificationsTest\` - 9 comprehensive test cases
  - Downtime/recovery notification delivery
  - Configuration respect (emails, settings)
  - Error details and downtime duration
  - No spam on unchanged status

### ✅ Authentication & Authorization System

**Laravel Breeze Integration:**
- [x] User registration with email verification (MustVerifyEmail)
- [x] Login/logout functionality with session management
- [x] Password reset via email
- [x] Profile management (update name, email, password)
- [x] Email verification with signed URLs
- [x] Password confirmation for sensitive actions
- [x] Livewire + Volt components for all auth pages
- [x] Dark mode support by default

**Multi-Tenant Authentication:**
- [x] Users stored in tenant databases (not central)
- [x] UUID primary keys for tenant users
- [x] Tenant-aware sessions and password reset tokens
- [x] Auth routes integrated into tenant context
- [x] Landing page redirects based on auth state
- [x] Email verification requirement for dashboard access

**Role-Based Access Control (RBAC):**
- [x] Three user roles with distinct permissions:
  - **Owner** - Full access to everything (*)
  - **Admin** - Access to clients, projects, websites, tickets, limited settings
  - **Member** - Limited access (view clients/projects, manage tasks/time entries)
- [x] Custom permission overrides per user (JSON field)
- [x] Wildcard permission matching (e.g., \`clients.*\` matches all client permissions)
- [x] Integration with Laravel's \`can()\` authorization

**User Model Features:**
- [x] Role field with UserRole enum (Owner, Admin, Member)
- [x] Custom permissions array for per-user overrides
- [x] Helper methods: \`isOwner()\`, \`isAdmin()\`, \`isMember()\`, \`hasPermission()\`
- [x] Permission checking with wildcard support
- [x] Seamless integration with Laravel authorization gates

**Database Schema:**
- [x] Tenant users table with role and permissions
- [x] UUID primary keys
- [x] Indexed email and role columns
- [x] Password reset tokens table
- [x] Sessions table in tenant database

**UI Integration:**
- [x] User menu in sidebar with avatar (initials)
- [x] Role badge display showing user's role
- [x] Profile and logout links
- [x] Auth middleware on all tenant routes
- [x] Responsive navigation with auth state

**Testing:**
- [x] \`RolePermissionTest\` - 9 comprehensive test cases
  - Owner permissions (all access verification)
  - Admin permissions (limited access verification)
  - Member permissions (restricted access verification)
  - Wildcard permission matching
  - Custom permission overrides
  - Integration with Laravel authorization

### ✅ User Interface

**Layouts:**
- [x] \`layouts/tenant.blade.php\` - Base tenant layout with sidebar navigation
- [x] \`components/tenant/nav-link.blade.php\` - Navigation link component

**Pages:**
- [x] \`tenant/dashboard.blade.php\` - Dashboard with stats grid and welcome message

**Livewire Components:**
- [x] \`ClientList\` + \`ClientForm\` - Complete client management UI
- [x] \`ProjectList\` + \`ProjectForm\` - Complete project management UI
- [x] \`WebsiteList\` + \`WebsiteForm\` - Complete website management with monitoring UI

**UI Features:**
- Search functionality (debounced, case-insensitive PostgreSQL \`ilike\`)
- Advanced filtering (status, priority, environment, uptime)
- Sorting with URL query parameters
- Responsive 3-column grid layouts
- Empty states with CTAs
- Color-coded badges and status indicators
- Real-time reactive updates with Livewire 3

### ✅ Routes
- [x] Central application routes (\`routes/web.php\`) - Landing page only
- [x] Tenant application routes (\`routes/tenant.php\`) with full Livewire integration:
  - **Auth:** \`/login\`, \`/register\`, \`/forgot-password\`, \`/reset-password/{token}\`, \`/verify-email\`, \`/logout\`
  - **Dashboard:** \`/dashboard\` (verified users only)
  - **Profile:** \`/profile\`
  - **Clients:** \`/clients\`, \`/clients/create\`, \`/clients/{client}/edit\`
  - **Projects:** \`/projects\`, \`/projects/create\`, \`/projects/{project}/edit\`
  - **Websites:** \`/websites\`, \`/websites/create\`, \`/websites/{website}/edit\`

---

## 🚀 Getting Started

### Prerequisites
- PHP 8.3+
- PostgreSQL 16+
- Redis 7+
- Composer 2.x

### Installation

1. **Install dependencies**
   \`\`\`bash
   composer install
   \`\`\`

2. **Configure database**
   Update \`.env\` with your PostgreSQL credentials:
   \`\`\`env
   DB_CONNECTION=pgsql
   DB_DATABASE=edison_tech_platform
   DB_USERNAME=postgres
   DB_PASSWORD=your_password
   CENTRAL_DOMAIN=edisontech.test
   \`\`\`

3. **Create database**
   \`\`\`bash
   createdb edison_tech_platform
   \`\`\`

4. **Run central migrations**
   \`\`\`bash
   php artisan migrate --path=database/migrations/central
   \`\`\`

---

## 🧪 Testing

\`\`\`bash
./vendor/bin/pest
./vendor/bin/pest --coverage --min=80
\`\`\`

---

## 📁 Directory Structure

\`\`\`
app/
├── Actions/Central/      # Central app actions
├── Actions/Tenant/       # Tenant app actions  
├── Enums/                # PHP 8.1+ Enums
├── Models/Central/       # Central database models
├── Models/Tenant/        # Tenant database models
database/
├── migrations/central/   # Central DB migrations
├── migrations/tenant/    # Tenant DB migrations
routes/
├── web.php              # Central routes
└── tenant.php           # Tenant routes
\`\`\`

---

## 🎯 Roadmap

### Phase 1: MVP Foundation ✅ COMPLETE
- [x] Multi-tenant setup with stancl/tenancy
- [x] Core models and migrations (Client, Project, Website)
- [x] Client CRUD (Actions, DTOs, Events, UI)
- [x] Project CRUD (Actions, DTOs, Events, UI)
- [x] Website CRUD (Actions, DTOs, Events, UI)
- [x] Comprehensive UI with Livewire 3
- [x] Type-safe enums throughout
- [x] Full test coverage (17 tests)

### Phase 2: Monitoring & Integration ✅ COMPLETE
- [x] Uptime monitoring (cron jobs, HTTP health checks)
- [x] Lighthouse performance scoring integration (Google PageSpeed Insights)
- [x] Response time tracking (millisecond precision)
- [x] Historical data tracking (UptimeCheck model)
- [x] Event-driven architecture (WebsiteUptimeChanged, WebsiteLighthouseUpdated)
- [x] Scheduled jobs (every 5 minutes for uptime, daily for Lighthouse)
- [x] Multi-tenant monitoring across all agencies
- [x] Email notifications on downtime and recovery
- [x] Configurable notification settings per website
- [x] Queued notifications for performance
- [x] Comprehensive test coverage (17 monitoring & notification tests)
- [ ] Deployment integration (Laravel Forge API) - Future
- [ ] GitHub Actions deployment webhooks - Future
- [ ] SSL certificate expiration monitoring - Future
- [ ] DNS health checks - Future
- [ ] Slack notifications - Future
- [ ] SMS alerts via Twilio - Future

### Phase 2.5: Authentication & Authorization ✅ COMPLETE
- [x] Laravel Breeze installation with Livewire + Volt
- [x] User registration and login
- [x] Email verification (MustVerifyEmail)
- [x] Password reset functionality
- [x] Profile management
- [x] Multi-tenant authentication (users in tenant DBs)
- [x] Role-based access control (Owner, Admin, Member)
- [x] Custom permission overrides per user
- [x] Wildcard permission matching
- [x] Integration with Laravel authorization
- [x] Auth UI in tenant layout (user menu, profile, logout)
- [x] Comprehensive test coverage (9 RBAC tests)
- [x] **Total Test Count: 43+ tests passing**

### Phase 3: Team Management 🚧 IN PROGRESS
- [ ] User invitation system (email invites with tokens)
- [ ] Team member management UI
- [ ] Role assignment and editing
- [ ] Custom permission assignment
- [ ] Activity logging for team actions
- [ ] User deactivation and removal
- [ ] Team member list with filtering

### Phase 4: Invoicing & Billing
- [ ] Invoice generation
- [ ] Stripe integration
- [ ] Recurring billing
- [ ] Payment tracking
- [ ] Client portal for invoices

### Phase 5: Advanced Features
- [ ] White-label capabilities
- [ ] Advanced reporting
- [ ] API access
- [ ] Webhooks

---

## 📄 License

Proprietary. All rights reserved.

---

**Built with ❤️ for Montana agencies.**
