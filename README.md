# Tracklet - Multi-Organization Management System

## Overview
Tracklet is a multi-organization management system built on Laravel 10 with Vuexy Bootstrap Admin Template. It features organization isolation, role-based access control, and Stripe subscription management with full webhook synchronization.

## ✨ Features

### Phase 1: Organization Management ✅
- ✅ Super admin can invite organizations
- ✅ Organizations can self-register (no invitation needed)
- ✅ Email invitation system with tracking
- ✅ First account becomes admin automatically
- ✅ Stripe subscription integration
- ✅ **1-month free trial** for yearly subscriptions
- ✅ Subscription required before platform access
- ✅ Status tracking (Pending/Joined/Subscribed/Source)
- ✅ API support (web + JSON responses)
- ✅ Full webhook synchronization with Stripe

### Phase 2: User Management ✅
- ✅ Organization-level user management (Admin role)
- ✅ Create, edit, delete users within organization
- ✅ Role assignment (Admin, Finance, Admin Support, General Staff)
- ✅ Strict data isolation (users can only see their organization)
- ✅ Super Admin exception (can manage all organizations)
- ✅ User filtering and search

### Phase 3: Expense Tracking Module ✅
- ✅ Full CRUD operations for expenses
- ✅ Expense categories (predefined + customizable)
- ✅ Receipt/Invoice file uploads
- ✅ Monthly, Quarterly, and Year-to-Date reports
- ✅ Comparison reports (month vs previous month, quarter vs previous quarter)
- ✅ Visual charts data (Bar, Line, Pie charts)
- ✅ Data export functionality (Excel/PDF ready)
- ✅ Vendor/Payee tracking
- ✅ Finance role access

### Phase 4: Inventory Management Module ✅
- ✅ Inventory item management (consumables)
- ✅ Stock logging (Stock In/Stock Out)
- ✅ Low-stock warnings with automatic alerts
- ✅ Purchase history tracking
- ✅ Item aging report (FIFO/FEFO tracking)
- ✅ Category management
- ✅ Unit price and total value tracking
- ✅ Admin Support role access

### Phase 5: Asset Management Module ✅
- ✅ Fixed asset registration
- ✅ Automatic unique asset code generation
- ✅ Asset assignment (Employee/Location)
- ✅ Asset movement tracking
- ✅ Status tracking (Active, In Repair, Retired)
- ✅ Age and depreciation tracking
- ✅ Warranty expiry tracking
- ✅ Serial number and model tracking
- ✅ Admin Support role access

### Phase 6: Repair & Maintenance Module ✅
- ✅ Maintenance record management
- ✅ Scheduled, Repair, Inspection, and Other maintenance types
- ✅ Status tracking (Pending, In Progress, Completed, Cancelled)
- ✅ Upcoming maintenance alerts (next 7 days)
- ✅ Cost tracking
- ✅ Service provider tracking
- ✅ Recurring maintenance support
- ✅ Admin Support role access

### Phase 7: Enhanced Dashboard ✅
- ✅ Financial snapshot (current vs previous month, top categories)
- ✅ Inventory status (low stock warnings)
- ✅ Asset summary (total, active, in repair)
- ✅ Upcoming maintenance (next 7 days)
- ✅ Expense charts data
- ✅ Role-based dashboard content

### Roles
- `super_admin` - Full access across all organizations
- `admin` - Full access within organization (can manage users)
- `finance` - Expense Tracking Module access
- `admin_support` - Inventory, Asset Management, and Maintenance modules access
- `general_staff` - Read-only access to relevant views

---

## 🚀 Quick Start

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Environment Setup
Copy `.env.example` to `.env` and configure:

```env
# Application
APP_NAME="Tracklet"
APP_URL=http://localhost:8000
APP_ENV=local
APP_DEBUG=true

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tracklet
DB_USERNAME=root
DB_PASSWORD=

# Stripe (REQUIRED)
STRIPE_KEY=pk_test_YOUR_PUBLISHABLE_KEY_HERE
STRIPE_SECRET=sk_test_YOUR_SECRET_KEY_HERE
STRIPE_PRICE_ID=price_YOUR_PRICE_ID_HERE
STRIPE_WEBHOOK_SECRET=whsec_YOUR_WEBHOOK_SECRET_HERE

# Mail (REQUIRED for invitations)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tracklet.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 3. Setup Commands
```bash
# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed roles and super admin
php artisan db:seed

