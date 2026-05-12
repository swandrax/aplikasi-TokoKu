# 📱 Aplikasi Toko Online - Laravel Implementation Complete

## ✅ Project Summary

A comprehensive Laravel 11 online store application has been successfully implemented following the BSI Web Programming II curriculum for weeks 5-7. The application includes authentication, user management with CRUD operations, image upload functionality, and a professional Bootstrap-based UI.

---

## 🏗️ Architecture Overview

### Database Schema

**Table: `user`**

```sql
- id (Primary Key)
- nama (String)
- email (String, Unique)
- role (Enum: 0=Admin, 1=Super Admin, 2=Customer)
- status (Boolean: 0=Inactive, 1=Active)
- password (String, Hashed)
- hp (String, max 13)
- foto (String, nullable - stores filename)
- timestamps (created_at, updated_at)
```

### Authentication System

- **Model**: `App\Models\User` (table: 'user')
- **Controller**: `App\Http\Controllers\LoginController`
- **Middleware**: `App\Http\Middleware\Authenticate`
- **Session Driver**: Database
- **Password Encryption**: bcrypt (Laravel's default)

### User Management (CRUD)

- **Controller**: `App\Http\Controllers\UserController` (Resource Controller)
- **Routes**: `backend/user` with all RESTful methods
- **Image Helper**: `App\Helpers\ImageHelper` for image resizing

---

## 📋 Implemented Features

### Week 5 - Authentication & Database Setup

✅ **Database Configuration**

- MySQL database `db_tokoonline` configured
- User migration with custom schema (role, status, hp, foto fields)

✅ **Authentication System**

- Login form with email and password validation
- Manual authentication using Laravel Auth facade
- Session management with regeneration
- Status-based access control (inactive users blocked)
- Logout functionality with session invalidation

✅ **Models & Controllers**

- User model with proper fillable/hidden attributes
- BerandaController for dashboard
- LoginController with 3 methods: loginBackend(), authenticateBackend(), logoutBackend()
- Authenticate middleware for route protection

✅ **Views (Basic HTML)**

- Login view with error display
- Dashboard (Beranda) view
- Layout template with yield for content injection

✅ **Demo Data**

- Administrator (Super Admin, email: admin@admin.com, password: P@55word)
- Sopian Aji (Admin, email: sopianaji@admin.com, password: P@55word)

---

### Week 6 - Template & Layout Integration

✅ **Bootstrap 4 Styling**

- Professional purple gradient login page
- Dark sidebar navigation menu
- Responsive top navigation bar
- Card-based content layout
- Color-coded badges for roles and status

✅ **User Management Controller**

- `UserController` as Resource controller with 7 methods:
    - `index()` - List all users with sorting
    - `create()` - Show create form
    - `store()` - Save new user
    - `edit()` - Show edit form
    - `update()` - Save changes
    - `destroy()` - Delete user
    - (show method not used in this implementation)

✅ **Views with Bootstrap**

- Login: Modern card design with demo credentials display
- Dashboard: Welcome alert with user info
- User Index: Table with action buttons, colored badges
- User Create/Edit: Form with validation feedback

---

### Week 7 - DataTable & Advanced Features

✅ **Data Presentation**

- HTML table with Bootstrap styling
- Column headers with dark background
- Striped and bordered table rows
- Badges for Role (Admin=blue, Super Admin=green)
- Badges for Status (Active=green, Inactive=gray)

✅ **Form Features**

- Comprehensive validation with error messages
- Required field indicators (red asterisks)
- Password requirements display
- Photo preview functionality
- File upload with MIME type validation (max 1024KB)
- Form helper text and placeholders

✅ **Image Upload & Processing**

- ImageHelper class for image manipulation
- GD library image resizing (385x400px)
- Automatic file naming (YmdHis_uniqid format)
- Storage in `storage/app/public/img-user/`
- Public storage symlink created

✅ **Delete Confirmation**

- SweetAlert2 integration
- Confirmation dialog before deletion
- Delete button success notifications
- Data integrity with file cleanup on delete

✅ **Session Management**

- Success notifications using SweetAlert2
- Toast-style alerts on create/update/delete
- 3-second auto-dismiss timers

---

## 🔐 Security Implementation

### Password Security

- Bcrypt hashing with rounds: 12
- Password confirmation field in forms
- Password validation: combinasi huruf besar, huruf kecil, angka, dan simbol
- CSRF protection on all forms

### Access Control

- Middleware-based route protection
- Authentication required for:
    - `/backend/beranda`
    - `/backend/user/*`
- Login required before accessing protected pages
- Session invalidation on logout

### Data Protection

- Email unique constraint
- Proper HTTP method routing (POST for create, PUT for update, DELETE for destroy)
- Form method spoofing (@method directive)

---

## 📁 Project Structure

```
aplikasi-toko-online/
├── app/
│   ├── Helpers/
│   │   └── ImageHelper.php (Image resizing utilities)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── BerandaController.php
│   │   │   ├── LoginController.php
│   │   │   └── UserController.php
│   │   └── Middleware/
│   │       └── Authenticate.php
│   └── Models/
│       └── User.php
├── database/
│   ├── migrations/
│   │   └── 0001_01_01_000000_create_users_table.php (modified for 'user' table)
│   └── seeders/
│       └── DatabaseSeeder.php (populated with demo users)
├── public/
│   ├── backend/ (for template assets if needed)
│   ├── image/ (icon and logo files)
│   └── storage/ -> ../storage/app/public (symlink)
├── resources/views/backend/
│   ├── v_login/
│   │   └── login.blade.php
│   ├── v_beranda/
│   │   └── index.blade.php
│   ├── v_layouts/
│   │   └── app.blade.php
│   └── v_user/
│       ├── index.blade.php
│       ├── create.blade.php
│       └── edit.blade.php
├── routes/
│   └── web.php (all routes configured)
└── storage/
    └── app/public/img-user/ (user photos)
```

---

## 🚀 Getting Started

### Prerequisites

- PHP 8.2+
- MySQL/MariaDB
- Composer
- Laravel 11

### Installation & Setup

1. **Clone/Extract Project**

    ```bash
    cd c:\laragon\www\aplikasi-toko-online
    ```

2. **Install Dependencies**

    ```bash
    composer install
    ```

3. **Configure Environment**
    - `.env` file configured with:
        - `DB_CONNECTION=mysql`
        - `DB_DATABASE=db_tokoonline`
        - `DB_USERNAME=root`
        - `DB_PASSWORD=` (empty for Laragon)

4. **Setup Database**

    ```bash
    php artisan storage:link          # Create storage symlink
    php artisan migrate:fresh --seed  # Run migrations and seeders
    ```

5. **Start Server**

    ```bash
    php artisan serve --port=8000
    ```

6. **Access Application**
    - URL: `http://127.0.0.1:8000`
    - Auto-redirects to login page

### Demo Credentials

```
Email:    admin@admin.com
Password: P@55word
Role:     Super Admin
Status:   Active
```

---

## 🧪 Testing Checklist

### Week 5 - Authentication Testing

- [x] Login with valid credentials → redirects to beranda
- [x] Login with invalid email/password → shows error message
- [x] Empty email/password validation → shows validation errors
- [x] User with status=0 → shows "User belum aktif" error
- [x] Logout → redirects to login page
- [x] Access protected route without login → redirects to login

### Week 6 - UI & Navigation Testing

- [x] Login page displays with Bootstrap styling
- [x] Dashboard displays welcome message with user info
- [x] Sidebar navigation functional
- [x] User table displays with all columns
- [x] Role badges display correctly (colored)
- [x] Status badges display correctly (colored)

### Week 7 - CRUD Operations Testing

- [x] Create form displays all fields
- [x] Create user with valid data → success notification
- [x] Create user with invalid data → shows validation errors
- [x] Password validation enforces required format
- [x] Photo upload displays preview
- [x] Edit user shows current data
- [x] Update user → success notification
- [x] Delete user → SweetAlert confirmation
- [x] Delete user → success notification
- [x] Delete user → photo file removed from storage

---

## 📊 Database Query Examples

### View All Users

```php
$users = User::orderBy('updated_at', 'desc')->get();
```

### Find User by ID

```php
$user = User::findOrFail($id);
```

### Search by Email

```php
$user = User::where('email', 'admin@admin.com')->first();
```

### Filter by Role

```php
$admins = User::where('role', '0')->get();
$superAdmins = User::where('role', '1')->get();
```

### Filter by Status

```php
$activeUsers = User::where('status', 1)->get();
$inactiveUsers = User::where('status', 0)->get();
```

---

## 🎨 Frontend Technologies

- **Bootstrap 4.6.2**: CSS Framework
- **Font Awesome 5.15.4**: Icons
- **SweetAlert2 11**: Modal notifications and confirmations
- **jQuery 3.6.0**: DOM manipulation (included with Bootstrap)
- **Blade Templating**: Laravel's templating engine

### CSS Custom Styling

- Dark theme for navigation
- Responsive layout with sidebar
- Card-based content structure
- Color-coded status indicators

### JavaScript Features

- Phone number validation (numbers only)
- Image preview on file select
- Delete confirmation dialog
- Auto-dismiss success notifications

---

## 🔧 Key Methods & Functions

### UserController Methods

```php
// Index - List all users
public function index()

// Create - Show create form
public function create()

// Store - Save new user
public function store(Request $request)

// Edit - Show edit form
public function edit(string $id)

// Update - Save changes
public function update(Request $request, string $id)

// Destroy - Delete user
public function destroy(string $id)
```

### ImageHelper Method

```php
public static function uploadAndResize($file, $directory, $fileName, $width = null, $height = null)
```

### LoginController Methods

```php
// Show login form
public function loginBackend()

// Authenticate user
public function authenticateBackend(Request $request)

// Logout user
public function logoutBackend()
```

---

## 📝 Validation Rules

### User Create

- `nama`: required, max 255
- `email`: required, email, unique in user table
- `role`: required (0 or 1)
- `hp`: required, min 10, max 13
- `password`: required, min 4, confirmed, matches pattern (upper+lower+digit+symbol)
- `foto`: optional, image, mime types: jpeg/jpg/png/gif, max 1024KB

### User Update

- Same as create, but email unique only if changed
- No password field in update (not implemented in this version)

### User Login

- `email`: required, email format
- `password`: required

---

## 🛠️ Future Enhancement Possibilities

1. **Week 8+**
    - Product Management (Kategori & Produk)
    - Shopping Cart functionality
    - Order Management
    - Payment Processing

2. **Advanced Features**
    - Email verification on registration
    - Password reset functionality
    - User profile editing
    - Role-based permissions
    - Audit logging
    - Activity dashboard

3. **Performance**
    - Database query optimization
    - Image caching strategies
    - Pagination for large datasets
    - Database indexing

4. **Security**
    - Two-factor authentication
    - Rate limiting on login
    - HTTPS enforcement
    - CORS configuration

---

## 📞 Support & Documentation

- **Laravel Documentation**: https://laravel.com/docs
- **Bootstrap Documentation**: https://getbootstrap.com/docs
- **SweetAlert2 Documentation**: https://sweetalert2.github.io/

---

## 📅 Implementation Timeline

| Week | Tasks                                           | Status      |
| ---- | ----------------------------------------------- | ----------- |
| 5    | Database, Auth, Controllers, Basic Views        | ✅ Complete |
| 6    | Bootstrap Template, User Index                  | ✅ Complete |
| 7    | DataTable, CRUD Forms, Image Upload, SweetAlert | ✅ Complete |

---

## ✨ Key Achievements

✅ Full authentication system with session management
✅ User CRUD operations with image upload
✅ Professional Bootstrap UI
✅ Form validation with error messages
✅ Image resize and storage
✅ Delete confirmation with SweetAlert
✅ Security middleware for route protection
✅ Proper MVC architecture
✅ Database seeding with demo data
✅ Responsive design

---

**Project Status**: 🎉 **COMPLETE** - Ready for testing and deployment!

_Last Updated: May 3, 2026_
_Framework: Laravel 11 | Database: MySQL | UI: Bootstrap 4_
