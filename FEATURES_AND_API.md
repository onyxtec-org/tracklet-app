# Tracklet - Features & API Endpoints

**Base URL:** `https://yourdomain.com/api`  
**Version:** 1.0  
**Authentication:** Bearer Token (Mobile) | Session (Web)

**Header:** `Authorization: Bearer {token}`

**📚 Interactive API Documentation:** Visit `/api/documentation` for Swagger UI with all endpoints, request/response formats, and the ability to test APIs directly from the browser.

---

## 🔐 Authentication

### Register Organization
**POST** `/api/register`

**Request:**
```json
{
  "organization_name": "Acme Corp",
  "name": "John Doe",
  "email": "admin@acme.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
  "success": true,
  "data": {
    "token": "1|abc123...",
    "user": { "id": 1, "name": "John Doe", "email": "admin@acme.com" },
    "organization": { "id": 1, "name": "Acme Corp" }
  }
}
```

### Login
**POST** `/api/login`

**Request:**
```json
{
  "email": "admin@acme.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "token": "1|abc123...",
    "user": { "id": 1, "name": "John Doe", "email": "admin@acme.com" }
  }
}
```

### Forgot Password (API - OTP Based)
**POST** `/api/forgot-password`  
**Auth:** Not Required

**Request:**
```json
{
  "email": "user@example.com"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "We have sent an OTP to your email address. Please check your inbox."
}
```

**Note:** API sends 6-digit OTP via email (expires in 10 minutes). Web sends reset link.

### Verify OTP (API Only)
**POST** `/api/verify-otp`  
**Auth:** Not Required  
**Request:** `{"email": "user@example.com", "otp": "123456"}`  
**Response:** `{"success": true, "message": "OTP verified successfully. You can now reset your password.", "data": {"verification_token": "abc123..."}}`  
**Note:** Verifies OTP and returns verification_token for password reset. OTP expires in 10 minutes.

### Reset Password (API - After OTP Verification)
**POST** `/api/reset-password`  
**Auth:** Not Required  
**Request:** `{"email": "user@example.com", "verification_token": "abc123...", "password": "newpassword", "password_confirmation": "newpassword"}`  
**Response:** `{"success": true, "message": "Your password has been reset successfully!"}`  
**Note:** API uses verification_token from verify-otp endpoint. Web uses token from reset link. Verification token expires in 10 minutes.

### Get Current User
**GET** `/api/user`  
**Auth:** Required

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "admin@acme.com",
    "organization": {
      "id": 1,
      "name": "Acme Corp",
      "is_subscribed": true,
      "trial_ends_at": null
    }
  }
}
```

### Logout
**POST** `/api/logout`  
**Auth:** Required

### Change Password
**POST** `/api/change-password`  
**Auth:** Required

**Request:**
```json
{
  "current_password": "oldpass",
  "password": "newpass123",
  "password_confirmation": "newpass123"
}
```

---

## 👤 Profile Management

### Get Profile
**GET** `/api/profile`  
**Auth:** Required

**Response (200):**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "organization": {"id": 1, "name": "Acme Corp"},
      "roles": [{"name": "admin"}]
    }
  }
}
```

### Update Profile
**PUT** `/api/profile`  
**Auth:** Required

**Request:**
```json
{
  "name": "John Doe"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    }
  }
}
```

### Update Password (Profile)
**PUT** `/api/profile/password`  
**Auth:** Required

**Request:**
```json
{
  "old-password": "oldpassword123",
  "new-password": "newpassword123",
  "new-password_confirmation": "newpassword123"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Password updated successfully"
}
```

---

## 🔑 Roles

### Get Available Roles
**GET** `/api/roles`  
**Auth:** Required

**Response (200):**
```json
{
  "success": true,
  "data": {
    "roles": [
      {
        "id": 2,
        "name": "admin",
        "display_name": "Admin",
        "description": "Full access within organization"
      },
      {
        "id": 3,
        "name": "finance",
        "display_name": "Finance",
        "description": "Access to Expense Tracking Module"
      },
      {
        "id": 4,
        "name": "admin_support",
        "display_name": "Admin Support",
        "description": "Access to Inventory, Assets, and Maintenance modules"
      },
      {
        "id": 5,
        "name": "general_staff",
        "display_name": "General Staff",
        "description": "Read-only access to relevant views"
      }
    ]
  }
}
```

---

## 👥 User Management

**Role:** `admin` | **Requires:** Subscription