# Seed default expense categories (for existing organizations)
php artisan db:seed --class=ExpenseCategorySeeder

# Compile assets
npm run dev
```

### 4. Start Server
```bash
php artisan serve
```

Visit: `http://localhost:8000`

**Default Super Admin:**
- Email: `superadmin@tracklet.com`
- Password: `password`

---

## 📋 Complete Setup Guide

### Stripe Configuration

#### 1. Get Stripe API Keys
1. Visit: https://dashboard.stripe.com/test/apikeys
2. Copy **Publishable key** → `STRIPE_KEY` in `.env`
3. Copy **Secret key** → `STRIPE_SECRET` in `.env`

#### 2. Create Subscription Product
1. Visit: https://dashboard.stripe.com/test/products
2. Click "Add product"
3. Name: "Tracklet Annual Subscription"
4. Pricing: Set to **Recurring** → **Yearly**
5. Set price (e.g., $999/year)
6. Copy **Price ID** (starts with `price_`) → `STRIPE_PRICE_ID` in `.env`

**Note:** The application automatically adds a **1-month free trial** to all yearly subscriptions. Users will have full access during the trial period, and the subscription will automatically begin after the trial ends.

#### 3. Setup Webhook (REQUIRED for proper sync)

**For Local Development:**
```bash
# Install Stripe CLI
# macOS: brew install stripe/stripe-cli/stripe
# Linux: Download from https://github.com/stripe/stripe-cli/releases

# Login to Stripe
stripe login

# Forward webhooks to local server
stripe listen --forward-to localhost:8000/webhook/stripe

# Copy the webhook signing secret (output shows: whsec_...)
# Add to .env as STRIPE_WEBHOOK_SECRET
```

**For Production:**
1. Go to: https://dashboard.stripe.com/webhooks
2. Click "Add endpoint"
3. **Endpoint URL:** `https://yourdomain.com/webhook/stripe`
4. **Select ALL these events (11 events):**
   - `customer.subscription.created`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
   - `customer.subscription.trial_will_end`
   - `invoice.payment_succeeded`
   - `invoice.payment_failed`
   - `invoice.payment_action_required`
   - `invoice.upcoming`
   - `customer.updated`
   - `customer.deleted`
   - `payment_method.attached`
5. Copy webhook signing secret → `STRIPE_WEBHOOK_SECRET` in `.env`

**Test Webhook:**
```bash
stripe trigger customer.subscription.created
tail -f storage/logs/laravel.log | grep "Stripe Webhook"
```

### Mail Configuration

#### Option 1: Mailtrap (Recommended for Development)
1. Sign up at https://mailtrap.io
2. Create inbox
3. Copy SMTP credentials to `.env`

#### Option 2: Gmail (For Real Emails)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
```

---

## 🧪 Testing Flows

### Flow 1: Super Admin Invites Organization

1. **Login as Super Admin**
   - URL: `http://localhost:8000/login`
   - Email: `superadmin@tracklet.com`
   - Password: `password`

2. **Invite Organization**
   - Go to `/super-admin/organizations`
   - Click "Invite Organization"
   - Fill in organization name and admin email
   - Click "Send Invitation"
   - ✅ Email sent, organization shows as "Pending"

3. **Accept Invitation**
   - Check email (Mailtrap inbox)
   - Click invitation link
   - Fill in name and password
   - ✅ Email field is readonly and must match invitation
   - ✅ Account created, auto-logged in, redirected to subscription

4. **Complete Subscription**
   - Click "Subscribe Now"
   - Use Stripe test card: `4242 4242 4242 4242`
   - Any future expiry date, any CVC
   - ✅ **1-month free trial activated**, full access granted
   - ✅ Trial status shown on dashboard
   - ✅ Subscription will automatically begin after trial ends

5. **Verify in Super Admin Dashboard**
   - Go to `/super-admin/organizations`
   - ✅ Organization shows: "Joined", "Subscribed", "Invited"

### Flow 2: Self-Registration

1. **Register Organization**
   - Go to `/register-organization`
   - Fill in organization and admin details
   - Click "Register Organization"
   - ✅ Organization created, auto-logged in, redirected to subscription

