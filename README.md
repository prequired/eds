# Edison Tech Platform

**Version:** 1.0 (Phase 1 Complete - Phase 2 In Progress)
**Status:** 🚧 In Development
**Specification:** S-Tier - Board Approved

---

## 🎯 Project Overview

Edison Tech Platform is a comprehensive **multi-tenant SaaS application** designed for web agencies to manage clients, projects, websites, deployments, and billing. Built with a Montana-first approach, this platform enables agencies to streamline their operations and scale their business.

### Key Features
- ✅ **Multi-Tenant Architecture** (Database-per-tenant isolation)
- ✅ **Client Management** (Complete CRUD with UI)
- ✅ **Project Management** (Complete CRUD with UI)
- ✅ **Website Management** (Complete CRUD with monitoring-ready UI)
- 🚧 **Website Monitoring** (Uptime, performance, Lighthouse scores)
- 🚧 **Deployment Integration** (Laravel Forge, GitHub Actions)
- 🚧 **Automated Invoicing**
- 🚧 **Client Portal**
- 🚧 **Team Collaboration**
- 🚧 **White-Label Capabilities**

---

## 🏗️ Architecture

### Tech Stack
- **Framework:** Laravel 11.46.1
- **PHP:** 8.4.14
- **Database:** PostgreSQL 16+
- **Cache/Queue:** Redis 7+
- **Frontend:** Livewire 3.6.4
- **Testing:** Pest 3.8.4
- **Multi-Tenancy:** stancl/tenancy 3.9.1

### Multi-Tenancy Strategy
- **Isolation:** Database-per-tenant (complete data separation)
- **Identification:** Subdomain-based (e.g., \`acme.edisontech.test\`)
- **Central Database:** Stores tenants, domains, and users
- **Tenant Databases:** Separate database for each agency (clients, projects, websites)

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
- [x] \`Client\` - Client management with billing info
- [x] \`Project\` - Project tracking with budget/timeline
- [x] \`Website\` - Website monitoring & deployment

### ✅ Database Migrations

**Central:** tenants, domains, users  
**Tenant:** clients, projects, websites

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
- [x] Central application routes (\`routes/web.php\`)
- [x] Tenant application routes (\`routes/tenant.php\`) with full Livewire integration:
  - \`/clients\`, \`/clients/create\`, \`/clients/{client}/edit\`
  - \`/projects\`, \`/projects/create\`, \`/projects/{project}/edit\`
  - \`/websites\`, \`/websites/create\`, \`/websites/{website}/edit\`

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
- [x] Full test coverage (17 tests passing)

### Phase 2: Monitoring & Integration (Weeks 9-12) 🚧 IN PROGRESS
- [ ] Uptime monitoring (cron jobs, webhooks)
- [ ] Lighthouse performance scoring integration
- [ ] Deployment integration (Laravel Forge API)
- [ ] GitHub Actions integration
- [ ] Response time tracking
- [ ] SSL certificate monitoring
- [ ] DNS health checks

### Phase 3: Invoicing & Billing (Weeks 13-16)
- [ ] Invoice generation
- [ ] Stripe integration
- [ ] Recurring billing
- [ ] Payment tracking
- [ ] Client portal for invoices

### Phase 4: Advanced Features (Weeks 17-20)
- [ ] Team collaboration
- [ ] White-label capabilities
- [ ] Advanced reporting
- [ ] API access

---

## 📄 License

Proprietary. All rights reserved.

---

**Built with ❤️ for Montana agencies.**
