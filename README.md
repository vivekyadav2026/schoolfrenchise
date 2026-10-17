# Drishti Computer Education - Institute & Franchise Management System

**Drishti Computer Education** is an enterprise-grade, multi-tenant Education ERP and Franchise Management Platform built with Laravel 11, PHP 8.2+, MySQL, and modern responsive UI components. It powers nationwide operations including public web presence, online admissions, student self-service portal, authorized franchise study centre management, central board governance, automated certificate generation with cryptographic QR verification, and payment gateway ledger reconciliation.

---

## Architecture Overview

```
                      ┌────────────────────────────────────────┐
                      │    Drishti Multi-Portal Ecosystem      │
                      └──────────────────┬─────────────────────┘
                                         │
     ┌──────────────────┬────────────────┼─────────────────┬──────────────────┐
     ▼                  ▼                ▼                 ▼                  ▼
┌──────────────┐ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐ ┌────────────────┐
│Public Website│ │Student Portal│ │Franchise Hub │ │Central Board │ │Online Payments │
│  (23 Pages)  │ │ (/student/*) │ │  (/centre/*) │ │  (/admin/*)  │ │  & Verification│
└──────────────┘ └──────────────┘ └──────────────┘ └──────────────┘ └────────────────┘
     │                  │                │                 │                  │
     └──────────────────┴────────────────┴─────────────────┴──────────────────┘
                                         │
                      ┌──────────────────▼─────────────────────┐
                      │   Core Security & Database Layer       │
                      │  - Multi-Guard RBAC (5 Roles)          │
                      │  - Strict Tenant IDOR Isolation        │
                      │  - HMAC-SHA256 Signature Webhooks      │
                      │  - SHA-256 QR Verification Tokens      │
                      │  - MySQL InnoDB Database Transactions  │
                      └────────────────────────────────────────┘
```

---

## Technology Stack

- **Framework:** Laravel 11.x
- **Language:** PHP 8.2+
- **Database:** MySQL 8.0+ / MariaDB (InnoDB, `utf8mb4_unicode_ci`)
- **Frontend:** Bootstrap 5.3.3, jQuery 3.7.1, FontAwesome 6.5.1, Plus Jakarta Sans
- **Security:** HMAC-SHA256 signature verification, timing-safe hashing, defense-in-depth HTTP security headers, role-based middleware gating, anti-IDOR policies.
- **Testing:** PHPUnit / Pest with 28 automated test suites (**264 tests, 1,206 assertions, 100% green**).

---

## System Requirements

| Component | Minimum Requirement | Recommended |
|---|---|---|
| **PHP** | 8.2 | 8.2 or 8.3 |
| **PHP Extensions** | `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd`, `curl` | All enabled |
| **Web Server** | Apache 2.4+ (with `mod_rewrite`) or Nginx 1.20+ | Apache / Nginx |
| **Database** | MySQL 8.0+ or MariaDB 10.5+ | MySQL 8.0 |
| **Composer** | Composer 2.x | Composer 2.7+ |

---

## Local Installation Guide

### 1. Clone or Copy the Repository
```bash
cd c:\xampp\htdocs\schoolfrenchise
```

### 2. Install Composer Dependencies
```bash
composer install --optimize-autoloader
```

### 3. Environment Configuration
Copy the provided `.env.example` template to `.env`:
```bash
cp .env.example .env
```

Generate the unique application encryption key:
```bash
php artisan key:generate
```

Configure your local MySQL database connection in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=frenchise
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Create the Database
In your MySQL console or phpMyAdmin:
```sql
CREATE DATABASE IF NOT EXISTS frenchise CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Run Database Migrations & Master Seeders
Execute the migrations to provision all 20+ tables:
```bash
php artisan migrate
```

Seed initial geographical master data, courses, sample authorized centres, verifiable certificates, and administrative permissions:
```bash
php artisan db:seed --class=GeographicSeeder
php artisan db:seed --class=SuperAdminSeeder
```

*(Optional) Create or reset the Super Administrator account directly via CLI:*
```bash
php artisan app:create-super-admin
```

### 6. Create Storage Symlink
Link the `storage/app/public` disk to `public/storage` for serving uploads (student photos, identity proofs, study materials, receipts):
```bash
php artisan storage:link
```

### 7. Start the Local Server
If running via XAMPP/Apache:
Access the application at `http://localhost/schoolfrenchise/public` or via virtual host `http://schoolfrenchise.test`.