2. **Complete Subscription**
   - Follow same subscription steps as Flow 1

3. **Verify in Super Admin Dashboard**
   - Login as super admin
   - ✅ Organization shows: "Self Registered", "Subscribed"

### Flow 3: Test Subscription Requirement

1. Register organization (don't complete subscription)
2. Try accessing dashboard
3. ✅ Redirected to `/subscription/checkout`
4. Complete subscription
5. ✅ Access granted

### Flow 4: Test API Endpoints

**Register Organization via API:**
```bash
curl -X POST http://localhost:8000/api/register-organization \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "organization_name": "API Test Corp",
    "name": "API Admin",
    "email": "api@testcorp.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**List Organizations (Super Admin):**
```bash
curl -X GET http://localhost:8000/api/super-admin/organizations \
  -H "Accept: application/json" \
  -H "Cookie: laravel_session=your_session_cookie"
```

---

## 🔌 API Documentation

### API Response Format

**Success Response:**
```json
{
  "success": true,
  "data": {
    // Response data
  },
  "message": "Optional message"
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    // Validation errors if any
  }
}
```

### Available API Endpoints

#### Public Endpoints
- `GET /api/register-organization` - Get registration form/data
- `POST /api/register-organization` - Register organization
- `GET /api/invitation/{token}` - Get invitation details
- `POST /api/invitation/{token}/accept` - Accept invitation
- `POST /api/webhook/stripe` - Stripe webhook endpoint

#### Authenticated Endpoints
- `GET /api/user` - Get current user
- `GET /api/dashboard` - Get dashboard data
- `GET /api/profile` - Get profile data
- `PUT /api/profile/password` - Update password
- `GET /api/subscription/checkout` - Get checkout data
- `POST /api/subscription/checkout` - Create checkout session
- `GET /api/subscription/success` - Get success page data

#### Organization-Level Endpoints (Require Subscription)

**User Management** (Admin role):
- `GET /api/users` - List users
- `POST /api/users` - Create user
- `GET /api/users/{id}` - Get user
- `PUT /api/users/{id}` - Update user
- `DELETE /api/users/{id}` - Delete user

**Expense Tracking** (Admin/Finance role):
- `GET /api/expenses` - List expenses
- `POST /api/expenses` - Create expense
- `GET /api/expenses/{id}` - Get expense
- `PUT /api/expenses/{id}` - Update expense
- `DELETE /api/expenses/{id}` - Delete expense
- `GET /api/expenses/reports` - Get reports
- `GET /api/expenses/charts` - Get charts data
- `GET /api/expenses/export` - Export expenses
- `GET /api/expenses/categories` - List categories
- `POST /api/expenses/categories` - Create category
- `PUT /api/expenses/categories/{id}` - Update category
- `DELETE /api/expenses/categories/{id}` - Delete category

**Inventory Management** (Admin/Admin Support role):
- `GET /api/inventory/items` - List items
- `POST /api/inventory/items` - Create item
- `GET /api/inventory/items/{id}` - Get item
- `PUT /api/inventory/items/{id}` - Update item
- `DELETE /api/inventory/items/{id}` - Delete item
- `POST /api/inventory/items/{id}/stock` - Log stock transaction
- `GET /api/inventory/items/{id}/transactions` - Get stock transactions
- `GET /api/inventory/low-stock` - Get low stock items
- `GET /api/inventory/purchase-history` - Get purchase history
- `GET /api/inventory/aging-report` - Get aging report

**Asset Management** (Admin/Admin Support role):
- `GET /api/assets` - List assets
- `POST /api/assets` - Create asset
- `GET /api/assets/{id}` - Get asset
- `PUT /api/assets/{id}` - Update asset
- `DELETE /api/assets/{id}` - Delete asset
- `POST /api/assets/{id}/movement` - Log asset movement

**Maintenance** (Admin/Admin Support role):
- `GET /api/maintenance` - List maintenance records
- `POST /api/maintenance` - Create maintenance record
- `GET /api/maintenance/{id}` - Get maintenance record
- `PUT /api/maintenance/{id}` - Update maintenance record
- `DELETE /api/maintenance/{id}` - Delete maintenance record
- `GET /api/maintenance/upcoming` - Get upcoming maintenance

#### Super Admin Endpoints
- `GET /api/super-admin/organizations` - List organizations
- `POST /api/super-admin/organizations` - Create organization
- `GET /api/super-admin/organizations/{id}` - Get organization
- `PUT /api/super-admin/organizations/{id}` - Update organization
- `DELETE /api/super-admin/organizations/{id}` - Delete organization
- `POST /api/super-admin/organizations/{id}/resend-invitation` - Resend invitation

**Note:** All controllers automatically detect API requests and return JSON. Web requests return views/redirects. All endpoints support both web and API requests.

---

## 🔔 Stripe Webhook Events

The application handles **11 Stripe webhook events** to keep subscriptions in sync:

### Critical Events (Required)
1. `customer.subscription.created` - New subscription created
2. `customer.subscription.updated` - Subscription modified
3. `customer.subscription.deleted` - Subscription cancelled
4. `invoice.payment_succeeded` - Payment successful
5. `invoice.payment_failed` - Payment failed

### Recommended Events
6. `customer.subscription.trial_will_end` - Trial ending soon
7. `invoice.payment_action_required` - 3D Secure action needed
8. `invoice.upcoming` - Upcoming invoice
9. `customer.updated` - Customer info updated
10. `customer.deleted` - Customer deleted
11. `payment_method.attached` - Payment method added

### Webhook Features
- ✅ Automatic organization subscription sync
- ✅ Handles renewals automatically
- ✅ Handles cancellations automatically
- ✅ Updates payment method info
- ✅ Signature verification (secure)
- ✅ Comprehensive logging
- ✅ Graceful error handling

### Webhook Endpoints
- `/webhook/stripe` (web route)
- `/api/webhook/stripe` (API route)

Both endpoints are identical and handle all events.

---

## 🐛 Troubleshooting

### Issue: Invitation email not sending
**Solution:**
1. Check mail configuration in `.env`
2. Test mail: `php artisan tinker`
   ```php
   Mail::raw('Test', function($m) { 
       $m->to('test@example.com')->subject('Test'); 
   });
   ```
3. Check `storage/logs/laravel.log` for errors

### Issue: Stripe Checkout not working
**Solution:**
1. Verify Stripe keys in `.env`
2. Check `STRIPE_PRICE_ID` is correct
3. Ensure price is set to recurring/yearly in Stripe
4. Check browser console for JavaScript errors
5. Check `storage/logs/laravel.log` for errors

### Issue: Subscription not activating
**Solution:**
1. **Check webhook is configured** (REQUIRED)
   - Verify `STRIPE_WEBHOOK_SECRET` in `.env`
   - Test webhook: `stripe trigger customer.subscription.created`
   - Check logs: `tail -f storage/logs/laravel.log | grep "Stripe Webhook"`
2. Manually check subscription in Stripe dashboard
3. Verify `subscription_ends_at` is set in database
4. Check `is_subscribed` flag in organizations table
5. **Webhook will automatically sync** - wait a few seconds after payment

### Issue: Webhook signature verification failing
**Solution:**
1. Verify webhook secret matches Stripe Dashboard
2. For local: Check Stripe CLI output
3. Check for proxy/load balancer issues
4. Ensure original headers are preserved

### Issue: Email validation failing
**Solution:**
1. Ensure email matches exactly (case-sensitive)
2. Check for extra spaces
3. Verify invitation token is valid
4. Check invitation hasn't expired (7 days)

---

## 📊 Database Verification

### Check Organizations
```sql
SELECT id, name, email, registration_source, is_subscribed, subscription_ends_at, created_at 
FROM organizations;
```

### Check Users
```sql
SELECT id, name, email, organization_id, created_at 
FROM users;
```

### Check Invitations
```sql
SELECT id, organization_id, email, accepted_at, expires_at, created_at 
FROM organization_invitations;
```

### Check Roles
```sql
SELECT u.email, r.name as role 
FROM users u 
JOIN model_has_roles mhr ON u.id = mhr.model_id 
JOIN roles r ON mhr.role_id = r.id;
```

---

## 🔧 Requirements
- **Laravel**: 10.x
- **PHP**: 8.2.11 or higher
- **Node.js**: For frontend asset compilation
- **Stripe Account**: For subscription management
- **Mail Service**: For invitation emails (Mailtrap recommended for dev)

---

## 🚀 Production Deployment

### Pre-Deployment Checklist
- [ ] Set up production Stripe account
- [ ] Configure production webhook with all 11 events
- [ ] Set up production email service
- [ ] Update `.env` with production values
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Test webhook delivery in Stripe Dashboard

### Production Environment Variables
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Production Stripe keys
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_PRICE_ID=price_...
STRIPE_WEBHOOK_SECRET=whsec_...

# Production mail
MAIL_MAILER=smtp
MAIL_HOST=your_production_smtp_host
# ... other mail settings
```

