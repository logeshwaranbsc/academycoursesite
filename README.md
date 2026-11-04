# Academy Courses Website — Core PHP Architecture

A professional, high-performance Academy Courses website and management system built with **Core PHP 8.5+**, **HTML5/CSS3/Vanilla JS**, **PostgreSQL (`pdo_pgsql`)**, and **Local Filesystem Storage**.

---

## Architecture Overview

- **Frontend:** Core PHP 8.5+, HTML5, CSS3, Vanilla JavaScript (Zero Node.js, zero React, zero Vite, zero build step).
- **Backend:** Core PHP MVC (Controllers, Repositories, Models, Services, Middleware, Router).
- **Database:** PostgreSQL direct connectivity via PHP Data Objects (`pdo_pgsql`).
- **File Storage:** Local server filesystem with strict MIME/extension verification (`public/uploads/` and `storage/private/documents/`).
- **Authentication:** PHP Session-based security with bcrypt hashing, session timeout, lockout throttling, and CSRF protection.
- **Web Server Compatibility:** Apache, Nginx, LiteSpeed, Hostinger Shared Hosting, cPanel.

---

## Directory Structure

```
AcademyCoursesSite/
├── app/
│   ├── Controllers/
│   │   ├── Admin/               # 12 Admin CRUD Controllers (Auth, Courses, Batches, etc.)
│   │   └── Public/              # 7 Public Page Controllers (Home, About, Courses, etc.)
│   ├── Core/                    # Framework Engine (Router, Request, Response, View, Config, Env)
│   ├── Helpers/                 # Helper functions (functions.php, response.php, upload.php)
│   ├── Middleware/              # AuthMiddleware, CsrfMiddleware
│   ├── Models/                  # Entity Data Models
│   ├── Repositories/            # PDO Database Query Repositories
│   └── Services/                # AuthService, DatabaseService, ImageUploadService, etc.
│
├── config/                      # app.php, database.php, storage.php
├── database/                    # schema.sql and migrations
├── public/                      # Web Root
│   ├── .htaccess                # Apache rewrite rules & security headers
│   ├── index.php                # Front controller & bootstrapping
│   ├── assets/
│   │   ├── css/style.css        # Responsive CSS design system
│   │   ├── js/main.js           # Vanilla JS interactive components
│   │   └── images/              # Logos and static graphics
│   ├── admin-assets/
│   │   └── admin.js             # Admin modals, file previews, AJAX actions
│   └── uploads/                 # Local media uploads (banners, courses, gallery, etc.)
│
├── resources/
│   └── views/
│       ├── layouts/             # public-layout.php, admin-layout.php
│       ├── components/          # Reusable UI partials (hero, courses, batches, forms)
│       ├── public/              # Public views (home, about, courses, contact, etc.)
│       └── admin/               # Admin views (dashboard, login, CRUD modules)
│
├── routes/
│   ├── web.php                  # Public and lead submission routes
│   └── admin.php                # Admin authentication and dashboard routes
│
├── storage/
│   ├── cache/
│   ├── logs/                    # Application error and activity logs
│   └── private/
│       └── documents/           # Private brochures, resumes, syllabi
│
├── .env                         # Environment variables (DB credentials, app settings)
├── .env.example
├── .htaccess                    # Root redirector to public/ for shared hosting
├── composer.json                # PSR-4 Autoloading configuration
└── README.md
```

---

## Required PHP Extensions

Ensure the following PHP extensions are enabled in `php.ini`:

- `pdo_pgsql` & `pgsql` (PostgreSQL Database Driver)
- `fileinfo` (MIME validation for secure file uploads)
- `session` (Admin authentication)
- `mbstring` (Multibyte string operations)
- `openssl` (CSRF token generation & secure hashing)
- `json` (AJAX payloads and JSON responses)
- `curl` (External integrations, webhooks)

---

## Getting Started

### 1. Environment Configuration

Copy `.env.example` to `.env` and fill in your PostgreSQL credentials:

```ini
APP_NAME="Meridian Academy"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=academy_db
DB_USERNAME=postgres
DB_PASSWORD=your_password
DB_SSLMODE=prefer

SESSION_LIFETIME=7200
```

### 2. Database Setup

Execute the complete SQL schema and seeds on your PostgreSQL database:

```bash
psql -h 127.0.0.1 -U postgres -d academy_db -f database/schema.sql
```

Default Admin Credentials:
- **Email:** `admin@meridianacademy.com`
- **Password:** `Admin@1234`
*(Change this immediately upon first login)*

### 3. Local Development

Run the PHP built-in server:

```bash
php -S localhost:8000 -t public
```

Access the website at `http://localhost:8000` and the admin panel at `http://localhost:8000/admin`.

---

## Hostinger / Shared Hosting Deployment

### Option A: Document Root set to `public/` (Recommended)
1. In hPanel, go to **Websites** -> **Manage** -> **General** / **Subdomains**.
2. Change the Document Root of your domain from `public_html` to `public_html/public`.
3. Upload all project files into `public_html`.
4. Ensure `storage/logs/`, `storage/cache/`, and `public/uploads/` have write permissions (`755` or `775`).

### Option B: Default `public_html` Document Root
1. The included root `.htaccess` file automatically rewrites all incoming traffic securely to the `public/` directory:
   ```apache
   <IfModule mod_rewrite.c>
       RewriteEngine On
       RewriteRule ^$ public/ [L]
       RewriteRule (.*) public/$1 [L]
   </IfModule>
   ```
2. Direct access to `/app`, `/config`, `/resources`, `/storage`, `/vendor`, and `.env` is blocked by server deny rules.

---

## Security Features

- **No Public Code Execution:** Business logic, views, models, and configuration files reside outside the document root.
- **CSRF Token Verification:** Validates tokens on all POST/PUT/DELETE requests via `CsrfMiddleware`.
- **Brute Force Protection:** 5 consecutive failed login attempts trigger an automatic 15-minute lockout.
- **Strict Upload Sanitization:** Validates real file MIME types using `finfo`, enforces extension whitelists, sanitizes filenames, and disallows executable execution inside `public/uploads/`.
- **SQL Injection Prevention:** 100% of database queries use PDO prepared statements with strict parameter binding.
