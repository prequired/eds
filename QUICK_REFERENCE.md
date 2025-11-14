# Edison Tech Platform - Quick Reference Guide

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                   PRESENTATION LAYER                         │
├─────────────────────────────────────────────────────────────┤
│  Livewire Components (19 components)                         │
│  ├─ ClientList/Form      ├─ InvoiceList/Form/View          │
│  ├─ ProjectList/Form     ├─ TimeTracker/List/Reports       │
│  ├─ WebsiteList/Form     ├─ ExpenseForm/List/Reports       │
│  ├─ TeamList/InviteMember├─ Dashboard/NotificationCenter   │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                  BUSINESS LOGIC LAYER                        │
├─────────────────────────────────────────────────────────────┤
│  Action Classes (31 actions)                                 │
│  ├─ CreateXyzAction      → Database Transaction             │
│  ├─ UpdateXyzAction      → Validation & Persistence         │
│  └─ DeleteXyzAction      → Soft Delete                      │
│                                                              │
│  Data Transfer Objects (DTO)                                 │
│  └─ CreateXyzData, UpdateXyzData → Type-safe data passing  │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                    DATA ACCESS LAYER                         │
├─────────────────────────────────────────────────────────────┤
│  Eloquent Models (11 tenant models)                         │
│  ├─ Client         ├─ Invoice/InvoiceItem/Payment          │
│  ├─ Project        ├─ TimeEntry                            │
│  ├─ Website        ├─ Expense                              │
│  ├─ UptimeCheck    └─ TeamInvitation                       │
│                                                              │
│  Database Scopes & Relationships                            │
│  └─ Query optimization, eager loading, soft deletes        │
└─────────────────────────────────────────────────────────────┘
```

---

## Feature Coverage Map

### Fully Implemented ✓
```
CLIENTS
├─ Create, Read, Update, Delete
├─ Contact information (name, email, phone, company)
├─ Billing information (billing email, address)
├─ Monthly retainer tracking
└─ Tags & custom notes

PROJECTS
├─ Create, Read, Update, Delete
├─ Status (active, inactive, archived)
├─ Priority levels (low, medium, high, urgent)
├─ Budget tracking (in cents)
├─ Estimated vs. actual hours
├─ Start/due dates
└─ Hourly rate per project

INVOICES
├─ Auto-generated invoice numbers (INV-YYYYMM-XXXX)
├─ Client association
├─ Line items with quantities & unit prices
├─ Real-time calculations (subtotal, tax, total)
├─ Payment tracking (multiple payments per invoice)
├─ Status workflow (Draft → Sent → Viewed → Paid/Overdue)
├─ Overdue detection
├─ Notes, terms, footer
└─ Send capability

TIME TRACKING
├─ Real-time timer (start/stop)
├─ Manual time entry creation
├─ Project & client association
├─ Billable flag & hourly rate
├─ Duration calculation (auto)
├─ Status tracking
├─ Invoice linking
├─ Time Reports (5 view types)
└─ CSV export

EXPENSES
├─ Category classification (12 types)
├─ Receipt upload (JPG, PNG, PDF; 5MB)
├─ Approval workflow (Draft → Submitted → Approved/Rejected)
├─ Reimbursement tracking
├─ Billable flag
├─ Project & client association
├─ Expense Reports (6 view types)
└─ CSV export

WEBSITES
├─ Uptime monitoring (UP, DOWN, DEGRADED)
├─ Response time tracking
├─ Deployment tracking (method, status, last deployed)
├─ Lighthouse scores (performance, accessibility, SEO)
├─ Environment classification (dev, staging, prod)
├─ Custom notifications on downtime
└─ Historical uptime data

DASHBOARD
├─ 4 quick stat cards (revenue, expenses, hours, projects)
├─ Trend indicators (MoM comparison)
├─ Recent invoices/expenses (last 5)
├─ Top projects & clients by metrics
├─ Team activity overview
└─ 7-month revenue & expense charts

TEAM
├─ Team invitations via email
├─ Role assignment (Owner, Admin, Manager, Employee)
├─ Permission checks
└─ Invitation management
```

### Partially Implemented ⚠
```
REPORTING
├─ Time & Expense reports ✓
├─ CSV export ✓
├─ Multi-filter capability ✓
└─ Missing: Advanced analytics, forecasting, financial reports

PERMISSIONS
├─ Basic role-based checks ✓
├─ Status-based permissions ✓
└─ Missing: Granular permission matrix, custom roles