### List Users
**GET** `/api/users`  
**Query:** `?search=john&role=finance&organization_id=1`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "users": {
      "data": [
        {
          "id": 1,
          "name": "John Doe",
          "email": "john@acme.com",
          "roles": [{"name": "admin"}]
        }
      ]
    }
  }
}
```

### Create User
**POST** `/api/users`

**Request:**
```json
{
  "name": "Jane Doe",
  "email": "jane@acme.com",
  "role": "finance"
}
```

**Response (201):**
```json
{
  "success": true,
  "data": {
    "message": "User created successfully. An email with login credentials has been sent.",
    "user": {
      "id": 2,
      "name": "Jane Doe",
      "email": "jane@acme.com",
      "roles": [{"name": "finance"}]
    }
  }
}
```

**Note:** Password auto-generated and sent via email. User must change on first login.

### Get User
**GET** `/api/users/{id}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@acme.com",
      "roles": [{"name": "admin"}]
    }
  }
}
```

### Update User
**PUT** `/api/users/{id}`

**Request:**
```json
{
  "name": "Jane Doe",
  "email": "jane@acme.com",
  "role": "admin_support"
}
```

### Delete User
**DELETE** `/api/users/{id}`

---

## 💰 Expense Tracking

**Roles:** `admin`, `finance` | **Requires:** Subscription

**⚠️ Approval Workflow:**
- **Admin expenses:** Auto-approved when created
- **Non-admin expenses:** Created with "pending" status, require admin approval
- **Only approved expenses** appear in reports, charts, and exports

### List Expenses
**GET** `/api/expenses`  
**Query:** `?category_id=1&date_from=2025-01-01&date_to=2025-12-31&vendor=office&approval_status=pending`

**Note:** Non-admin users only see their own expenses or approved expenses. Admin can filter by `approval_status`.

**Response (200):**
```json
{
  "success": true,
  "data": {
    "expenses": {
      "data": [
        {
          "id": 1,
          "expense_date": "2025-11-28",
          "amount": "150.00",
          "vendor_payee": "Office Depot",
          "description": "Office supplies",
          "approval_status": "approved",
          "approved_by": 1,
          "approved_at": "2025-11-28T10:30:00.000000Z",
          "rejection_reason": null,
          "category": {"id": 1, "name": "Stationery"},
          "user": {"id": 1, "name": "John Doe"},
          "approver": {"id": 1, "name": "Admin User"}
        }
      ]
    }
  }
}
```

### Create Expense
**POST** `/api/expenses`

**Request:**
```json
{
  "expense_category_id": 1,
  "category_name": "New Category",
  "expense_date": "2025-11-28",
  "amount": "150.00",
  "vendor_payee": "Office Depot",
  "description": "Office supplies"
}
```

**Note:** Use either `expense_category_id` (existing) or `category_name` (auto-creates if doesn't exist).

**Response (201) - Admin:**
```json
{
  "success": true,
  "message": "Expense created and approved successfully.",
  "data": {
    "expense": {
      "id": 1,
      "approval_status": "approved",
      "approved_by": 1,
      "approved_at": "2025-11-28T10:30:00.000000Z"
    }
  }
}
```

**Response (201) - Non-Admin:**
```json
{
  "success": true,
  "message": "Expense created successfully. It is pending admin approval.",
  "data": {
    "expense": {
      "id": 1,
      "approval_status": "pending",
      "approved_by": null,
      "approved_at": null
    }
  }
}
```

### Approve Expense
**POST** `/api/expenses/{id}/approve`  
**Role:** `admin` only

**Response (200):**
```json
{
  "success": true,
  "message": "Expense approved successfully.",
  "data": {
    "expense": {
      "approval_status": "approved",
      "approved_by": 1,
      "approved_at": "2025-11-28T10:30:00.000000Z"
    }
  }
}
```

### Reject Expense
**POST** `/api/expenses/{id}/reject`  
**Role:** `admin` only

**Request:**
```json
{
  "rejection_reason": "Insufficient documentation"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Expense rejected successfully.",
  "data": {
    "expense": {
      "approval_status": "rejected",
      "approved_by": 1,
      "approved_at": "2025-11-28T10:30:00.000000Z",
      "rejection_reason": "Insufficient documentation"
    }
  }
}
```

### Get Expense
**GET** `/api/expenses/{id}`

### Update Expense
**PUT** `/api/expenses/{id}`

**Request:** Same as create

**Note:** If non-admin edits an approved/rejected expense, approval status resets to "pending".

### Delete Expense
**DELETE** `/api/expenses/{id}`

### Get Reports
**GET** `/api/expenses/reports`  
**Query:** `?period=monthly&month=11&year=2025`

### Get Charts Data
**GET** `/api/expenses/charts`  
**Query:** `?period=quarterly`

### List Categories
**GET** `/api/expenses/categories`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "categories": [
      {
        "id": 1,
        "name": "Stationery",
        "description": null,
        "is_system": true
      }
    ]
  }
}
```