If running via Artisan CLI:
```bash
php artisan serve
```
Access at `http://127.0.0.1:8000`.

---

## Default Roles & Access Credentials

| Portal | URL Route | Default Email / Username | Default Password | Role Access |
|---|---|---|---|---|
| **Central Board Admin** | `/login` | `admin@drishticomputer.com` | Configured in `.env` (`SUPER_ADMIN_PASSWORD`) | `super_admin`, `admin` |
| **Franchise Study Centre** | `/login` | Director email (e.g. `patna@drishti.com`) | `password123` | `centre`, `staff` |
| **Student Portal** | `/student` | Roll No / Reg No (e.g. `DCE2501042`) | Student Mobile (e.g. `9876543210`) | `student` |

---

## Running the Automated Test Suite

The project includes 28 feature and unit test suites covering every layer of the system:
```bash
php artisan test
```

### Targeted Test Execution
Run specific module test suites:
```bash
# Security & Quality Audit Suite (12 tests, 121 assertions)
php artisan test tests/Feature/SecurityAndQualityAuditTest.php

# Student Self-Service Portal (16 tests, 96 assertions)
php artisan test tests/Feature/StudentPortalModuleTest.php

# Franchise Centre Portal (15 tests, 67 assertions)
php artisan test tests/Feature/CentrePortalModuleTest.php

# Admissions & Payment Gateway Workflow (15 tests, 73 assertions)
php artisan test tests/Feature/AdmissionsAndFeeWorkflowTest.php

# Operational Modules & Financial P&L (8 tests, 71 assertions)
php artisan test tests/Feature/OperationalModulesTest.php
```

---

## Security Architecture & Best Practices

1. **Authentication & Multi-Role RBAC:**
   - Middleware gating (`role:super_admin,admin`, `role:centre,staff`, `role:student`).
   - Center active check (`centre.active`) immediately revoking sessions of suspended branches.
   - Deactivated user lockout.
   - Rate limiting on sensitive endpoints (`throttle:6,1` on login/password resets, `throttle:15,1` on public forms, `throttle:30,1` on verification).

2. **Data Isolation & Anti-IDOR:**
   - Multi-tenant query scoping (`BelongsToCentre`) preventing cross-centre data leakage.
   - Student ownership authorization on payment receipts, identity cards, marksheets, and support tickets. Unauthorized access triggers explicit `403 Forbidden` or `404 Not Found` responses.

3. **Cryptographic Signatures & Verification:**
   - Webhook & online checkout signatures use timing-safe `hash_equals` against HMAC-SHA256 hashes.
   - Certificate & Marksheet records include unique SHA-256 verification hashes for tamper detection.
   - Public certificate verification masks personal contact details (phone, email, home address) to protect candidate privacy.

4. **File Upload Hardening:**
   - `FileUploadService` enforces strict MIME-type allowlists and automatically rejects dangerous executable extensions (`.php`, `.exe`, `.sh`, `.phtml`, `.bat`, `.js`, etc.).
   - Files are stored with cryptographically unguessable random filenames.

5. **Financial Accounting Integrity:**
   - Atomic `DB::transaction()` with database row locks (`lockForUpdate()`) strictly preventing duplicate fee credit, race conditions, or duplicate receipt numbers.
   - Real-time separation of gross fee collections, refund adjustments, categorized operating expenses, marketing spends, and net institutional profit.

---

## Production Deployment Checklist

Refer to [DEPLOYMENT_CHECKLIST.md](file:///c:/xampp/htdocs/schoolfrenchise/DEPLOYMENT_CHECKLIST.md) for step-by-step production server setup, security hardening, web server configs, and payment gateway credential activation.
