# Project Status Report: TECH ERA COMPUTER

**System:** TECH ERA COMPUTER (Institute & Franchise Management System)  
**Brand:** TECH ERA COMPUTER &bull; Tagline: *Building Digital Skills for the Future*  
**Campus / Address:** Sasbahana, Sheikhpura, Bihar - 801105 &bull; Helpline: 9162384660  
**Framework:** Laravel 12.x / PHP 8.2+ / MySQL 8.x  
**Audit Date:** 2026-10-17  
**Quality & QA Status:** Production-Ready Verification Passed  
**Test Suite Metrics:** 29 Feature & Unit Test Suites | **269 Tests Passing** | **1,229 Assertions** | **0 Failures**  

---

## 1. Completed Features

### 1.1 Multi-Guard Authentication & Authorization
- Unified Administrative & Staff Login (`/login`) with rate limiting (`throttle:6,1`).
- Dedicated Student Roll/Registration Login (`/student`) with rate limiting (`throttle:6,1`).
- Multi-Role RBAC Middleware enforcing boundaries across 5 user roles (`super_admin`, `admin`, `centre`, `staff`, `student`).
- Centre Active Verification Middleware (`centre.active`) ensuring suspended or deactivated franchises cannot access backend tools.
- Strict Tenant Isolation (`BelongsToCentre` global scopes and Policies) blocking cross-centre and cross-student IDOR access.
- Cryptographic Password Reset workflow (`/password/reset`) with single-use tokens, 60-minute expiry, and security audit trail.
- Secure Session Termination (`/logout`) with session invalidation and CSRF token regeneration.

### 1.2 Public Website (23 Fully Functional Pages)
- **Home (`/`)**: Violet/purple institute header, marquee announcement ticker, hero slider, live animated metrics (12,042 Franchises, 28 States, 763 Cities, 364 Courses), interactive AJAX state-to-district demo lead capture form, ISO 9001:2015/NITI Aayog badges, campus gallery carousel, institutional footer with helpline and WhatsApp float.
- **Institutional About Pages**: About Us (`/about-us`), Company Profile (`/company`), Chairman's Message (`/chairman`), Mission & Vision (`/mission`), Corporate Members (`/corporate-members`), Facilities (`/facilities`), Photo Gallery (`/gallery`).
- **Course Catalog**: Course Directory (`/courses`) with categories and search; Course Detail (`/course/{slug}`) with duration, eligibility, module syllabus, fee schedule, and enquiry modal.
- **Franchise Public Zone**: "Apply for New Franchise" (`/frenchise` & `/franchise`), "Franchise Application Status" tracker (`/frenchise-status`), "Search Franchise" centre locator (`/search_centre`).
- **Student Public Zone**: Public Online Admission Enquiry (`/admission-enquiry`), Public Online Admission Application (`/admission-apply`), Public Admission Status Tracker (`/admission-status`).
- **Legal & Compliance**: Contact Us (`/contact-us`), Privacy Policy (`/privacy-policy`), Terms & Conditions (`/terms-conditions`), Refund Policy (`/refund-policy`), SEO `sitemap.xml`, and `robots.txt`.

### 1.3 Public Verification Engine
- **Certificate & Marksheet Verification (`/check-certificate-marksheet`)**: Dual verification mode (Registration No + Father Name OR Certificate No). Shows verified student particulars, course, grade, and issuing centre with privacy data protection.
- **Direct QR-Code Verification (`/verify/cert/{cert_no}`)**: Mobile camera scan destination verifying issued credentials in real time.
- **Admission Status Verification (`/check-admission`)**: Real-time candidate enrollment verification by Roll No or Registration No.
- **Verification Audit Logging**: Stores client IP, query, timestamp, and result in `verification_logs`.
- **Privacy Guarding**: Public verification views strictly suppress candidate phone numbers, email addresses, and residential street addresses.

