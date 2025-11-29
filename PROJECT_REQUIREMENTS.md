# Tracklet - Project Requirements & Scope

**Version:** 1.0  
**Date:** December 2025  
**Status:** Completed

---

## Project Scope

**Tracklet** is a comprehensive multi-organization management system (SaaS platform) that enables organizations to efficiently manage their business operations through integrated modules for expense tracking, inventory management, asset management, and maintenance scheduling. The platform features strict multi-tenancy with data isolation, role-based access control, and subscription-based access management.

### Core Functionality
- **Expense Tracking**: Complete financial expense management with categorization, receipt uploads, reporting, and analytics
- **Inventory Management**: Consumable items tracking with stock in/out operations, low-stock alerts, and purchase history
- **Asset Management**: Fixed asset registration, assignment tracking, movement history, and status management
- **Maintenance Scheduling**: Asset maintenance records, scheduling, cost tracking, and upcoming maintenance alerts
- **User Management**: Organization-level user administration with role-based permissions
- **Subscription Management**: Stripe-integrated subscription system with 1-month free trial

---

## Project Requirements, Scope & Documentation

**Detailed Documentation Available:**
- **SRS Document**: `SRS_DOCUMENT.md` - Complete Software Requirements Specification
- **User Manual**: `USER_MANUAL.md` - Step-by-step user guide and workflows
- **API Documentation**: `API_DOCUMENTATION.md` - Complete API reference for mobile developers
- **Features & API**: `FEATURES_AND_API.md` - Concise API endpoints with request/response formats
- **Session Context**: `SESSION_CONTEXT.md` - Technical implementation details and architecture
- **README**: `README.md` - Setup instructions and feature overview

**Additional Resources:**
- Swagger/OpenAPI Documentation: Available at `/api/documentation` (Interactive API explorer)
- Database Schema: Defined in `database/migrations/`
- Test Suite: Comprehensive PHPUnit tests in `tests/Feature/`

---

## Executive Summary

Tracklet is a production-ready multi-tenant SaaS platform built to help organizations streamline their operational management. The system provides four core modules (Expenses, Inventory, Assets, Maintenance) with comprehensive reporting, analytics, and mobile API support. 

**Key Highlights:**
- ✅ **Multi-Organization Architecture**: Complete data isolation between organizations
- ✅ **Role-Based Access Control**: 5 distinct roles (Super Admin, Admin, Finance, Admin Support, General Staff)
- ✅ **Subscription Management**: Stripe integration with 1-month free trial and auto-renewal
- ✅ **API-First Design**: Full REST API with Swagger documentation for mobile app integration
- ✅ **Comprehensive Reporting**: Charts, exports, and analytics across all modules
- ✅ **Approval Workflows**: Expense approval system for non-admin users
- ✅ **Real-time Dashboard**: Role-based dashboards with financial snapshots and alerts

**Business Value:**
- Centralized management of expenses, inventory, assets, and maintenance
- Improved financial visibility through detailed reporting and analytics
- Enhanced asset tracking and lifecycle management
- Automated low-stock alerts and maintenance scheduling
- Scalable multi-tenant architecture supporting unlimited organizations

---

## Technology Stack

### Backend
- **Framework**: Laravel 10 (PHP)
- **Database**: MySQL
- **Authentication**: Laravel Sanctum (Token-based for API) + Session (Web)
- **Authorization**: Spatie Permissions Package (Role-Based Access Control)
- **Payment Processing**: Stripe API (Subscriptions, Webhooks)
- **File Storage**: Laravel Storage (Local/Public disk)

### Frontend
- **Admin Template**: Vuexy Bootstrap Admin Template
- **UI Framework**: Bootstrap 4
- **JavaScript**: jQuery, DataTables
- **Icons**: Feather Icons

### API & Documentation
- **API Documentation**: L5-Swagger (OpenAPI 3.0)
- **API Authentication**: Bearer Token (Sanctum)
- **Response Format**: JSON with standardized structure

### Development Tools
- **Testing**: PHPUnit with comprehensive test coverage
- **Version Control**: Git
- **Package Management**: Composer (PHP), NPM (JavaScript)

