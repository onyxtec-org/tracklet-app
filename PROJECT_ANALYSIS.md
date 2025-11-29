# Project Analysis: TruView Order Portal

## Overview
This is a Laravel 10 application built on the **Vuexy Bootstrap Admin Template**. The project implements a device order/request management system with role-based access control.

## Technology Stack

### Backend
- **Framework**: Laravel 10.x
- **PHP Version**: 8.2.11+
- **Database**: MySQL/PostgreSQL (via Laravel migrations)
- **Authentication**: Laravel UI (Bootstrap 4)
- **Authorization**: Spatie Laravel Permission (v6.3)
- **Billing**: Laravel Cashier (v15.0) - Stripe integration
- **Queue**: Laravel Queue system (for email sending)

### Frontend
- **Template**: Vuexy Bootstrap Admin Template
- **CSS Framework**: Bootstrap 4.6.0
- **JavaScript Libraries**:
  - jQuery 3.2
  - DataTables (for data tables)
  - Feather Icons
  - ApexCharts (for charts)
  - Various Vuexy template components
- **Build Tool**: Laravel Mix 6.0.6
- **CSS Preprocessor**: Sass

## Project Structure

### Core Template Files (Vuexy - KEEP)
These are part of the Vuexy template and should be preserved:

#### Layouts (`resources/views/layouts/`)
- `verticalLayoutMaster.blade.php` - Main vertical layout
- `horizontalLayoutMaster.blade.php` - Horizontal layout
- `contentLayoutMaster.blade.php` - Content layout wrapper
- `detachedLayoutMaster.blade.php` - Detached sidebar layout
- `fullLayoutMaster.blade.php` - Full width layout
- `verticalDetachedLayoutMaster.blade.php` - Vertical detached
- `horizontalDetachedLayoutMaster.blade.php` - Horizontal detached

#### Panels (`resources/views/panels/`)
- `sidebar.blade.php` - Sidebar navigation
- `navbar.blade.php` - Top navbar
- `footer.blade.php` - Footer
- `breadcrumb.blade.php` - Breadcrumb navigation
- `scripts.blade.php` - Global scripts
- `styles.blade.php` - Global styles
- `horizontalMenu.blade.php` - Horizontal menu
- `response.blade.php` - Flash message display

#### Assets
- `resources/vendors/` - All vendor libraries (DataTables, ApexCharts, etc.)
- `resources/images/` - Template images and icons
- `resources/fonts/` - Font files (Feather, Font Awesome, etc.)
- `resources/sass/` - Template SCSS files
- `resources/js/scripts/` - Template JavaScript files (most are template demos)

#### Configuration
- `config/custom.php` - Vuexy template configuration
- `app/Helpers/helpers.php` - Template helper functions
- `app/Providers/MenuServiceProvider.php` - Menu data provider

### Custom Business Logic (REMOVE)
These are specific to the device order management system:

#### Models (`app/Models/`)
- `Device.php` - Device model (has versions and colors)
- `DeviceVersion.php` - Device version model
- `Color.php` - Color model for device versions
- `DeviceRequest.php` - Device request/order model
- `ShippingAddress.php` - Shipping address model

#### Controllers (`app/Http/Controllers/`)
- `DeviceController.php` - Device CRUD operations
- `DeviceRequestController.php` - Device request management
- `ShippingController.php` - Shipping address management
- `StripeController.php` - Stripe payment integration (not used in routes)

#### Views (`resources/views/`)
- `devices/` - Device management views
  - `create.blade.php` - Add device form
  - `list.blade.php` - Device list with DataTable
- `device_requests/` - Device request views
  - `create.blade.php` - Device request form
  - `list.blade.php` - Device requests list with DataTable
- `shipping/` - Shipping address views
  - `create.blade.php` - Add shipping address form
  - `list.blade.php` - Shipping addresses list with DataTable
- `emails/device_request_submitted.blade.php` - Email template

#### JavaScript (`resources/js/scripts/pages/`)
- `devices.js` - Device DataTable initialization
- `device-requests.js` - Device requests DataTable initialization
- `shipping-addresses.js` - Shipping addresses DataTable initialization

#### Request Validators (`app/Http/Requests/`)
- `CreateDeviceRequest.php` - Device creation validation
- `StoreDeviceRequest.php` - Device request submission validation
- `StoreShippingRequest.php` - Shipping address validation

#### Migrations (`database/migrations/`)
- `2025_02_26_054325_create_devices_table.php`
- `2025_02_26_054349_create_device_versions_table.php`
- `2025_02_26_054407_create_colors_table.php`
- `2025_02_26_093625_create_shipping_addresses_table.php`
- `2025_02_26_094236_create_device_requests_table.php`
- `2025_02_28_073832_add_status_to_devices_table.php`