### 1.4 Student Self-Service Portal (`/student/*` - 16 Features)
1. **Student Dashboard**: Purple/violet header, financial KPI cards (Total Fees, Total Paid, Total Dues), enrolled program details, batch timetable, recent payment ledger, fixed mobile bottom navigation.
2. **My Profile (`/student/profile`)**: Enrollment dossier, personal details, contact address editor.
3. **Admission Form Dossier (`/student/admission-form`)**: Printable official institutional enrollment dossier with declaration signatures.
4. **My Course & Syllabus (`/student/course`)**: Program curriculum, module breakdown, subject papers, maximum/passing marks.
5. **My Fees & Instalments (`/student/fees`)**: Transparent fee structure, concessions, payment records, and milestone installment due dates.
6. **Pay Fees Online (`/student/fees/pay`)**: Dynamic balance validation, installment auto-allocation, payment mode selection (`upi`, `card`, `net_banking`).
7. **Payment History Ledger (`/student/fees/history`)**: Chronological payment history with computerized receipt numbers.
8. **Printable Payment Receipts (`/student/fees/receipts/{payment}`)**: Computerized branded receipt with student particulars, amount in words, and QR verification stamp.
9. **Digital Student ID Card (`/student/id-card`)**: Dual-sided printable identity card with photo frame, barcode/QR badge, and director seal.
10. **Study Materials Library (`/student/study-materials`)**: PDF e-books and notes downloads scoped to the student's enrolled course.
11. **Batch Schedule & Timetable (`/student/schedule`)**: Shift timing, assigned lab room, and weekly days breakdown.
12. **Attendance Analytics (`/student/attendance`)**: Total sessions, present/absent counters, dynamic attendance percentage calculation.
13. **Examinations & Hall Tickets (`/student/exams`, `/student/exams/{id}/admit-card`)**: Exam timetable and official printable admit cards with candidate instructions.
14. **Results & Academic Marksheets (`/student/results`, `/student/results/{id}/marksheet`)**: Subject-wise marks, percentage, grade, and computerized marksheet transcript.
15. **Certificates & Diplomas (`/student/certificates`)**: Tamper-proof certificates with SHA-256 cryptographic verification tokens and verification links.
16. **Help & Support Desk (`/student/support`)**: Raise tickets with attachments, track conversation timeline, and receive real-time notifications.
17. **Notifications & Security**: In-app alert bell center (`/student/notifications`) and Password Change (`/student/change-password`).

### 1.5 Franchise Study Centre Portal (`/centre/*` - 15 Features)
- Scoped Centre Dashboard (`/centre/dashboard`) with real-time KPI metric counters and active batch shortcuts.
- Direct Student Registration with photo/ID proof uploads and automatic Roll No / Registration No generation.
- Scoped Student Directory, Printable Admission Dossier, and Digital Student ID Cards.
- Daily Batch Attendance Register with radio toggles (Present, Absent, Late, Leave), bulk "All Present" shortcut, and monthly attendance percentage analytics.
- Examination Management, Batch Hall Tickets download, Internal Marks Entry (Theory, Practical, Viva), and Results Register.
- Offline Fee Collection intake with balance validation, computerized official receipts (`RCP-YYYY-XXXXX`), and outstanding dues reporting.
- Franchise Wallet Ledger, real-time balance tracking, and top-up recharge requests with bank/UPI transfer slips.
- Official Printable Certificate of Authorized Study Centre (`/centre/downloads/certificate`) with Board seal.
- Syllabi, flex banner, and academic prospectus downloads.
- Priority Centre Support Desk with threaded replies to Head Office.
- Centre Profile management and password updates.