### Create Category
**POST** `/api/expenses/categories`

**Request:**
```json
{
  "name": "Category Name",
  "description": "Optional description"
}
```

### Update Category
**PUT** `/api/expenses/categories/{id}`

### Delete Category
**DELETE** `/api/expenses/categories/{id}`

---

## 📦 Inventory Management

**Roles:** `admin`, `admin_support` | **Requires:** Subscription

### List Items
**GET** `/api/inventory/items`  
**Query:** `?category=Office Supplies&low_stock=1&search=paper`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "items": {
      "data": [
        {
          "id": 1,
          "name": "A4 Paper",
          "category": "Office Supplies",
          "quantity": 100,
          "minimum_threshold": 20,
          "unit_price": "5.00",
          "total_price": "500.00",
          "unit": "reams"
        }
      ]
    },
    "categories": ["Office Supplies", "Electronics"],
    "low_stock_count": 3
  }
}
```

### Create Item
**POST** `/api/inventory/items`

**Request:**
```json
{
  "name": "A4 Paper",
  "category": "Office Supplies",
  "quantity": 100,
  "minimum_threshold": 20,
  "unit_price": "5.00",
  "unit": "reams"
}
```

**Response (201):**
```json
{
  "success": true,
  "data": {
    "message": "Inventory item created successfully.",
    "item": {
      "id": 1,
      "name": "A4 Paper",
      "quantity": 100,
      "total_price": "500.00"
    }
  }
}
```

### Get Item
**GET** `/api/inventory/items/{id}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "item": {
      "id": 1,
      "name": "A4 Paper",
      "quantity": 100,
      "stock_transactions": [...]
    }
  }
}
```

### Update Item
**PUT** `/api/inventory/items/{id}`

**Request:** Same as create

### Delete Item
**DELETE** `/api/inventory/items/{id}`

### Log Stock Transaction
**POST** `/api/inventory/items/{id}/stock`  
**Purpose:** Log stock in (additions) or stock out (removals). Automatically updates item quantity.

**Request:**
```json
{
  "type": "in",              // Required: "in" or "out"
  "quantity": 50,            // Required: integer, min: 1
  "transaction_date": "2025-11-28",  // Required: date (YYYY-MM-DD)
  "reference": "PO-12345",   // Optional: Purchase order, usage reason
  "notes": "New purchase",   // Optional: Additional notes
  "unit_price": "5.00",      // Optional: For stock in only
  "vendor": "Office Supplies Co"  // Optional: For stock in only
}
```

**Stock In Example:**
```json
{
  "type": "in",
  "quantity": 100,
  "transaction_date": "2025-11-28",
  "reference": "PO-12345",
  "unit_price": "5.50",
  "vendor": "Office Supplies Co"
}
```

**Stock Out Example:**
```json
{
  "type": "out",
  "quantity": 25,
  "transaction_date": "2025-11-28",
  "reference": "Used for office printing",
  "notes": "Distributed to marketing"
}
```

**Response (201):**
```json
{
  "success": true,
  "data": {
    "message": "Stock transaction logged successfully.",
    "transaction": {
      "id": 1,
      "type": "in",
      "quantity": 50,
      "transaction_date": "2025-11-28",
      "reference": "PO-12345",
      "user": {"name": "John Doe"}
    },
    "item": {
      "id": 1,
      "quantity": 150  // Updated quantity
    }
  }
}
```

**Notes:**
- **Stock In:** Adds to quantity. Can include `unit_price` and `vendor`.
- **Stock Out:** Subtracts from quantity. Validates sufficient stock - returns error if insufficient.

### Get Stock Transactions
**GET** `/api/inventory/items/{id}/transactions`  
**Query:** `?type=out&date_from=2025-11-01&date_to=2025-11-30`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "transactions": [
      {
        "id": 1,
        "type": "in",
        "quantity": 50,
        "transaction_date": "2025-11-28",
        "reference": "PO-12345",
        "notes": "New purchase",
        "unit_price": "5.00",
        "vendor": "Office Supplies Co",
        "user": {"name": "John Doe"}
      },
      {
        "id": 2,
        "type": "out",
        "quantity": 25,
        "transaction_date": "2025-11-29",
        "reference": "Used for printing",
        "notes": "Marketing department",
        "user": {"name": "Jane Smith"}
      }
    ]
  }
}
```