#### Seeders (`database/seeders/`)
- `DeviceSeeder.php` - Seeds devices, versions, colors, and shipping addresses
- `UserSeeder.php` - Seeds admin and user roles (may keep for reference)

#### Mail Classes (`app/Mail/`)
- `DeviceRequestSubmitted.php` - Email notification for device requests

#### Repositories (`app/Repositories/`)
- `StripeBillingRepository.php` - Stripe billing operations

#### Contracts (`app/Contracts/`)
- `BillingRepositoryInterface.php` - Billing repository interface

#### Routes (`routes/web.php`)
Custom routes to remove:
- `/device-request` - Device request form
- `/device-requests` - Device requests list
- `/add-device` - Add device form
- `/devices` - Devices list
- `/shipping/create` - Add shipping address
- `/shipping-addresses` - Shipping addresses list
- Device toggle status and delete routes

#### Menu Configuration (`resources/data/menu-data/`)
- `verticalMenu.json` - Contains custom menu items for devices, device requests, shipping
- `horizontalMenu.json` - Contains template demo items (can be cleaned)

### Keep but May Need Updates

#### Core Application Files
- `app/Models/User.php` - User model (remove `Billable` trait if not using Stripe)
- `app/Http/Controllers/DashboardController.php` - Currently redirects based on roles, may need update
- `app/Http/Controllers/ProfileController.php` - Basic profile management (keep)
- `resources/views/dashboard.blade.php` - Template demo dashboard (can be customized)
- `resources/views/profile.blade.php` - User profile page (keep)
- `config/truview.php` - Role configuration (may keep for reference)

#### Authentication
- `resources/views/auth/` - Laravel UI auth views (styled with Vuexy)
- Auth controllers in `app/Http/Controllers/Auth/` - Standard Laravel UI

#### Dependencies
- `laravel/cashier` - Only needed if using Stripe billing
- `spatie/laravel-permission` - Useful for role-based access (recommend keeping)

## Database Schema

### Custom Tables (REMOVE)
- `devices` - Stores device names and status
- `device_versions` - Stores device versions (linked to devices)
- `colors` - Stores colors for device versions
- `shipping_addresses` - Stores shipping addresses
- `device_requests` - Stores device order requests

### Core Tables (KEEP)
- `users` - User accounts
- `password_resets` - Password reset tokens
- `failed_jobs` - Failed queue jobs
- `jobs` - Queue jobs
- `permissions`, `roles`, `model_has_permissions`, etc. - Spatie permission tables
- `customers`, `subscriptions`, `subscription_items` - Laravel Cashier tables (if using)

## Features Implemented

### Device Management
- Create devices with versions and colors
- List devices with DataTable
- Enable/disable devices
- Delete devices (cascades to versions and colors)

### Device Requests
- Submit device requests with:
  - Customer information (name, email, phone)
  - Device selection (device → version → colors)
  - Shipping address
  - Additional notes
- List all device requests (admin only)
- Email notification on submission

### Shipping Management
- Add multiple shipping addresses
- List shipping addresses with DataTable
- Delete shipping addresses

### User Roles
- **Admin**: Can view all device requests and manage devices
- **User**: Can only submit device requests

## DataTables Implementation

The project uses DataTables with:
- AJAX data loading
- Server-side rendering (via Laravel controllers)
- Export functionality (CSV, Excel, PDF, Print)
- Responsive design
- Custom column rendering
- Action buttons (edit, delete)

Example pattern:
```javascript
$('.table').DataTable({
    ajax: '/route',
    columns: [...],
    dom: '...',
    buttons: [...]
});
```

## Removal Strategy

1. **Remove Models** - Delete custom model files
2. **Remove Controllers** - Delete custom controller files
3. **Remove Views** - Delete custom view directories
4. **Remove Routes** - Clean up `routes/web.php`
5. **Remove Migrations** - Delete custom migration files
6. **Remove Seeders** - Delete custom seeder files
7. **Remove JavaScript** - Delete custom JS files
8. **Remove Request Validators** - Delete custom request classes
9. **Remove Mail Classes** - Delete email classes
10. **Remove Repositories/Contracts** - Delete billing-related files
11. **Update Menus** - Clean menu JSON files
12. **Update Dashboard** - Simplify dashboard controller
13. **Clean Dependencies** - Remove unused packages if any

## Template Preservation

The Vuexy template structure will remain intact:
- All layout files
- All panel components
- All vendor assets
- All template JavaScript
- Template configuration
- Helper functions

## Next Steps After Cleanup

1. Update dashboard to show basic welcome or analytics
2. Create new models/controllers for your new project
3. Update menu configuration for new modules
4. Customize theme colors/styles if needed
5. Set up new database migrations
6. Implement new business logic


