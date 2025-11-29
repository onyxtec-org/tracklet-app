# Tracklet - Complete User Manual & Testing Guide

**Multi-Organization Management System** | Version 1.0

---

## 📋 Table of Contents

1. [System Overview](#system-overview)
2. [Super Admin Guide](#super-admin-guide)
3. [Organization Guide](#organization-guide)
4. [Module Usage](#module-usage)
5. [Testing Scenarios](#testing-scenarios)
6. [Troubleshooting](#troubleshooting)

---

## 🎯 System Overview

### What is Tracklet?

Tracklet is a comprehensive multi-organization management system that helps organizations:
- **Track Expenses** - Monitor and analyze all business expenses
- **Manage Inventory** - Track consumable items and office supplies
- **Manage Assets** - Track fixed assets (laptops, furniture, equipment)
- **Schedule Maintenance** - Plan and track asset maintenance
- **Manage Users** - Control access with role-based permissions

### Key Features

✅ **Multi-Organization Support** - Each organization's data is completely isolated  
✅ **Role-Based Access Control** - 5 different roles with specific permissions  
✅ **Subscription Management** - Stripe integration with 1-month free trial  
✅ **Comprehensive Reporting** - Charts, exports, and analytics  
✅ **Mobile-Ready API** - Full REST API for mobile applications  
✅ **Real-time Dashboard** - Quick insights and overviews  

### User Roles

1. **Super Admin** - Full access across all organizations (system administrator)
2. **Admin** - Full access within their organization
3. **Finance** - Access to Expense Tracking module
4. **Admin Support** - Access to Inventory, Assets, and Maintenance modules
5. **General Staff** - Read-only access to relevant views

---

## 👨‍💼 Super Admin Guide

### Initial Setup

#### Step 1: Access Super Admin Account

1. **Default Super Admin Credentials:**
   - Email: `superadmin@tracklet.com` (or as configured in your seeder)
   - Password: Check your `SuperAdminSeeder.php` or `.env` file

2. **Login:**
   - Navigate to `/login`
   - Enter super admin credentials
   - Click "Sign In"

#### Step 2: Super Admin Dashboard

After login, you'll see the **Super Admin Dashboard** with:
- **Total Organizations** - Count of all registered organizations
- **Subscribed Organizations** - Organizations with active subscriptions
- **Trial Organizations** - Organizations currently on free trial
- **Active Subscriptions** - Count of active paid subscriptions
- **Total Users** - All users across all organizations (excluding super admins)
- **Pending Invitations** - Invitations not yet accepted
- **Expired Invitations** - Invitations that have expired
- **Recent Organizations** - Table of 5 most recently created organizations
- **Registration Sources** - Breakdown of invited vs self-registered organizations

### Super Admin Workflows

#### Workflow 1: Invite a New Organization

**Purpose:** Super Admin can invite organizations to join the platform.

**Steps:**

1. **Navigate to Organizations:**
   - Click on "Organizations" in the sidebar menu
   - Or go to `/super-admin/organizations`

2. **Create New Organization:**
   - Click "Add Organization" or "Create New" button
   - Fill in the form:
     - **Organization Name:** e.g., "Acme Corporation"
     - **Email:** Admin email for the organization (e.g., `admin@acme.com`)
   - Click "Create Organization"

3. **What Happens:**
   - Organization record is created
   - Invitation email is sent to the provided email
   - Invitation token is generated (valid for 7 days)
   - Organization status: **Pending** (until invitation is accepted)

4. **Invitation Email:**
   - Recipient receives email with invitation link
   - Link format: `/invitation/{token}`
   - Email contains organization name and expiration date

5. **Track Invitation Status:**
   - View organizations list to see invitation status:
     - **Pending** - Invitation sent, not yet accepted
     - **Joined** - Invitation accepted, user created account
     - **Expired** - Invitation expired (7 days)

6. **Resend Invitation (if needed):**
   - Click on organization
   - Click "Resend Invitation" button
   - New invitation email is sent

#### Workflow 2: View Organization Details

**Steps:**

1. **Access Organization List:**
   - Go to `/super-admin/organizations`
   - See all organizations in a table

2. **View Organization:**
   - Click on an organization name or "View" button
   - See details:
     - Organization information
     - Users in the organization
     - Invitation history
     - Subscription status

3. **Edit Organization:**
   - Click "Edit" button
   - Update organization details:
     - Name
     - Email
     - Phone
     - Address
     - Active status
   - Click "Save"

4. **Delete Organization:**
   - Click "Delete" button
   - Confirm deletion
   - ⚠️ **Warning:** This will delete all organization data

#### Workflow 3: Monitor System Statistics

**Steps:**

1. **View Dashboard:**
   - Dashboard shows real-time statistics
   - Refresh page to see updated numbers

2. **Key Metrics to Monitor:**
   - **Total Organizations** - Growth indicator
   - **Subscribed vs Trial** - Revenue potential
   - **Pending Invitations** - Follow-up needed
   - **Recent Organizations** - New signups

3. **Filter Organizations:**
   - Use search/filter options in organizations list
   - Filter by status, registration source, etc.

---

## 🏢 Organization Guide

### Getting Started

#### Option A: Organization Invited by Super Admin

**Step 1: Receive Invitation Email**

1. Check your email inbox
2. Look for email from Tracklet with subject: "Organization Invitation"
3. Email contains:
   - Organization name
   - Invitation link
   - Expiration date (7 days)

**Step 2: Accept Invitation**

1. **Click the invitation link** in the email
   - Link format: `https://yourdomain.com/invitation/{token}`
   - Or: `https://yourdomain.com/api/invitation/{token}`

2. **Accept Invitation Page:**
   - You'll see organization name
   - Form to create your account:
     - **Name:** Your full name
     - **Email:** Must match invitation email (pre-filled)
     - **Password:** Create a secure password (min 8 characters)
     - **Password Confirmation:** Re-enter password
   - Click "Accept Invitation & Create Account"

3. **What Happens:**
   - User account is created
   - You're automatically assigned **Admin** role
   - Organization status changes to **Joined**
   - You're logged in automatically
   - Redirected to subscription checkout

#### Option B: Self-Registration

**Step 1: Register Organization**

1. **Navigate to Registration:**
   - Go to `/register-organization`
   - Or click "Register" link on login page

2. **Fill Registration Form:**
   - **Organization Name:** Your company name (e.g., "Tech Solutions Inc")
   - **Your Name:** Your full name
   - **Email:** Your email address (will be admin email)
   - **Password:** Create secure password (min 8 characters)
   - **Confirm Password:** Re-enter password
   - **Privacy Policy:** Check the box to agree
   - Click "Register Organization"

3. **What Happens:**
   - Organization is created
   - Your admin account is created
   - You're automatically logged in
   - Redirected to subscription checkout

### Step 2: Complete Subscription

**Purpose:** Organizations must subscribe to access the platform features.

**Steps:**

1. **Subscription Checkout Page:**
   - After registration/invitation acceptance, you're redirected to `/subscription/checkout`
   - Page shows:
     - Organization name
     - Subscription information
     - "Subscribe Now" button

2. **Start Subscription:**
   - Click "Subscribe Now" or "Complete Subscription"
   - You'll be redirected to **Stripe Checkout**

3. **Stripe Checkout:**
   - Enter payment method (card details)
   - Review subscription details:
     - **Plan:** Annual subscription
     - **Trial:** 1-month (30 days) free trial
     - **Price:** As configured in Stripe
   - Click "Subscribe" or "Pay"

4. **What Happens:**
   - Payment method is saved (not charged during trial)
   - **1-month free trial starts immediately**
   - Full access to all features during trial
   - After 30 days, subscription auto-renews and charges

5. **Success:**
   - Redirected to success page
   - Then redirected to Dashboard
   - You now have full access!

### Step 3: First Login & Dashboard

**After Subscription:**

1. **Dashboard Overview:**
   - Navigate to `/dashboard` or click "Dashboard" in sidebar
   - You'll see:
     - **Financial Snapshot:**
       - Current month expenses
       - Previous month comparison
       - Top 5 expense categories
     - **Inventory Status:**
       - Low stock warnings count
       - List of items below threshold
     - **Asset Summary:**
       - Total assets
       - Active assets
       - Assets in repair
       - Retired assets
     - **Upcoming Maintenance:**
       - Maintenance scheduled in next 7 days
     - **Expense Charts:**
       - Category breakdown
       - Monthly trends

2. **Empty Dashboard:**
   - Initially, dashboard will be mostly empty
   - As you add data, charts and statistics populate
   - This is normal for new organizations

### Step 4: Manage Users

**Purpose:** Add team members and assign roles.

**Steps:**

1. **Navigate to User Management:**
   - Click "User Management" in sidebar
   - Or go to `/users`

2. **View Users:**
   - See list of all users in your organization
   - Initially, only you (admin) will be listed

3. **Add New User:**
   - Click "Add User" or "Create User" button
   - Fill in the form:
     - **Name:** User's full name
     - **Email:** User's email address (must be unique)
     - **Role:** Select from dropdown:
       - **Admin** - Full access
       - **Finance** - Expense tracking only
       - **Admin Support** - Inventory, Assets, Maintenance
       - **General Staff** - Read-only access
   - Click "Create User"

4. **What Happens:**
   - User account is created
   - **Random password is generated** (12 characters)
   - **Email is sent** to user with:
     - Login credentials
     - Temporary password
     - Link to login page
     - Instructions to change password
   - User's `must_change_password` flag is set to `true`

5. **User First Login:**
   - User receives email with password
   - User logs in with provided credentials
   - **Forced to change password** immediately
   - After password change, full access granted

6. **Edit User:**
   - Click on user name or "Edit" button
   - Update name, email, or role
   - Click "Save"

7. **Delete User:**
   - Click "Delete" button
   - Confirm deletion
   - ⚠️ User will lose access immediately

### Step 5: Start Using Modules

Now you can start using the various modules. See [Module Usage](#module-usage) section below.

---

## 📦 Module Usage

### 1. Expense Tracking Module

**Access:** Admin, Finance roles  
**Purpose:** Track and analyze business expenses

#### Adding Expenses

**Steps:**

1. **Navigate to Expenses:**
   - Click "Expense Tracking" in sidebar
   - Or go to `/expenses`

2. **View Expenses:**
   - See list of all expenses (initially empty)
   - Filter by:
     - Category
     - Date range
     - Vendor/Payee
     - Search by description

3. **Add New Expense:**
   - Click "Add Expense" button
   - Fill in the form:
     - **Category:** 
       - Select existing category from dropdown, OR
       - Select "Create New Category" and enter category name
     - **Date:** Expense date (required)
     - **Amount:** Expense amount (required, numeric)
     - **Vendor/Payee:** Who you paid (optional)
     - **Description:** What the expense was for (optional)
     - **Receipt/Invoice:** Upload file (PDF, JPG, PNG - max 5MB) (optional)
   - Click "Save Expense"

4. **Category Auto-Creation:**
   - If you select "Create New Category" and enter a name:
     - Category is created automatically if it doesn't exist
     - Category is assigned to your organization
     - You can use it for future expenses

#### Managing Categories

**Steps:**

1. **View Categories:**
   - Go to `/expenses/categories`
   - See all expense categories
   - System categories (seeded) are marked
   - Custom categories can be edited/deleted

2. **Create Category:**
   - Click "Add Category"
   - Enter:
     - **Name:** Category name (required, unique)
     - **Description:** Optional description
   - Click "Save"

3. **Edit Category:**
   - Click "Edit" on a category
   - Update name or description
   - ⚠️ System categories cannot be edited

4. **Delete Category:**
   - Click "Delete" on a category
   - ⚠️ Cannot delete if category has expenses
   - ⚠️ System categories cannot be deleted

#### Viewing Reports

**Steps:**

1. **Access Reports:**
   - Go to `/expenses/reports`
   - Or click "Reports" in Expenses section

2. **Select Report Type:**
   - **Monthly:** Select month and year
   - **Quarterly:** Select quarter and year
   - **Year-to-Date:** Select year

3. **View Report:**
   - See total expenses
   - Breakdown by category
   - List of all expenses in period
   - Comparison with previous period

4. **View Charts:**
   - Go to `/expenses/charts`
   - See visual representations:
     - **Bar Chart:** Category breakdown
     - **Line Chart:** Trend over time
     - **Pie Chart:** Category percentages

5. **Export Data:**
   - Go to `/expenses/export`
   - Select format: Excel or PDF
   - Download file with all expense data

### 2. Inventory Management Module

**Access:** Admin, Admin Support roles  
**Purpose:** Track consumable items and office supplies

#### Adding Inventory Items

**Steps:**

1. **Navigate to Inventory:**
   - Click "Inventory Management" in sidebar
   - Or go to `/inventory/items`

2. **View Items:**
   - See list of all inventory items
   - Filter by:
     - Category
     - Low stock items only
     - Search by name

3. **Add New Item:**
   - Click "Add Item" button
   - Fill in the form:
     - **Name:** Item name (e.g., "A4 Paper")
     - **Category:** Item category (e.g., "Office Supplies")
     - **Initial Quantity:** Starting stock quantity (this is set once when creating the item)
     - **Minimum Threshold:** Alert when stock drops below this
     - **Unit Price:** Price per unit
     - **Unit:** Unit of measurement (e.g., "reams", "boxes")
   - Click "Save Item"
   - Total price is calculated automatically (quantity × unit price)
   - ⓘ **Note:** Stock In/Out buttons are NOT in the create form. After creating the item, use the Stock In/Out buttons on the listing page or item detail page to track inventory changes.

#### Stock Transactions

**Purpose:** Track all inventory movements - both additions (stock in) and removals (stock out). **This is how you update inventory after creating an item.** The create form only sets the initial quantity - all subsequent changes are tracked through Stock In/Out transactions.

**Where to Find Stock In/Out Buttons:**
- **On the listing page:** Each item has Stock In (green) and Stock Out (red) buttons in the Actions column
- **On the item detail page:** Stock In and Stock Out buttons are at the top of the page

**Steps:**

1. **Log Stock In (Purchase/Addition):**
   - From the inventory listing page, click **"Stock In"** button (green arrow-down icon) for the item
   - OR go to item details page (`/inventory/items/{id}`) and click **"Stock In"** button
   - Fill in the form:
     - **Quantity:** Amount received/added
     - **Transaction Date:** Date of purchase/addition
     - **Reference:** Purchase order number or reference (optional)
     - **Vendor:** Supplier name (optional, only for stock in)
     - **Unit Price:** Purchase price per unit (optional, updates item's unit price)
     - **Notes:** Additional notes about the transaction
   - Click "Save Transaction"
   - ✅ Item quantity increases automatically
   - ✅ Transaction is logged in history

2. **Log Stock Out (Usage/Consumption):**
   - From the inventory listing page, click **"Stock Out"** button (red arrow-up icon) for the item
   - OR go to item details page (`/inventory/items/{id}`) and click **"Stock Out"** button
   - Fill in the form:
     - **Quantity:** Amount used/consumed/removed
     - **Transaction Date:** Date of usage
     - **Reference:** Reason for usage or reference (optional)
     - **Notes:** Additional notes about the usage
   - Click "Save Transaction"
   - ✅ Item quantity decreases automatically
   - ✅ Transaction is logged in history
   - ⚠️ **System validates sufficient stock** - If you try to remove more than available, the transaction will be rejected with an error message showing available quantity

**How It Works:**
- **Stock In:** Adds to current quantity (e.g., if current is 50 and you add 30, new quantity = 80)
- **Stock Out:** Subtracts from current quantity (e.g., if current is 50 and you remove 20, new quantity = 30)
- **All transactions are tracked** in the item's transaction history
- **Quantity syncs automatically** - the item's quantity field updates immediately after each transaction

3. **View Stock History & Summary:**
   - Go to item details page
   - Scroll to "Stock Transactions" section
   - **Summary Cards** show:
     - **Total Stock In:** Cumulative amount ever added
     - **Total Stock Out:** Cumulative amount ever removed
     - **Current Stock:** Current available quantity
   - **Transaction Table** shows:
     - All stock in/out transactions (sorted by date, newest first)
     - Type badges (green for Stock In, red for Stock Out)
     - Quantity with +/- indicators
     - Reference, Notes, and User who logged the transaction

#### Low Stock Alerts

**Steps:**

1. **View Low Stock Items:**
   - Go to `/inventory/low-stock`
   - Or click "Low Stock" in Inventory section
   - See all items where quantity ≤ minimum threshold

2. **Dashboard Alert:**
   - Dashboard shows count of low stock items
   - Click to see detailed list

3. **Set Thresholds:**
   - When creating/editing items, set appropriate minimum threshold
   - System automatically flags items below threshold

#### Reports

**Steps:**

1. **Purchase History:**
   - Go to `/inventory/purchase-history`
   - See all stock "in" transactions
   - Filter by item, date range
   - Useful for tracking purchases and vendors

2. **Aging Report:**
   - Go to `/inventory/aging-report`
   - See how long items have been in stock
   - Sorted by oldest stock first
   - Useful for FIFO/FEFO inventory management

### 3. Asset Management Module

**Access:** Admin, Admin Support roles  
**Purpose:** Track fixed assets (laptops, furniture, equipment)

#### Adding Assets

**Steps:**

1. **Navigate to Assets:**
   - Click "Asset Management" in sidebar
   - Or go to `/assets`

2. **View Assets:**
   - See list of all assets
   - Filter by:
     - Status (Active, In Repair, Retired)
     - Category
     - Assigned user
     - Search by name, asset code, serial number

3. **Add New Asset:**
   - Click "Add Asset" button
   - Fill in the form:
     - **Name:** Asset name (e.g., "Dell Laptop")
     - **Category:** Asset category (e.g., "Electronics")
     - **Purchase Date:** When asset was purchased
     - **Purchase Price:** Cost of asset
     - **Vendor:** Supplier name (optional)
     - **Warranty Expiry:** Warranty expiration date (optional)
     - **Serial Number:** Asset serial number (optional)
     - **Model Number:** Asset model (optional)
     - **Assigned To User:** Select user from dropdown (optional)
     - **Assigned To Location:** Location (e.g., "Room 101") (optional)
     - **Description:** Additional details (optional)
     - **Notes:** Internal notes (optional)
   - Click "Save Asset"
   - **Asset code is auto-generated** (format: `ORG-YYYY-NNNN`)

#### Asset Status Management

**Steps:**

1. **Change Asset Status:**
   - Go to asset details page
   - Click "Edit Asset"
   - Change **Status:**
     - **Active:** Asset is in use
     - **In Repair:** Asset is being repaired
     - **Retired:** Asset is no longer in use
   - If retiring, enter **Status Change Reason** (required)
   - Click "Save"
   - Status change is logged with timestamp

2. **View Asset Details:**
   - Click on asset name
   - See:
     - All asset information
     - Assignment history
     - Movement history
     - Maintenance records

#### Asset Movements

**Steps:**

1. **Log Asset Movement:**
   - Go to asset details page
   - Click "Log Movement"
   - Fill in:
     - **Movement Date:** Date of movement
     - **Movement Type:**
       - **Assignment:** Assigning to user/location
       - **Location Change:** Moving to different location
       - **Return:** Returning asset
       - **Other:** Other type of movement
     - **From User/Location:** Previous assignment
     - **To User/Location:** New assignment
     - **Reason:** Why asset is being moved
     - **Notes:** Additional notes
   - Click "Save"
   - If movement type is "assignment", asset assignment is updated automatically

2. **View Movement History:**
   - Go to asset details page
   - See "Movements" section
   - View all historical movements
   - See who moved it and when

### 4. Maintenance Management Module

**Access:** Admin, Admin Support roles  
**Purpose:** Schedule and track asset maintenance

#### Adding Maintenance Records

**Steps:**

1. **Navigate to Maintenance:**
   - Click "Repair & Maintenance" in sidebar
   - Or go to `/maintenance`

2. **View Maintenance Records:**
   - See list of all maintenance records
   - Filter by:
     - Status (Pending, In Progress, Completed, Cancelled)
     - Type (Scheduled, Repair, Inspection, Other)
     - Asset
     - Upcoming (next 7 days)

3. **Add New Maintenance:**
   - Click "Add Maintenance" button
   - Fill in the form:
     - **Asset:** Select asset from dropdown
     - **Type:**
       - **Scheduled:** Regular maintenance
       - **Repair:** Fixing something broken
       - **Inspection:** Routine inspection
       - **Other:** Other type
     - **Scheduled Date:** When maintenance is scheduled
     - **Description:** What needs to be done (required)
     - **Cost:** Estimated/actual cost (optional)
     - **Service Provider:** Who will perform maintenance (optional)
     - **Next Maintenance Date:** When next maintenance is due (optional)
     - **Notes:** Additional notes (optional)
   - Click "Save"
   - Status defaults to "Pending"

#### Updating Maintenance Status

**Steps:**

1. **Update Status:**
   - Go to maintenance record details
   - Click "Edit"
   - Change **Status:**
     - **Pending:** Not yet started
     - **In Progress:** Currently being worked on
     - **Completed:** Finished
     - **Cancelled:** Cancelled
   - If completed, **Completed Date** is auto-set to current date
   - Update **Cost** if actual cost differs
   - Add **Work Performed** notes
   - Click "Save"

2. **View Upcoming Maintenance:**
   - Go to `/maintenance/upcoming`
   - Or see on dashboard
   - See all maintenance scheduled in next 7 days with "Pending" status
   - Plan ahead for maintenance needs

---

## 🧪 Testing Scenarios

### Scenario 1: Complete Organization Onboarding

**Goal:** Test the full flow from invitation to active usage

**Steps:**

1. **Super Admin invites organization:**
   - Login as super admin
   - Create organization "Test Corp"
   - Email: `test@testcorp.com`
   - Verify invitation email sent

2. **Organization accepts invitation:**
   - Open invitation email
   - Click invitation link
   - Create account with password
   - Verify redirected to subscription

3. **Complete subscription:**
   - Go through Stripe checkout
   - Use test card: `4242 4242 4242 4242`
   - Verify trial starts
   - Verify access to dashboard

4. **Add users:**
   - Create Finance user
   - Create Admin Support user
   - Verify emails sent
   - Test user login and password change

5. **Add sample data:**
   - Add 5 expenses
   - Add 3 inventory items
   - Add 2 assets
   - Add 1 maintenance record

6. **Verify dashboard:**
   - Check all statistics populate
   - Verify charts show data
   - Check low stock alerts work

### Scenario 2: Expense Tracking Workflow

**Goal:** Test complete expense management

**Steps:**

1. **Create categories:**
   - Create 3 custom categories
   - Verify they appear in dropdown

2. **Add expenses:**
   - Add expense with existing category
   - Add expense with new category (auto-create)
   - Add expense with receipt upload
   - Verify all expenses appear in list

3. **Generate reports:**
   - View monthly report
   - View quarterly report
   - View YTD report
   - Verify calculations correct

4. **View charts:**
   - Check bar chart
   - Check line chart
   - Check pie chart
   - Verify data accuracy

5. **Export data:**
   - Export to Excel (if package installed)
   - Verify file downloads

### Scenario 3: Inventory Management Workflow

**Goal:** Test complete inventory management

**Steps:**

1. **Add items:**
   - Add 5 inventory items
   - Set different minimum thresholds
   - Verify total prices calculated

2. **Stock transactions:**
   - Log stock in for 3 items
   - Verify quantities increase
   - Log stock out for 2 items
   - Verify quantities decrease
   - Try stock out with insufficient stock (should fail)

3. **Low stock alerts:**
   - Reduce quantity below threshold
   - Verify appears in low stock list
   - Check dashboard alert

4. **Reports:**
   - View purchase history
   - View aging report
   - Verify data accuracy

### Scenario 4: Asset Management Workflow

**Goal:** Test complete asset management

**Steps:**

1. **Add assets:**
   - Add 3 assets
   - Assign 2 to users
   - Assign 1 to location
   - Verify asset codes generated

2. **Asset movements:**
   - Log movement for assigned asset
   - Change assignment
   - Verify movement history updated

3. **Status changes:**
   - Change asset to "In Repair"
   - Change asset to "Retired" (with reason)
   - Verify status changes logged

4. **Maintenance integration:**
   - Create maintenance for asset
   - Verify appears in asset details
   - Complete maintenance
   - Verify status updates

### Scenario 5: Role-Based Access Control

**Goal:** Verify permissions work correctly

**Steps:**

1. **Test Finance role:**
   - Login as Finance user
   - Verify can access Expenses
   - Verify cannot access Inventory/Assets
   - Verify can view dashboard

2. **Test Admin Support role:**
   - Login as Admin Support user
   - Verify can access Inventory, Assets, Maintenance
   - Verify cannot access Expenses
   - Verify can view dashboard

3. **Test General Staff role:**
   - Login as General Staff user
   - Verify read-only access
   - Verify cannot create/edit/delete

4. **Test Admin role:**
   - Login as Admin user
   - Verify full access to all modules
   - Verify can manage users

### Scenario 6: API Testing

**Goal:** Test mobile API endpoints

**Steps:**

1. **Authentication:**
   - Register via API: `POST /api/register`
   - Login via API: `POST /api/login`
   - Verify token received
   - Test token with: `GET /api/user`

2. **Expenses API:**
   - Get expenses: `GET /api/expenses`
   - Create expense: `POST /api/expenses`
   - Update expense: `PUT /api/expenses/{id}`
   - Delete expense: `DELETE /api/expenses/{id}`

3. **Inventory API:**
   - Get items: `GET /api/inventory/items`
   - Create item: `POST /api/inventory/items`
   - Log stock: `POST /api/inventory/items/{id}/stock`

4. **Assets API:**
   - Get assets: `GET /api/assets`
   - Create asset: `POST /api/assets`
   - Log movement: `POST /api/assets/{id}/movement`

5. **Use Swagger UI:**
   - Access `/api/documentation`
   - Test endpoints directly
   - Verify request/response formats

---

## 🎯 Benefits for Organizations

### 1. **Centralized Management**
- All business data in one place
- Easy access from anywhere (web + mobile)
- Real-time updates across team

### 2. **Financial Control**
- Track all expenses in one system
- Categorize and analyze spending
- Generate reports for accounting
- Export data for tax purposes

### 3. **Inventory Optimization**
- Never run out of supplies
- Low stock alerts prevent shortages
- Track purchase history
- Optimize inventory levels

### 4. **Asset Tracking**
- Know where assets are
- Track asset assignments
- Monitor asset status
- Plan maintenance schedules

### 5. **Cost Savings**
- Identify unnecessary expenses
- Optimize inventory levels
- Prevent asset loss
- Plan maintenance efficiently

### 6. **Team Collaboration**
- Multiple users with different roles
- Role-based access control
- Audit trail of all actions
- User activity tracking

### 7. **Reporting & Analytics**
- Visual charts and graphs
- Export to Excel/PDF
- Custom date ranges
- Category breakdowns

### 8. **Mobile Access**
- Full REST API
- Mobile app ready
- Access from anywhere
- Real-time synchronization

---

## 🔧 Troubleshooting

### Issue: Cannot Access Dashboard After Subscription

**Solution:**
- Clear browser cache
- Logout and login again
- Check subscription status in Stripe
- Verify webhook received (check logs)

### Issue: User Cannot Login

**Solution:**
- Verify user exists in system
- Check if user must change password
- Verify email matches exactly
- Check user's organization is subscribed

### Issue: Low Stock Alert Not Showing

**Solution:**
- Verify item quantity ≤ minimum threshold
- Check item belongs to your organization
- Refresh dashboard
- Check browser console for errors

### Issue: Cannot Delete Category

**Solution:**
- Category has expenses - remove expenses first
- System category - cannot delete system categories
- Check category is not in use

### Issue: Stock Out Fails

**Solution:**
- Check available quantity
- Verify quantity is sufficient
- Check for negative quantity validation

### Issue: API Returns 401 Unauthorized

**Solution:**
- Verify token is included in header: `Authorization: Bearer {token}`
- Check token hasn't expired
- Verify user is authenticated
- Check organization is subscribed

### Issue: Swagger UI Blank Screen

**Solution:**
- Clear browser cache
- Check browser console for errors
- Verify assets are loading
- Check CORS configuration
- Verify TrustProxies middleware configured

---

## 📞 Support & Resources

### Documentation Files

- **API_DOCUMENTATION.md** - Complete API reference
- **FEATURES_AND_API.md** - Quick API reference
- **TESTING_GUIDE.md** - Detailed testing instructions
- **README.md** - Setup and installation guide

### API Documentation

- **Swagger UI:** `/api/documentation`
- **Interactive testing** - Test APIs directly from browser
- **Request/Response examples** - See exact formats

### Demo Data

- **Demo Organization Seeder:**
  ```bash
  php artisan db:seed --class=DemoOrganizationSeeder
  ```
- **Login:** `admin@democompany.com`
- **Password:** `password`
- **Note:** Not subscribed (test subscription flow)

---

## ✅ Quick Checklist for New Organizations

- [ ] Register/accept invitation
- [ ] Complete subscription (Stripe checkout)
- [ ] Verify trial period started
- [ ] Access dashboard
- [ ] Add team members (users)
- [ ] Assign roles to users
- [ ] Add expense categories
- [ ] Add first expense
- [ ] Add inventory items
- [ ] Set minimum thresholds
- [ ] Add assets
- [ ] Schedule maintenance
- [ ] View reports
- [ ] Test mobile API (optional)

---

**Last Updated:** November 2025  
**Version:** 1.0  
**For Support:** Check logs in `storage/logs/laravel.log`