### Get Low Stock Items
**GET** `/api/inventory/low-stock`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": 1,
        "name": "A4 Paper",
        "quantity": 15,
        "minimum_threshold": 20,
        "unit": "reams"
      }
    ]
  }
}
```

### Get Purchase History
**GET** `/api/inventory/purchase-history`  
**Query:** `?item_id=1&date_from=2025-01-01&date_to=2025-12-31`

### Get Aging Report
**GET** `/api/inventory/aging-report`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": 1,
        "name": "A4 Paper",
        "quantity": 50,
        "oldest_stock_date": "2025-10-01",
        "age_in_days": 58
      }
    ]
  }
}
```

---

## 🏷️ Asset Management

**Roles:** `admin`, `admin_support` | **Requires:** Subscription

### List Assets
**GET** `/api/assets`  
**Query:** `?status=active&category=Electronics&assigned_to_user_id=2&search=laptop`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "assets": {
      "data": [
        {
          "id": 1,
          "asset_code": "ORG-2025-0001",
          "name": "Dell Laptop",
          "category": "Electronics",
          "purchase_date": "2025-01-15",
          "purchase_price": "1200.00",
          "vendor": "Dell Inc",
          "warranty_expiry": "2027-01-15",
          "serial_number": "DL123456",
          "model_number": "Latitude 5520",
          "status": "active",
          "assigned_to_user_id": 2,
          "assigned_to_location": "Room 101",
          "assignedToUser": {
            "id": 2,
            "name": "Jane Doe"
          }
        }
      ]
    },
    "summary": {
      "total": 50,
      "active": 45,
      "in_repair": 3,
      "retired": 2
    },
    "categories": ["Electronics", "Furniture"],
    "users": [...]
  }
}
```

### Create Asset
**POST** `/api/assets`

**Request:**
```json
{
  "name": "Dell Laptop",
  "category": "Electronics",
  "purchase_date": "2025-01-15",
  "purchase_price": "1200.00",
  "vendor": "Dell Inc",
  "warranty_expiry": "2027-01-15",
  "serial_number": "DL123456",
  "model_number": "Latitude 5520",
  "assigned_to_user_id": 2,
  "assigned_to_location": "Room 101",
  "description": "Office laptop",
  "notes": "For marketing team"
}
```

**Response (201):**
```json
{
  "success": true,
  "data": {
    "message": "Asset created successfully.",
    "asset": {
      "id": 1,
      "asset_code": "ORG-2025-0001",
      "name": "Dell Laptop",
      "status": "active"
    }
  }
}
```

**Note:** `asset_code` auto-generated (format: `ORG-YYYY-NNNN`). Status defaults to `active`.

### Get Asset
**GET** `/api/assets/{id}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "asset": {
      "id": 1,
      "asset_code": "ORG-2025-0001",
      "name": "Dell Laptop",
      "movements": [...]
    }
  }
}
```

### Update Asset
**PUT** `/api/assets/{id}`

**Request:** Same as create. Include `status` to change status:
```json
{
  "status": "in_repair",
  "status_change_reason": "Screen repair needed"
}
```

**Status Values:** `active`, `in_repair`, `retired`

### Delete Asset
**DELETE** `/api/assets/{id}`

### Log Asset Movement
**POST** `/api/assets/{id}/movement`

**Request:**
```json
{
  "movement_date": "2025-11-28",
  "movement_type": "assignment",
  "from_user_id": 1,
  "from_location": "Room 101",
  "to_user_id": 2,
  "to_location": "Room 102",
  "reason": "Department transfer",
  "notes": "Moving to marketing department"
}
```

**Movement Types:** `assignment`, `location_change`, `return`, `other`

**Response (201):**
```json
{
  "success": true,
  "data": {
    "message": "Asset movement logged successfully.",
    "movement": {
      "id": 1,
      "movement_type": "assignment",
      "movement_date": "2025-11-28"
    }
  }
}
```

---

## 🔧 Maintenance

**Roles:** `admin`, `admin_support` | **Requires:** Subscription

### List Maintenance Records
**GET** `/api/maintenance`  
**Query:** `?status=pending&type=scheduled&asset_id=1&upcoming=1`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "records": {
      "data": [
        {
          "id": 1,
          "asset_id": 1,
          "type": "scheduled",
          "scheduled_date": "2025-12-01",
          "completed_date": null,
          "status": "pending",
          "description": "Monthly maintenance check",
          "cost": "150.00",
          "service_provider": "Tech Services Inc",
          "asset": {
            "id": 1,
            "name": "Dell Laptop",
            "asset_code": "ORG-2025-0001"
          }
        }
      ]
    },
    "upcoming_count": 5
  }
}
```

### Create Maintenance Record
**POST** `/api/maintenance`