---

## 📝 Quick Reference

### Test Credentials
- **Super Admin:** `superadmin@tracklet.com` / `password`
- **Stripe Test Card:** `4242 4242 4242 4242` (any future expiry, any CVC)

### Important Routes
- Login: `/login`
- Register Organization: `/register-organization`
- Super Admin Dashboard: `/super-admin/organizations`
- Subscription Checkout: `/subscription/checkout`
- Webhook: `/webhook/stripe`

### Useful Commands
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Check routes
php artisan route:list | grep organization
php artisan route:list | grep subscription

# Check database
php artisan tinker
>>> \App\Models\Organization::count()
>>> \App\Models\User::count()
```

---

## 🎯 Next Steps
1. ✅ Test organization invitation and registration flows
2. ✅ Test subscription checkout and webhook sync
3. ✅ All modules implemented (Expense Tracking, Inventory, Assets, Maintenance, User Management)
4. Customize dashboard for your needs
5. Set up production environment
6. Install additional packages for exports (Laravel Excel, DomPDF) if needed

---

## 📱 API Documentation

### Interactive Swagger UI
Visit `/api/documentation` in your browser for interactive API documentation with:
- Complete list of all endpoints
- Request/response examples
- Try it out functionality - test APIs directly from the browser
- Authentication support - add Bearer token to test authenticated endpoints
- Schema definitions for all models

### Static Documentation
Complete API documentation for mobile developers is available:

- **API_DOCUMENTATION.md** - Comprehensive API documentation with all endpoints, request/response formats, and examples
- **FEATURES_AND_API.md** - Concise API reference with all endpoints and their request/response formats
- **API_QUICK_REFERENCE.md** - Quick reference guide for common endpoints
- **TESTING_GUIDE.md** - Complete testing guide with step-by-step instructions

### Quick API Overview
- Base URL: `https://yourdomain.com/api`
- Authentication: Session-based (web) or Bearer Token (mobile)
- Response Format: JSON with `success`, `data`, and `message` fields
- All endpoints support both web and API requests automatically
- All modules are API-ready (User Management, Expenses, Inventory, Assets, Maintenance)