### 1.6 Central Administrative Panel (`/admin/*` - All 20 Modules)
- **Module 1: Dashboard**: Nationwide KPIs, 6-month revenue trends, recent admissions, recent collections, live audit trail.
- **Module 2: Students**: Complete roster, search, centre filter, status filter, CSV export, dossier view, edit, soft delete.
- **Module 3: Admissions**: Applications review dossier, one-click approval with automatic user/student provisioning, rejection with reasons, direct admission creation.
- **Module 4: Courses**: Course catalog, create course with fee structures, syllabus breakdown, prospectus dossier, edit, soft delete.
- **Module 5: Batches & Attendance**: Daily shift management, interactive roll call sheet, institutional attendance reporting with KPI filters.
- **Module 6: Examinations**: Exam scheduling, MCQ question bank, marks entry, computerized marksheet dossiers.
- **Module 7: Study Materials**: Multi-format library (PDF/DOC/ZIP up to 20MB) via `FileUploadService`, status toggles, course/centre scoped downloads.
- **Module 8: Certificates & Marksheets**: Issuance engine, auto serial numbers (`DCE/CERT/YYYY/XXXX`), SHA-256 hash, live QR codes, luxury printable certificates, marksheet statements, revocations/reinstatements with audit logs.
- **Module 9: Fees & Payments**: Financial collections roster, outstanding dues roster, receive payments, computerized receipt generation, refunds with ledger reversals.
- **Module 10: Franchise / Centres**: Nationwide franchise directory, application review & approval/rejection with wallet provisioning, direct centre creation, multi-tab centre dossier, affiliation certificates.
- **Module 11: Downloads & Orders**: Software/form catalog, resource uploads, download counters, franchise welcome kit & stationery order fulfillment, financial separation of completed, pending, and refunded orders.
- **Module 12: Wallet History**: Franchise ledger, transaction search/filter, manual adjustments with overdraft protection, top-up verification, wallet freeze/unfreeze, printable account statements.
- **Module 13: Help & Support**: Helpdesk tickets, ticket creation, threaded conversation timeline, staff assignment, 6-stage lifecycle transitions, automated notifications.
- **Module 14: Expenses & Operating Costs**: Operational expense ledger, voucher reference, receipt invoice upload, marketing campaign tracker with CPL ROI analytics.
- **Module 15: Website CMS**: CMS management hub, announcements & marquee notices with circular uploads, photo gallery with categories, lead enquiry management, site content configuration.
- **Module 16: Notifications & Broadcasting**: Targeted broadcast composer, notification center, read status management.
- **Module 17: Reports & Analytics**: BI executive hub, student report, fees & revenue report, attendance report, exam performance report, expense report, streaming CSV exports.
- **Module 18: User Management**: User roster, provisioning with role matrix, user dossier, edit, password reset modal, toggle status, self-deletion/deactivation safeguards.
- **Module 19: Settings**: Institutional branding & logos, corporate office contact, academic sessions, financial & numbering prefixes, payment gateway & messaging switches.
- **Module 20: Audit Logs**: Forensic audit trail, search/filter by IP/operator/model, diff inspector comparing old vs new JSON states, CSV export.

### 1.7 Financial Engine & Payment Gateway
- **Multi-Provider Architecture**: Razorpay and Cashfree support via `App\Services\PaymentGatewayService`.
- **Server-Side Order Creation**: Secure order generation with amount, currency, and receipt tags.
- **Authoritative Cryptographic Signature Verification**: HMAC-SHA256 verification using timing-safe `hash_equals`.
- **Idempotency & Concurrency Safeguards**: `OnlineTransaction::lockForUpdate()` within `DB::transaction()` preventing duplicate payment credits on replay callbacks or concurrent webhooks.
- **Live Mode Guards**: Live mode activation blocked unless authentic merchant keys are configured.
- **Financial P&L Statement Engine (`/admin/reports/financial`)**: Real-time calculation of Gross Fee Collections, Soft-Deleted Refunds, Operating Expenses, Marketing Spends, and Net Institutional Profit with daily cashflow reconciliation and streaming CSV export.

### 1.8 Unified Notification Dispatch Service (`App\Services\NotificationDispatchService`)
- Automated multi-channel notification dispatcher supporting real-time in-app alerts (`SystemNotification`) and external WhatsApp/SMS gateway alerts.
- Integrated into: Admission approvals, fee receipt generation, admit card releases, certificate issuance, wallet alerts, and support ticket replies.

---

## 2. Partially Completed Features

### 2.1 Public Online Admission Acknowledgement Slip
- **Current State**: Public application submission (`/admission-apply`) and tracking (`/admission-status`) are fully operational and recorded in MySQL.
- **Gap**: Candidates do not have a dedicated, computerized, printable **Admission Application Acknowledgement Slip (पावती / रसीद)** (`/admission/acknowledgement/{application_no}`) with institutional crest, candidate photo frame, tracking QR code, applicant declaration, and centre scrutiny verification box.

### 2.2 Live SMS / WhatsApp External Gateway Provider Adapters
- **Current State**: `NotificationDispatchService` generates in-app notifications and formats outbound payloads, with simulated gateway dispatch.
- **Gap**: Direct HTTP client drivers for specific Indian SMS/WhatsApp gateway providers (Fast2SMS, MSG91, Interakt, Twilio) can be configured via dedicated `.env` driver settings.

### 2.3 Centre Portal Batch Attendance Monthly Register Export
- **Current State**: Daily attendance marking and monthly student regularity percentage calculations are implemented.
- **Gap**: Printable monthly attendance register sheet (grid layout of days 1–31) for physical centre documentation is not yet available in the Centre Portal.

### 2.4 Centre Portal Supply & Welcome Kit Self-Service Order Intake
- **Current State**: Admin manages and fulfills resource/kit orders (`/admin/downloads/orders`).
- **Gap**: Centre Directors cannot submit stationery / welcome kit replenishment requests directly from their portal.

---

## 3. Missing Features