---

## Architecture

### Backend Architecture
- **Framework**: Laravel 10 MVC architecture
- **Database**: MySQL with migrations and seeders
- **API Layer**: RESTful API endpoints with unified response format
- **Middleware**: Custom middleware for subscription checks, password change enforcement, and organization scoping
- **Service Layer**: Controllers with business logic, Models with relationships

### Multi-Tenancy
- **Data Isolation**: Organization-scoped queries at database level
- **Route Model Binding**: Custom bindings ensuring organization context
- **Middleware Protection**: Organization and subscription middleware on all routes

### Integration Points
- **Stripe**: Webhook-based subscription synchronization
- **Email Service**: SMTP-based email delivery (invitations, password resets, notifications)
- **File Storage**: Local storage with symbolic links for public access

---

## Key Features Implemented

### 1. Organization Management
- Super Admin can invite organizations via email
- Organizations can self-register without invitation
- Email invitation system with 7-day token expiration
- Registration source tracking (invited/self_registered)
- Organization status tracking (Pending/Joined/Subscribed)

### 2. Subscription System
- Stripe Checkout integration
- 1-month (30-day) free trial for yearly subscriptions
- Auto-renewal after trial period
- Webhook synchronization for real-time status updates
- Subscription required before platform access

### 3. User Management
- Organization-level user administration
- Role assignment (5 roles: Super Admin, Admin, Finance, Admin Support, General Staff)
- Auto-generated passwords sent via email
- Password change requirement on first login
- Strict data isolation per organization

### 4. Expense Tracking Module
- Full CRUD operations with approval workflow
- Dynamic category creation (create on-the-fly)
- Receipt/Invoice file uploads (PDF, JPG, PNG, max 10MB)
- Monthly, Quarterly, Year-to-Date reports
- Comparison reports (month vs previous, quarter vs previous)
- Visual charts (Bar, Line, Pie charts)
- Data export (Excel/PDF ready)
- Admin auto-approval, non-admin requires approval

### 5. Inventory Management Module
- Inventory item management (consumables)
- Stock In/Stock Out transactions
- Low-stock warnings with automatic alerts
- Purchase history tracking
- Item aging report (FIFO/FEFO tracking)
- Category management
- Unit price and total value calculation

### 6. Asset Management Module
- Fixed asset registration with unique code generation
- Asset assignment (Employee/Location)
- Asset movement tracking with history
- Status management (Active, In Repair, Retired)
- Age and depreciation tracking
- Warranty expiry tracking
- Serial number and model tracking

### 7. Maintenance Module
- Maintenance record management
- Multiple maintenance types (Scheduled, Repair, Inspection, Other)
- Status tracking (Pending, In Progress, Completed, Cancelled)
- Upcoming maintenance alerts (next 7 days)
- Cost tracking
- Service provider tracking
- Recurring maintenance support

### 8. Dashboard
- Role-based dashboard content
- Financial snapshot (current vs previous month, top categories)
- Inventory status (low stock warnings)
- Asset summary (total, active, in repair)
- Upcoming maintenance alerts
- Super Admin dashboard (system-wide statistics)

### 9. API & Mobile Support
- Complete REST API for all modules
- Swagger/OpenAPI interactive documentation
- Bearer token authentication
- Unified JSON response format
- Profile management API
- Forgot/Reset password API

---

## Access Control & Security

### Roles & Permissions
1. **Super Admin**: Full access across all organizations
2. **Admin**: Full access within organization (can manage users, all modules)
3. **Finance**: Expense Tracking module access
4. **Admin Support**: Inventory, Assets, and Maintenance modules access
5. **General Staff**: Read-only access to assigned assets only

### Security Features
- CSRF protection (web routes)
- Webhook signature verification (Stripe)
- Organization data isolation
- Role-based middleware protection
- Subscription middleware enforcement
- Password change enforcement for new users
- Session-based (web) and Token-based (API) authentication

---

## API Endpoints Summary