NOTIFICATIONS
├─ Toast notifications ✓
├─ Notification center ✓
└─ Missing: Email alerts, SMS, push notifications
```

### Not Implemented ✗
```
REVENUE CRITICAL
├─ Client Portal (view invoices, pay online)
├─ Payment Gateway Integration (Stripe, PayPal)
├─ Quotes/Estimates (create, convert to invoice)
└─ Recurring Invoices (auto-generation on schedule)

ACCOUNTING
├─ General Ledger
├─ Journal Entries
├─ Financial Statements (P&L, Balance Sheet)
├─ Tax Reporting (beyond simple %)
├─ Accounts Payable (vendor management)
└─ Bank Reconciliation

INTEGRATIONS
├─ Third-party APIs (QuickBooks, Xero, Salesforce)
├─ Payment Processors (built-in)
├─ Calendar Integration
└─ Email/SMS gateways

OPERATIONAL
├─ Audit Logging
├─ Bulk Operations
├─ Templates (invoice, expense, email)
├─ Workflow Automation
├─ Duplicate Detection
└─ Data Export (beyond CSV)
```

---

## Model Relationships

```
Client (1) ────┬──→ (N) Project ────┬──→ (N) Website
               ├──→ (N) Website     ├──→ (N) TimeEntry
               ├──→ (N) Invoice     └──→ (N) Expense
               ├──→ (N) TimeEntry
               └──→ (N) Expense

Project ───────┬──→ (N) Website
               ├──→ (N) TimeEntry
               └──→ (N) Expense

Invoice ───────┬──→ (N) InvoiceItem
               ├──→ (N) Payment
               ├──→ (N) TimeEntry (optional)
               └──→ (N) Expense (optional)

TimeEntry ─────┬──→ (1) User
               ├──→ (1) Project (optional)
               ├──→ (1) Client (optional)
               └──→ (1) Invoice (optional)

Expense ───────┬──→ (1) User
               ├──→ (1) Project (optional)
               ├──→ (1) Client (optional)
               ├──→ (1) Invoice (optional)
               └──→ (1) User (approvedBy)

Website ───────┬──→ (N) UptimeCheck
               ├──→ (1) Project (optional)
               └──→ (1) Client (optional)
```

---

## Database Tables (Tenant Database)

| Table | Purpose | Key Fields |
|-------|---------|-----------|
| clients | Client master data | name, email, company, currency, monthly_retainer |
| projects | Project tracking | name, client_id, status, priority, budget, hourly_rate |
| websites | Website monitoring | name, url, status, deployment_status, uptime_status |
| uptime_checks | Historical uptime | website_id, status, response_time_ms, checked_at |
| invoices | Invoice master | invoice_number, client_id, status, issue_date, due_date |
| invoice_items | Invoice line items | invoice_id, description, quantity, unit_price |
| payments | Invoice payments | invoice_id, amount, payment_method, payment_date |
| time_entries | Time tracking | user_id, project_id, start_time, end_time, duration |
| expenses | Expense tracking | user_id, category, amount, status, receipt_path |
| team_invitations | Team member invites | email, token, role, accepted_at |

---

## Enum Types (Status & Classification)

| Enum | Values | Purpose |
|------|--------|---------|
| InvoiceStatus | Draft, Sent, Viewed, Paid, Partially Paid, Overdue, Cancelled | Invoice workflow |
| ExpenseStatus | Draft, Submitted, Approved, Rejected, Reimbursed, Invoiced | Expense approval workflow |
| ProjectStatus | Active, Inactive, Archived | Project state |
| ProjectPriority | Low, Medium, High, Urgent | Project priority |
| ClientStatus | Active, Inactive, Archived | Client state |
| TimeEntryStatus | Draft, Submitted, Approved, Rejected, Invoiced | Time entry state |
| ExpenseCategory | Travel, Meals, Software, Hardware, etc. (12 types) | Expense categorization |
| WebsiteStatus | Active, Inactive, Archived | Website state |
| WebsiteEnvironment | Development, Staging, Production | Website environment |
| UptimeStatus | UP, DOWN, DEGRADED | Website uptime state |
| DeploymentStatus | Pending, In Progress, Success, Failed | Deployment state |
| UserRole | Owner, Admin, Manager, Employee | Team role |
| PaymentMethod | Cash, Check, Credit Card, Bank Transfer, PayPal | Payment method |

---

## File Structure

```
app/
├─ Actions/Tenant/
│  ├─ Clients/          (3 files)
│  ├─ Projects/         (3 files)
│  ├─ Websites/         (5 files)
│  ├─ Invoice/          (5 files)
│  ├─ TimeEntry/        (5 files)
│  ├─ Expense/          (7 files)
│  └─ Team/             (3 files)
│
├─ Livewire/Tenant/
│  ├─ Clients/          (2 components)
│  ├─ Projects/         (2 components)
│  ├─ Websites/         (2 components)
│  ├─ Invoice/          (3 components)
│  ├─ TimeEntry/        (3 components)
│  ├─ Expense/          (3 components)
│  ├─ Team/             (2 components)
│  └─ Dashboard.php, NotificationCenter.php, NotificationList.php
│
├─ Models/Tenant/       (11 models)
├─ Enums/               (15 status enums)
├─ Data/Tenant/         (DTO classes)
└─ Notifications/       (notification classes)