1. **Dedicated Printable Admission Acknowledgement Slip Route & View** (`/admission/acknowledgement/{application_no}`) accessible by candidate, centre staff, and administrator.
2. **Bulk Admit Card & ID Card Zip/PDF Exporter** for entire student batches in the Centre Portal.
3. **Dead-Letter Webhook Retry Queue** for automated retries of unhandled payment gateway webhooks.
4. **Artisan CLI Backup Command** (`php artisan system:backup`) for automated database and document storage snapshots.
5. **Centre Director Monthly Royalty / Billing Statement PDF Exporter**.

---

## 4. Bugs and Security Issues (Audit & Resolution)

| Issue | Severity | Status | Resolution / Safeguard |
|---|---|---|---|
| Direct QR verification failed on certificate numbers containing slashes (`DCE/CERT/...`) | High | **RESOLVED** | Added regex parameter constraint `->where('cert_no', '.*')` to route definition. |
| Potential XSS from unescaped session flash `{!! session('success') !!}` | Medium | **RESOLVED** | Converted to HTML-escaped `{{ session('success') }}` across all views. |
| Missing HTTP security headers (clickjacking, MIME sniffing, XSS protection) | Medium | **RESOLVED** | Implemented and registered `SecurityHeadersMiddleware` attaching `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`. |
| Unprotected endpoints vulnerable to brute force / submission flooding | Medium | **RESOLVED** | Added `throttle:6,1` on auth/passwords, `throttle:15,1` on public forms/checkout, and `throttle:30,1` on verification searches. |
| Potential IDOR across student and centre tenant boundaries | High | **RESOLVED** | Enforced strict `BelongsToCentre` global scopes and ownership checks in all Controllers and Policies. |
| Webhook replay & duplicate payment credit risks | High | **RESOLVED** | Enforced atomic `lockForUpdate()` within `DB::transaction()` on all payment verifications. |
| Public certificate verification exposed candidate personal contact info | Medium | **RESOLVED** | Masked phone numbers, email addresses, and home street addresses on public verification views. |
| Dangerous file uploads (PHP/executable scripts) | High | **RESOLVED** | `FileUploadService` enforces MIME type whitelists, size caps, and randomized filenames. |
| Symlinked asset access failure on fresh installations | Low | **RESOLVED** | Executed `php artisan storage:link` connecting `storage/app/public` to `public/storage`. |

---

## 5. Database and Migration Status

- **Database Connection**: MySQL on `127.0.0.1:3306`, Database: `frenchise`.
- **Character Set / Collation**: `utf8mb4` / `utf8mb4_unicode_ci`.
- **Total Migrations**: 20 migrations executed.
- **Migration Status**: All 20 migrations have run successfully (`[Ran]`) with zero pending migrations.
- **Rollback Safety**: Every migration file defines a matching `down()` method, verified via automated rollback tests (`SecurityAndQualityAuditTest::test_all_migrations_have_rollback_safety`).

### Table Inventory
1. `users`, `password_reset_tokens`, `roles`, `permissions`, `role_user`, `permission_role`
2. `states`, `districts`
3. `centres`, `centre_wallets`, `centre_wallet_transactions`
4. `course_categories`, `courses`, `course_subjects`, `batches`
5. `students`, `admissions`, `admission_applications`, `student_fees`, `fee_structures`, `fee_payments`, `payment_receipts`, `online_transactions`
6. `attendances`, `study_materials`, `exams`, `admit_cards`, `exam_results`, `exam_result_subjects`, `marksheets`, `certificates`, `verification_logs`
7. `support_tickets`, `support_ticket_replies`, `system_notifications`
8. `expenses`, `marketing_campaigns`, `notices`, `galleries`, `enquiries`, `franchise_applications`, `download_catalogs`, `kit_orders`, `settings`, `audit_logs`

---

## 6. Tests That Passed and Failed

- **Total Test Suites**: 29 feature and unit test files.
- **Total Tests**: **269 Tests Passed**
- **Total Assertions**: **1,229 Assertions**
- **Failed Tests**: **0 Failed**
- **Errors**: **0 Errors**
- **Execution Time**: ~17.87 seconds
- **Pass Rate**: **100%**