---

## 📚 Additional Information

### Key Features Implemented
- ✅ Multi-organization support with strict data isolation
- ✅ Super admin can invite organizations
- ✅ Organizations can self-register
- ✅ Email matching enforced (invitation flow)
- ✅ First account becomes admin automatically
- ✅ Stripe subscription integration with 1-month free trial
- ✅ Full webhook synchronization
- ✅ Subscription required before platform access
- ✅ Status tracking (Pending/Joined/Subscribed/Source)
- ✅ API support for all endpoints (web + mobile)
- ✅ Role-based access control (5 roles)
- ✅ User Management module (organization-level)
- ✅ Expense Tracking module (CRUD, reports, charts, export)
- ✅ Inventory Management module (items, stock, low-stock alerts)
- ✅ Asset Management module (assets, assignments, movements)
- ✅ Repair & Maintenance module (records, scheduling, tracking)
- ✅ Enhanced Dashboard (financial snapshot, inventory status, asset summary)

### Security Features
- ✅ Webhook signature verification
- ✅ Email matching validation
- ✅ CSRF protection (webhook excluded)
- ✅ Organization data isolation
- ✅ Role-based middleware
- ✅ Subscription middleware

---

## 📞 Support

For issues or questions:
1. Check `storage/logs/laravel.log` for errors
2. Verify webhook configuration in Stripe Dashboard
3. Test webhook with Stripe CLI
4. Check database for subscription status

---

**Built with Laravel 10 & Vuexy Bootstrap Admin Template**
