# Tracklet API Documentation

**Base URL:** `https://yourdomain.com/api`  
**Version:** 1.0  
**Authentication:** Bearer Token (Mobile) or Session (Web)

**📚 Interactive API Documentation (Swagger UI):**  
Visit `/api/documentation` in your browser for interactive API documentation with:
- All endpoints listed with request/response formats
- Try it out functionality - test APIs directly from the browser
- Authentication support - add Bearer token and test authenticated endpoints
- Complete schema definitions for all models

---

## Quick Start

### Authentication
Include Bearer token in header:
```
Authorization: Bearer {token}
```

### Response Format
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
  "errors": { "field": ["Error"] }
}
```

**Status Codes:** `200` Success, `201` Created, `400` Bad Request, `401` Unauthorized, `403` Forbidden, `404` Not Found, `422` Validation Error

---

## Authentication Endpoints

### Password Reset Flow (API)

**For API (Mobile Apps):**
1. **Step 1**: `POST /api/forgot-password` - Request OTP by email
2. **Step 2**: `POST /api/verify-otp` - Verify the OTP and get verification token
3. **Step 3**: `POST /api/reset-password` - Reset password using verification token

**For Web:**
1. `POST /api/forgot-password` - Request password reset link by email
2. `POST /api/reset-password` - Reset password using token from email link

---

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

**Note:** 
- **API Endpoint**: Sends a 6-digit OTP to the user's email address
- **Web Endpoint**: Sends a password reset link (different behavior for web requests)
- OTP expires after 10 minutes
- Returns error if email doesn't exist (for API)
- OTP is sent via email and must be verified using `/api/verify-otp` endpoint

**Error Responses:**

**Response (404) - User Not Found:**
```json
{
  "success": false,
  "message": "We can't find a user with that email address."
}
```

**Response (500) - Email Send Failed:**
```json
{
  "success": false,
  "message": "Failed to send OTP. Please try again later."
}
```

### Verify OTP (API Only)
**POST** `/api/verify-otp`  
**Auth:** Not Required

**Request:**
```json
{
  "email": "user@example.com",
  "otp": "123456"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "OTP verified successfully. You can now reset your password.",
  "data": {
    "verification_token": "abc123..."
  }
}
```

**Note:** 
- Verifies the 6-digit OTP received via email
- Returns a `verification_token` that must be used in the reset-password endpoint
- OTP expires after 10 minutes
- OTP can only be verified once
- After verification, use the `verification_token` to reset password

**Error Responses:**

**Response (400) - OTP Expired:**
```json
{
  "success": false,
  "message": "OTP has expired. Please request a new OTP."
}
```

**Response (400) - OTP Already Used:**
```json
{
  "success": false,
  "message": "OTP has already been used. Please request a new OTP."
}
```

**Response (422) - Invalid OTP:**
```json
{
  "success": false,
  "message": "Invalid OTP. Please check and try again."
}
```

**Response (404) - User Not Found:**
```json
{
  "success": false,
  "message": "User not found."
}
```

### Reset Password (API - After OTP Verification)
**POST** `/api/reset-password`  
**Auth:** Not Required

**Request:**
```json
{
  "email": "user@example.com",
  "verification_token": "abc123...",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

**Note:** 
- **API Endpoint**: Uses `verification_token` received from verify-otp endpoint
- **Web Endpoint**: Uses token from reset link (different behavior for web requests)
- `verification_token` is received from the verify-otp endpoint after OTP verification
- Password must be at least 8 characters
- Verification token expires after 10 minutes
- Verification token can only be used once

**Response (200):**
```json
{
  "success": true,
  "message": "Your password has been reset successfully!"
}
```

**Error Responses:**

**Response (400) - Token Expired:**
```json
{
  "success": false,
  "message": "Verification token has expired. Please verify OTP again."
}
```

**Response (422) - Invalid Token:**
```json
{
  "success": false,
  "message": "Invalid verification token. Please verify OTP again."
}
```

**Response (404) - User Not Found:**
```json
{
  "success": false,
  "message": "User not found."
}
```

**Note for Web:** Web endpoints use reset link tokens instead of verification tokens.

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

## Profile Management

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
      "organization": {
        "id": 1,
        "name": "Acme Corp"
      },
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

**Note:** 
- Only `name` can be updated (email is read-only)
- Name must be at least 2 characters
- Name can only contain letters, numbers, and spaces

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

**Note:** 
- New password must be at least 8 characters
- New password must be different from old password
- Old password must be correct

**Response (200):**
```json
{
  "success": true,
  "message": "Password updated successfully"
}
```

---

## Roles

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

**Note:** 
- Organization admins see all roles except `super_admin`
- Super admin sees all roles including `super_admin`
- Use this endpoint to populate role dropdowns when creating/editing users

---

## User Management (Admin Only)

### List Users
**GET** `/api/users`  
**Auth:** Required | **Role:** `admin`

**Query:** `?search=john&role=finance&organization_id=1`

**Response:**
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
**Auth:** Required | **Role:** `admin`

**Request:**
```json
{
  "name": "Jane Doe",
  "email": "jane@acme.com",
  "role": "finance"
}
```

**Note:** Password auto-generated and sent via email. User must change on first login.

### Update User
**PUT** `/api/users/{id}`  
**Auth:** Required | **Role:** `admin`

### Delete User
**DELETE** `/api/users/{id}`  
**Auth:** Required | **Role:** `admin`

---

## Expense Tracking (Admin/Finance)

### List Expenses
**GET** `/api/expenses`  
**Auth:** Required | **Role:** `admin` or `finance`

**Query:** `?category_id=1&date_from=2025-01-01&date_to=2025-12-31&vendor=office&approval_status=pending`

**Note:** 
- Non-admin users only see their own expenses or approved expenses
- Admin users can filter by `approval_status` (pending, approved, rejected)

**Response:**
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
**Auth:** Required | **Role:** `admin` or `finance`

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

**Note:** 
- Use either `expense_category_id` (existing) or `category_name` (auto-creates if doesn't exist)
- **Admin expenses are auto-approved**
- **Non-admin expenses require admin approval** (status: `pending`)

**Response (Admin):**
```json
{
  "success": true,
  "message": "Expense created and approved successfully.",
  "data": {
    "expense": {
      "approval_status": "approved",
      "approved_by": 1,
      "approved_at": "2025-11-28T10:30:00.000000Z"
    }
  }
}
```

**Response (Non-Admin):**
```json
{
  "success": true,
  "message": "Expense created successfully. It is pending admin approval.",
  "data": {
    "expense": {
      "approval_status": "pending",
      "approved_by": null,
      "approved_at": null
    }
  }
}
```

### Approve Expense
**POST** `/api/expenses/{id}/approve`  
**Auth:** Required | **Role:** `admin` only

**Response:**
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
**Auth:** Required | **Role:** `admin` only

**Request:**
```json
{
  "rejection_reason": "Insufficient documentation"
}
```

**Response:**
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

### Update Expense
**PUT** `/api/expenses/{id}`  
**Auth:** Required | **Role:** `admin` or `finance`

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

**Important Notes:**
- **Admin users:** Can edit expenses without affecting approval status. The approval status remains unchanged.
- **Non-admin users (e.g., finance role):** When editing an expense, the approval status is **automatically reset to `pending`** and requires admin approval, regardless of the expense's previous approval status (approved, rejected, or pending).
- When non-admin edits, the following fields are automatically cleared: `approved_by`, `approved_at`, and `rejection_reason`.

**Response (Non-Admin Edit):**
```json
{
  "success": true,
  "message": "Expense updated successfully. It is pending admin approval.",
  "data": {
    "expense": {
      "id": 1,
      "approval_status": "pending",
      "approved_by": null,
      "approved_at": null,
      "rejection_reason": null
    }
  }
}
```

**Response (Admin Edit):**
```json
{
  "success": true,
  "message": "Expense updated successfully.",
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

### Delete Expense
**DELETE** `/api/expenses/{id}`  
**Auth:** Required | **Role:** `admin` only

**Important:** Only organization administrators can delete expenses. Finance role and other roles **cannot** delete expenses.

**Response (200):**
```json
{
  "success": true,
  "message": "Expense deleted successfully."
}
```

**Response (403) - Non-Admin Attempt:**
```json
{
  "success": false,
  "message": "Only administrators can delete expenses."
}
```

### Get Reports
**GET** `/api/expenses/reports`  
**Query:** `?period=monthly&month=11&year=2025`

### Get Charts Data
**GET** `/api/expenses/charts`  
**Query:** `?period=quarterly`

### List Categories
**GET** `/api/expenses/categories`  
**Auth:** Required | **Role:** `admin` or `finance`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "categories": [
      {
        "id": 1,
        "name": "Office Supplies",
        "description": "Office related expenses",
        "is_system": false,
        "organization_id": 1
      },
      {
        "id": 2,
        "name": "Utilities",
        "description": null,
        "is_system": true,
        "organization_id": 1
      }
    ]
  }
}
```

**Note:** 
- Returns all expense categories for the organization
- Categories are sorted alphabetically by name
- System categories (`is_system: true`) are predefined and cannot be modified or deleted

### Create Category
**POST** `/api/expenses/categories`  
**Auth:** Required | **Role:** `admin` or `finance`

**Request:**
```json
{
  "name": "Office Supplies",
  "description": "Office related expenses"
}
```

**Request Fields:**
- `name` (required, string, max 255): Category name. Must be unique within the organization.
- `description` (optional, string): Optional description for the category.

**Response (201):**
```json
{
  "success": true,
  "message": "Expense category created successfully.",
  "data": {
    "category": {
      "id": 1,
      "name": "Office Supplies",
      "description": "Office related expenses",
      "is_system": false,
      "organization_id": 1
    }
  }
}
```

**Error Responses:**

**Response (422) - Category Name Already Exists:**
```json
{
  "success": false,
  "message": "The name has already been taken.",
  "errors": {
    "name": ["The name has already been taken."]
  }
}
```

**Response (403) - User Not in Organization:**
```json
{
  "success": false,
  "message": "User does not belong to an organization."
}
```

**Important Notes:**
- Category names must be unique within your organization
- Created categories have `is_system: false` and can be modified or deleted (if no expenses exist)
- You can also create categories automatically when creating expenses using the `category_name` field in the expense creation endpoint

### Update Category
**PUT** `/api/expenses/categories/{id}`  
**Auth:** Required | **Role:** `admin` or `finance`

**Request:**
```json
{
  "name": "Office Supplies Updated",
  "description": "Updated description"
}
```

**Request Fields:**
- `name` (required, string, max 255): Category name. Must be unique within the organization.
- `description` (optional, string): Optional description for the category.

**Response (200):**
```json
{
  "success": true,
  "message": "Expense category updated successfully.",
  "data": {
    "category": {
      "id": 1,
      "name": "Office Supplies Updated",
      "description": "Updated description",
      "is_system": false,
      "organization_id": 1
    }
  }
}
```

**Error Responses:**

**Response (403) - System Category Cannot Be Modified:**
```json
{
  "success": false,
  "message": "System categories cannot be modified."
}
```

**Response (422) - Category Name Already Exists:**
```json
{
  "success": false,
  "message": "The name has already been taken.",
  "errors": {
    "name": ["The name has already been taken."]
  }
}
```

**Note:** System categories (`is_system: true`) cannot be updated.

### Delete Category
**DELETE** `/api/expenses/categories/{id}`  
**Auth:** Required | **Role:** `admin` or `finance`

**Response (200):**
```json
{
  "success": true,
  "message": "Expense category deleted successfully."
}
```

**Error Responses:**

**Response (400) - Category Has Existing Expenses:**
```json
{
  "success": false,
  "message": "Cannot delete category with existing expenses."
}
```

**Response (403) - System Category Cannot Be Deleted:**
```json
{
  "success": false,
  "message": "System categories cannot be deleted."
}
```

**Important Notes:**
- Categories with existing expenses cannot be deleted
- System categories (`is_system: true`) cannot be deleted
- Only custom categories created by your organization can be deleted

---

## Inventory Management (Admin/Admin Support)

**⚠️ Note:** Super Admin cannot access inventory management. Only organization users with `admin` or `admin_support` roles can access these endpoints.

### List Items
**GET** `/api/inventory/items`  
**Auth:** Required | **Role:** `admin` or `admin_support` | **Not Available:** `super_admin`

**Query:** `?category=Office Supplies&low_stock=1&search=paper`

**Response:**
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
**Auth:** Required | **Role:** `admin` or `admin_support` | **Not Available:** `super_admin`

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

**Note:** `quantity` is the **initial stock quantity** when creating the item. Use Stock In/Out transactions to track changes after creation.

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
**Auth:** Required | **Role:** `admin` or `admin_support`

**Response:**
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
**Auth:** Required | **Role:** `admin` or `admin_support`

### Delete Item
**DELETE** `/api/inventory/items/{id}`  
**Auth:** Required | **Role:** `admin` or `admin_support`

### Log Stock Transaction (Stock In/Out)
**POST** `/api/inventory/items/{id}/stock`  
**Auth:** Required | **Role:** `admin` or `admin_support` | **Not Available:** `super_admin`

**Purpose:** Log stock in (additions) or stock out (removals) transactions. **Automatically updates the item's quantity in real-time.** This is separate from the create form - you create the item first with an initial quantity, then use this endpoint to track all subsequent changes.

**Request Body:**
```json
{
  "type": "in",              // Required: "in" or "out"
  "quantity": 50,            // Required: integer, min: 1
  "transaction_date": "2025-11-28",  // Required: date (YYYY-MM-DD)
  "reference": "PO-12345",   // Optional: Purchase order, usage reason, etc.
  "notes": "New purchase",   // Optional: Additional notes
  "unit_price": "5.00",      // Optional: For stock in only - updates item unit price
  "vendor": "Office Supplies Co"  // Optional: For stock in only - supplier name
}
```

**Stock In Example:**
```json
{
  "type": "in",
  "quantity": 100,
  "transaction_date": "2025-11-28",
  "reference": "PO-12345",
  "notes": "Monthly office supplies order",
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
  "notes": "Distributed to marketing department"
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
      "notes": "New purchase",
      "user": {"id": 1, "name": "John Doe"}
    },
    "item": {
      "id": 1,
      "name": "A4 Paper",
      "quantity": 150,  // Updated quantity
      "unit_price": "5.00"
    }
  }
}
```

**Important Notes:**
- **Stock In (`type: "in"`):** Adds to quantity. Can include `unit_price` and `vendor`.
- **Stock Out (`type: "out"`):** Subtracts from quantity. Validates sufficient stock - will return error if quantity would go negative.
- **Error Response (400):** If stock out exceeds available quantity:
  ```json
  {
    "success": false,
    "message": "Failed to log stock transaction: Insufficient stock. Available: 20"
  }
  ```

### Get Stock Transactions
**GET** `/api/inventory/items/{id}/transactions`  
**Auth:** Required | **Role:** `admin` or `admin_support`

**Query Parameters:**
- `type` (optional): Filter by transaction type - `"in"` or `"out"`
- `date_from` (optional): Filter from date (YYYY-MM-DD)
- `date_to` (optional): Filter to date (YYYY-MM-DD)

**Example:** `/api/inventory/items/1/transactions?type=out&date_from=2025-11-01&date_to=2025-11-30`

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
        "user": {
          "id": 1,
          "name": "John Doe"
        }
      },
      {
        "id": 2,
        "type": "out",
        "quantity": 25,
        "transaction_date": "2025-11-29",
        "reference": "Used for printing",
        "notes": "Marketing department",
        "unit_price": null,
        "vendor": null,
        "user": {
          "id": 2,
          "name": "Jane Smith"
        }
      }
    ]
  }
}
```

**Note:** Transactions are sorted by date (newest first). Use query parameters to filter by type or date range.

### Get Low Stock Items
**GET** `/api/inventory/low-stock`  
**Auth:** Required | **Role:** `admin` or `admin_support`

**Response:**
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
**Auth:** Required | **Role:** `admin` or `admin_support`

**Query:** `?item_id=1&date_from=2025-01-01&date_to=2025-12-31`

### Get Aging Report
**GET** `/api/inventory/aging-report`  
**Auth:** Required | **Role:** `admin` or `admin_support`

**Response:**
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

## Asset Management (Admin/Admin Support)

### List Assets
**GET** `/api/assets`  
**Auth:** Required | **Role:** `admin` or `admin_support`

**Query:** `?status=active&category=Electronics&assigned_to_user_id=2&search=laptop`

**Response:**
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
**Auth:** Required | **Role:** `admin` or `admin_support`

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
**Auth:** Required | **Role:** `admin` or `admin_support`

**Response:**
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
**Auth:** Required | **Role:** `admin` or `admin_support`

**Request:** Same fields as create. Include `status` to change status:
```json
{
  "status": "in_repair",
  "status_change_reason": "Screen repair needed"
}
```

**Status Values:** `active`, `in_repair`, `retired`

### Delete Asset
**DELETE** `/api/assets/{id}`  
**Auth:** Required | **Role:** `admin` or `admin_support`

### Log Asset Movement
**POST** `/api/assets/{id}/movement`  
**Auth:** Required | **Role:** `admin` or `admin_support`

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

## Maintenance (Admin/Admin Support)

### List Maintenance Records
**GET** `/api/maintenance`  
**Auth:** Required | **Role:** `admin` or `admin_support`

**Query:** `?status=pending&type=scheduled&asset_id=1&upcoming=1`

**Response:**
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
**Auth:** Required | **Role:** `admin` or `admin_support`

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
**Auth:** Required | **Role:** `admin` or `admin_support`

### Update Maintenance Record
**PUT** `/api/maintenance/{id}`  
**Auth:** Required | **Role:** `admin` or `admin_support`

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
**Auth:** Required | **Role:** `admin` or `admin_support`

### Get Upcoming Maintenance
**GET** `/api/maintenance/upcoming`  
**Auth:** Required | **Role:** `admin` or `admin_support`

**Response:**
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

## Dashboard

### Get Dashboard Data
**GET** `/api/dashboard`  
**Auth:** Required

**Response:**
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

## Subscription

### Get Checkout Page
**GET** `/api/subscription/checkout`  
**Auth:** Required

### Create Checkout Session
**POST** `/api/subscription/checkout`  
**Auth:** Required

**Response:**
```json
{
  "success": true,
  "data": {
    "checkout_url": "https://checkout.stripe.com/..."
  }
}
```

**Note:** 1-month free trial included. Payment method required but not charged until trial ends.

---

## Important Notes

### Trial Period
- All yearly subscriptions include **1-month (30-day) free trial**
- Full access during trial
- No charges during trial
- Auto-renewal after trial ends

### Organization Slug
- Auto-generated from organization name
- No need to provide in requests

### Data Isolation
- All data is organization-scoped
- Users can only access their organization's data
- Super Admin can access all organizations

### Pagination
- Default: 20 items per page
- Use `?page=2` for next page
- Response includes `links` and `meta` for pagination

### Date Formats
- Use `YYYY-MM-DD` format for dates
- Example: `"2025-11-28"`

### Price/Amount Formats
- Use string format: `"150.00"`
- Always include 2 decimal places

---

## 📚 Interactive API Documentation

### Swagger UI

Visit `/api/documentation` in your browser for interactive API documentation powered by Swagger UI.

**Features:**
- Complete list of all API endpoints
- Request/response examples for each endpoint
- Try it out - Test APIs directly from the browser
- Authentication support - Add Bearer token to test authenticated endpoints
- Schema definitions for all models
- Search and filter endpoints

**Access:** `GET /api/documentation` (No authentication required)

**Note:** The documentation is auto-generated from code annotations. Regenerate after code changes using:
```bash
php artisan l5-swagger:generate
```

---

**Last Updated:** November 2025  
**API Version:** 1.0