**Request:**
```json
{
  "asset_id": 1,
  "type": "scheduled",
  "scheduled_date": "2025-12-01",
  "description": "Monthly maintenance check",
  "cost": "150.00",
  "service_provider": "Tech Services Inc",
  "next_maintenance_date": "2026-01-01",
  "notes": "Regular maintenance"
}
```

**Types:** `scheduled`, `repair`, `inspection`, `other`  
**Status:** Defaults to `pending`

### Get Maintenance Record
**GET** `/api/maintenance/{id}`

### Update Maintenance Record
**PUT** `/api/maintenance/{id}`

**Request:** Include `status` to update:
```json
{
  "status": "completed",
  "completed_date": "2025-12-01",
  "cost": "150.00"
}
```

**Status Values:** `pending`, `in_progress`, `completed`, `cancelled`

### Delete Maintenance Record
**DELETE** `/api/maintenance/{id}`

### Get Upcoming Maintenance
**GET** `/api/maintenance/upcoming`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "records": [
      {
        "id": 1,
        "scheduled_date": "2025-12-01",
        "asset": {
          "name": "Dell Laptop",
          "asset_code": "ORG-2025-0001"
        },
        "description": "Monthly maintenance check"
      }
    ]
  }
}
```

**Note:** Returns maintenance scheduled within next 7 days with status `pending`.

---

## 📊 Dashboard

**Requires:** Subscription

### Get Dashboard Data
**GET** `/api/dashboard`  
**Auth:** Required

**Response (200):**
```json
{
  "success": true,
  "data": {
    "trial_info": {
      "is_on_trial": false,
      "trial_days_remaining": null
    },
    "financial_snapshot": {
      "current_month": "5000.00",
      "previous_month": "4500.00",
      "change": 11.11,
      "top_categories": [...]
    },
    "inventory_status": {
      "low_stock_count": 3,
      "low_stock_items": [...]
    },
    "asset_summary": {
      "total": 50,
      "active": 45,
      "in_repair": 3,
      "upcoming_maintenance_count": 5
    },
    "upcoming_maintenance": [...],
    "expense_charts": {
      "category_breakdown": [...],
      "monthly_trend": [...]
    }
  }
}
```

**Note:** Data varies by user role. Super Admin gets different stats.

---

## 💳 Subscription

**Requires:** Authentication

### Get Checkout Page
**GET** `/api/subscription/checkout`

### Create Checkout Session
**POST** `/api/subscription/checkout`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "checkout_url": "https://checkout.stripe.com/..."
  }
}
```

**Note:** 1-month free trial included. Payment method required but not charged until trial ends.

### Get Success Page
**GET** `/api/subscription/success`

---

## 🏢 Organization Management (Super Admin)

**Role:** `super_admin` | **No subscription required**

### List Organizations
**GET** `/api/super-admin/organizations`

### Create Organization
**POST** `/api/super-admin/organizations`

**Request:**
```json
{
  "name": "Acme Corp",
  "email": "admin@acme.com"
}
```

### Get Organization
**GET** `/api/super-admin/organizations/{id}`

### Update Organization
**PUT** `/api/super-admin/organizations/{id}`

### Delete Organization
**DELETE** `/api/super-admin/organizations/{id}`

### Resend Invitation
**POST** `/api/super-admin/organizations/{id}/resend-invitation`

---

## 📝 Response Format

**Success:**
```json
{
  "success": true,
  "data": { ... },
  "message": "Optional message"
}
```

**Error:**
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Validation error"]
  }
}
```

**Status Codes:** `200` Success, `201` Created, `400` Bad Request, `401` Unauthorized, `403` Forbidden, `404` Not Found, `422` Validation Error

---

## 🔒 Access Control

**Roles:**
- `super_admin` - Full access across all organizations
- `admin` - Full access within organization
- `finance` - Expense tracking module
- `admin_support` - Inventory, Assets, Maintenance modules
- `general_staff` - Read-only access

**Middleware:**
- `auth:sanctum` - API token authentication
- `subscribed` - Organization must be subscribed (Super Admin bypasses)
- `role:admin` - Admin role required
- `role_or_permission:admin|finance` - Admin OR Finance role

---

## 📌 Important Notes

- **Trial Period:** All yearly subscriptions include 1-month (30-day) free trial
- **Data Isolation:** All data is organization-scoped
- **Pagination:** Default 20 items per page, use `?page=2` for next page
- **Date Format:** `YYYY-MM-DD` (e.g., `"2025-11-28"`)
- **Price Format:** String with 2 decimals (e.g., `"150.00"`)
- **Organization Slug:** Auto-generated from organization name

---

**Last Updated:** November 2025  
**API Version:** 1.0