### Authentication
- `POST /api/register` - Organization registration
- `POST /api/login` - User login (returns Bearer token)
- `POST /api/logout` - User logout
- `POST /api/forgot-password` - Request password reset (sends OTP for API, reset link for Web)
- `POST /api/verify-otp` - Verify OTP for password reset (API only)
- `POST /api/reset-password` - Reset password with verification token (API) or reset token (Web)
- `POST /api/change-password` - Change password (authenticated)

### Profile Management
- `GET /api/profile` - Get current user profile
- `PUT /api/profile` - Update profile (name)
- `PUT /api/profile/password` - Update password

### Modules (All with CRUD + specific endpoints)
- **Expenses**: `/api/expenses` (with approval workflow)
- **Inventory**: `/api/inventory/items` (with stock transactions)
- **Assets**: `/api/assets` (with movements)
- **Maintenance**: `/api/maintenance` (with upcoming alerts)
- **Users**: `/api/users` (organization-level)
- **Roles**: `/api/roles` (available roles)

**Full API Documentation**: Available at `/api/documentation` (Swagger UI)

---

## Database Schema

### Core Tables
- `organizations` - Organization data with subscription info
- `users` - User accounts with organization relationship
- `organization_invitations` - Invitation tracking
- `password_resets` - Password reset tokens

### Module Tables
- `expense_categories`, `expenses` - Expense tracking
- `inventory_items`, `stock_transactions` - Inventory management
- `assets`, `asset_movements` - Asset management
- `maintenance_records` - Maintenance tracking

### System Tables
- `roles`, `permissions`, `model_has_roles` - Spatie Permissions
- `jobs`, `failed_jobs` - Queue management
- `migrations` - Database versioning

---

## Testing & Quality Assurance

- **Test Framework**: PHPUnit
- **Test Coverage**: Comprehensive feature tests for all modules
- **Test Database**: Separate test database with transactions
- **Test Types**: Feature tests, Unit tests, Authorization tests
- **Coverage Areas**: CRUD operations, authorization, validation, business logic

---

## Deployment & Infrastructure

### Requirements
- PHP 8.1+
- MySQL 5.7+
- Composer
- NPM/Node.js
- Web server (Apache/Nginx)

### Environment Configuration
- `.env` file with database, Stripe, mail, and app configuration
- Storage symbolic link for public file access
- Queue worker for background jobs (if using queues)

### Key Configuration Files
- `config/truview.php` - Application-specific settings
- `config/services.php` - Stripe and third-party service configuration
- `config/permission.php` - Spatie Permissions configuration

---

## Content & Assets

**Note**: This section should be updated based on actual content generation tools used.

- **Content Generation**: [To be specified - e.g., "Generated using XYZ AI"]
- **Images/Graphics**: [To be specified - e.g., "Generated using ABC AI"]
- **Documentation**: Written and maintained by development team
- **UI Template**: Vuexy Bootstrap Admin Template (Licensed)

---

## Future Enhancements (Optional)

- Mobile applications (iOS/Android) using existing API
- Advanced analytics and reporting
- Email notifications for low stock and maintenance alerts
- Bulk import/export functionality
- Multi-currency support
- Advanced asset depreciation calculations
- Integration with accounting software

---

## Project Status

✅ **Phase 1**: Organization Management - Completed  
✅ **Phase 2**: User Management - Completed  
✅ **Phase 3**: Expense Tracking - Completed  
✅ **Phase 4**: Inventory Management - Completed  
✅ **Phase 5**: Asset Management - Completed  
✅ **Phase 6**: Maintenance Module - Completed  
✅ **Phase 7**: Dashboard Enhancement - Completed  
✅ **Phase 8**: API & Documentation - Completed  
✅ **Phase 9**: Testing & Quality Assurance - Completed  

**Current Status**: Production Ready

---

## Contact & Support

For technical documentation, refer to:
- `README.md` - Setup and installation
- `USER_MANUAL.md` - User guide and workflows
- `API_DOCUMENTATION.md` - API reference
- `SESSION_CONTEXT.md` - Technical implementation details

---

**Built with Laravel 10 & Vuexy Bootstrap Admin Template**  
**Last Updated**: December 2025