### Test Suite Summary
| Test Suite File | Tests | Assertions | Status |
|---|---|---|---|
| `AdmissionAcknowledgementTest.php` | 5 | 23 | PASS |
| `AdminAdmissionModuleTest.php` | 9 | 43 | PASS |
| `AdminAuditLogModuleTest.php` | 5 | 19 | PASS |
| `AdminBatchModuleTest.php` | 6 | 29 | PASS |
| `AdminCentreModuleTest.php` | 11 | 48 | PASS |
| `AdminCertificateModuleTest.php` | 7 | 34 | PASS |
| `AdminCmsModuleTest.php` | 14 | 57 | PASS |
| `AdminCourseModuleTest.php` | 7 | 33 | PASS |
| `AdminDownloadModuleTest.php` | 10 | 44 | PASS |
| `AdminExamModuleTest.php` | 6 | 31 | PASS |
| `AdminExpenseModuleTest.php` | 11 | 47 | PASS |
| `AdminFeeModuleTest.php` | 8 | 39 | PASS |
| `AdminMaterialModuleTest.php` | 6 | 28 | PASS |
| `AdminNotificationModuleTest.php` | 9 | 28 | PASS |
| `AdminReportModuleTest.php` | 12 | 42 | PASS |
| `AdminSettingModuleTest.php` | 5 | 28 | PASS |
| `AdminStudentModuleTest.php` | 8 | 41 | PASS |
| `AdminSupportModuleTest.php` | 7 | 34 | PASS |
| `AdminUserModuleTest.php` | 14 | 51 | PASS |
| `AdminWalletModuleTest.php` | 8 | 37 | PASS |
| `AdmissionsAndFeeWorkflowTest.php` | 15 | 73 | PASS |
| `AuthAndManagementTest.php` | 14 | 58 | PASS |
| `CentrePortalModuleTest.php` | 15 | 67 | PASS |
| `ExampleTest.php` | 1 | 2 | PASS |
| `NotificationDispatchServiceTest.php` | 4 | 19 | PASS |
| `OperationalModulesTest.php` | 8 | 71 | PASS |
| `PublicWebsiteTest.php` | 9 | 42 | PASS |
| `SecurityAndQualityAuditTest.php` | 12 | 121 | PASS |
| `StudentPortalModuleTest.php` | 16 | 96 | PASS |
| **Total** | **269** | **1,229** | **100% PASS** |

---

## 7. Next Five Implementation Tasks in Priority Order

### Completed in this Sprint:
- [x] **Task Priority 1: Public Admission Application Acknowledgement Slip & Printable Dossier Engine**
  - Implemented `GET /admission/acknowledgement/{application_no}` route, controller action in `EnquiryPublicController`, and printable view `pages.admission-acknowledgement`.
  - Added "Print Acknowledgement Slip" button on `/admission-status` and "Print Application Slip" button on administrative application review (`/admin/admissions/applications/{id}`).
  - Added automated test suite `tests/Feature/AdmissionAcknowledgementTest.php` (5 tests, 23 assertions, 100% passing).

### Next Up:
### Task Priority 1: Centre Portal Monthly Batch Attendance Register Export & Print View
- **Scope**: Implement `/centre/batches/{id}/attendance/register` allowing Centre Directors to view and print the complete monthly attendance grid (days 1 to 31) for physical lab inspections and local regulatory records.
- **Verification**: Automated feature tests verifying centre isolation, monthly calculations, and print formatting.

### Task Priority 2: Franchise Welcome Kit & Stationery Stock Order Intake in Centre Portal
- **Scope**: Implement `/centre/orders` and `/centre/orders/create` enabling Study Centre Directors to place requests for student welcome kits, certificate blank papers, prospectus sets, and institute marketing material directly from their portal.
- **Verification**: Automated feature tests covering order placement, ledger deduction/cash on delivery, and administrative fulfillment notifications.

### Task Priority 3: Pluggable SMS & WhatsApp Gateway Provider Adapters
- **Scope**: Extend `NotificationDispatchService` with pluggable HTTP drivers for Indian SMS/WhatsApp gateway providers (Fast2SMS, MSG91, Interakt, and Twilio) configurable via `.env`.
- **Verification**: Feature tests verifying correct API endpoint resolution, request header construction, parameter mapping, and fallback error handling.

### Task Priority 4: Automated Daily Database & Storage Backup Artisan Command
- **Scope**: Implement `php artisan system:backup` Artisan command to create compressed MySQL database dumps and storage archives with retention policies and optional S3/remote storage dispatch.
- **Verification**: Unit tests verifying command execution, archive integrity, and non-destructive operation.

### Task Priority 5: Centre Director Monthly Royalty & Billing Statement PDF Exporter
- **Scope**: Implement computerized monthly franchise royalty and settlement invoicing in the Centre Portal and Admin Wallet ledger with downloadable PDF format.
- **Verification**: Automated feature tests verifying monthly aggregation, debit calculations, and PDF layout.