resources/views/
└─ livewire/tenant/
   ├─ clients/           (2 blade files)
   ├─ projects/          (2 blade files)
   ├─ websites/          (2 blade files)
   ├─ invoice/           (3 blade files)
   ├─ time-entry/        (3 blade files)
   ├─ expense/           (3 blade files)
   ├─ team/              (2 blade files)
   ├─ dashboard.blade.php
   └─ notification-*.blade.php
```

---

## Development Patterns

### Adding a New Feature (e.g., Quotes)

1. **Create Model:**
   ```php
   // app/Models/Tenant/Quote.php
   class Quote extends Model {
       protected $fillable = [...];
       public function client() { ... }
       public function items() { ... }
   }
   ```

2. **Create DTO:**
   ```php
   // app/Data/Tenant/Quote/CreateQuoteData.php
   class CreateQuoteData extends Data {
       public function __construct(...) {}
   }
   ```

3. **Create Actions:**
   ```php
   // app/Actions/Tenant/Quote/CreateQuoteAction.php
   class CreateQuoteAction {
       public function __invoke(CreateQuoteData $data): Quote {
           return DB::transaction(function () use ($data) { ... });
       }
   }
   ```

4. **Create Livewire Component:**
   ```php
   // app/Livewire/Tenant/Quote/QuoteForm.php
   class QuoteForm extends Component {
       #[Validate(...)] public $fields;
       public function save() { ... }
   }
   ```

5. **Create View:**
   ```blade
   {{-- resources/views/livewire/tenant/quote/quote-form.blade.php --}}
   <form wire:submit.prevent="save">
       ...
   </form>
   ```

6. **Add Route:**
   ```php
   // routes/tenant.php
   Route::get('/quotes', QuoteList::class)->name('quotes.index');
   Route::get('/quotes/create', QuoteForm::class)->name('quotes.create');
   ```

---

## Testing Checklist

### For Each Feature
- [ ] Create action in isolation
- [ ] Test Livewire component mounting
- [ ] Test form validation
- [ ] Test permission checks
- [ ] Test status transitions
- [ ] Test error handling
- [ ] Test related data access (eager loading)

### Integration Tests
- [ ] Full feature workflow (create → update → delete)
- [ ] Multi-user scenarios
- [ ] Role-based access control
- [ ] Concurrent data modifications

### Performance Tests
- [ ] Query count (N+1 check)
- [ ] Large dataset pagination
- [ ] File upload handling

---

## Common Issues & Solutions

### Issue: N+1 Queries
**Solution:** Use eager loading in list components
```php
Invoice::with(['client', 'items', 'payments'])->get();
```

### Issue: Form Validation Error Not Showing
**Solution:** Check `#[Validate]` attribute matches property name
```php
#[Validate('required|email')]
public string $email = '';
```

### Issue: Permission Denied for Edit
**Solution:** Check invoice status via enum method
```php
if (!$invoice->canEdit()) {
    abort(403);
}
```

### Issue: Invoice Total Not Updating
**Solution:** Ensure InvoiceItem boot() calls `calculateTotals()`
```php
protected static function booted() {
    static::saved(function (InvoiceItem $item) {
        $item->invoice->calculateTotals();
        $item->invoice->save();
    });
}
```

---

## Performance Considerations

- **Pagination:** 15 items per page (good for UX)
- **Eager Loading:** Used in dashboard queries (`with()`)
- **Aggregations:** Uses `withSum()`, `withCount()` for efficiency
- **Caching:** Not implemented; opportunity for optimization
- **Search:** Uses `ilike` for case-insensitive (PostgreSQL)

---

## Security Considerations

- **Authorization:** Basic permission checks on actions
- **Validation:** Livewire attribute validation
- **Transactions:** Used in action classes
- **Soft Deletes:** Maintains audit trail
- **Gaps:** No audit logging, rate limiting, or encryption

---

## Next Steps

1. **Review Full Audit:** Read PLATFORM_AUDIT.md for detailed analysis
2. **Identify Priorities:** Choose which missing features to implement first
3. **Architecture Discussion:** Plan API/integration approach
4. **Roadmap Planning:** Create development timeline
5. **Performance Testing:** Test at scale before launch

