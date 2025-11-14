# Edison Tech Platform

**Version:** 1.0 (Phase 1 Foundation)  
**Status:** 🚧 In Development  
**Specification:** S-Tier - Board Approved

---

## 🎯 Project Overview

Edison Tech Platform is a comprehensive **multi-tenant SaaS application** designed for web agencies to manage clients, projects, websites, deployments, and billing. Built with a Montana-first approach, this platform enables agencies to streamline their operations and scale their business.

### Key Features (Planned)
- ✅ **Multi-Tenant Architecture** (Database-per-tenant isolation)
- ✅ **Client & Project Management**
- ✅ **Website Monitoring & Deployment**
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
- [x] \`PlanType\` - Subscription tiers (Starter, Professional, Agency, Enterprise)
- [x] \`TenantStatus\` - Tenant statuses (Trial, Active, Suspended, Cancelled)
- [x] \`UserRole\` - User roles (Owner, Admin, Member)
- [x] \`ClientStatus\` - Client statuses (Active, Inactive, Archived)

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

### ✅ Routes
- [x] Central application routes (\`routes/web.php\`)
- [x] Tenant application routes (\`routes/tenant.php\`)

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

### Phase 1: MVP Foundation (Weeks 1-8) ✅ IN PROGRESS
- [x] Multi-tenant setup
- [x] Core models and migrations
- [ ] Basic authentication
- [ ] Client CRUD
- [ ] Project CRUD

### Phase 2: Core Features (Weeks 9-12)
- [ ] Deployment integration
- [ ] Uptime monitoring
- [ ] Basic invoicing
- [ ] Client portal

---

## 📄 License

Proprietary. All rights reserved.

---

**Built with ❤️ for Montana agencies.**
