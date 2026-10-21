# Implementation Checklist: Drishti Computer Education

**System:** Drishti Computer Education (Institute & Franchise Management System)  
**Methodology:** Phased, Test-Driven & Verifiable Implementation  
**Execution Status:** All Phases 1 through 15 are 100% Complete & Verified (269/269 automated tests passing, 1,229 assertions). Comprehensive QA and security audit executed, defense-in-depth HTTP security headers installed, storage symlink connected, CSRF/XSS/IDOR safeguards hardened, rate limits enforced, Admission Application Acknowledgement Slip engine implemented, and comprehensive README, PROJECT_STATUS.md & Deployment Checklist established.

---

## Quality & Integrity Principles
- **No False Claims:** Do not mark any task as complete or declare a feature working until concrete code, database migrations, controllers, views, and end-to-end browser/terminal verification confirm it.
- **Preservation First:** Preserve existing configuration and working files.
- **Responsive & Accessible:** Every layout must pass responsive testing on Desktop, Tablet, and Mobile.
- **Defensive Security:** Strict input validation, role-based middleware gating, safe file uploads, and CSRF protection on all forms.

---

## Milestone Phase Breakdown

### Phase 1: Environment, Database & Asset Foundation
- [x] **1.1. Database Provisioning**
  - Verified MySQL connection on `127.0.0.1:3306`.
  - Created MySQL database `frenchise` with `utf8mb4` character set and `utf8mb4_unicode_ci` collation.
  - Tested database connectivity and migrations successfully.
- [x] **1.2. Frontend Dependencies & Asset Pipeline**
  - Set up Bootstrap 5.3.3, jQuery 3.7.1, FontAwesome 6 icons, Google Fonts (Plus Jakarta Sans).
  - Created `public/css/drishti-custom.css` with Indian Institute styling, purple/violet accents, turquoise highlights, clean white cards.
  - Created `public/js/drishti-custom.js` with animated metric counters and dynamic AJAX state-to-district loader.
- [x] **1.3. Master Layouts & Reusable Component System**
  - Created public website layout `resources/views/layouts/app.blade.php` with top info bar, navigation, responsive mobile menu, helpline, and comprehensive legal footer.

---

### Phase 2: Database Migrations, Models & Seeding
- [x] **2.1. Geographic & Core Master Migrations**
  - Migration for `states` and `districts` (`2026_10_17_000001_create_geographic_tables.php`).
  - Seeded all 28 Indian States & 8 Union Territories with major districts (`GeographicSeeder.php`).
- [x] **2.2. User & RBAC Migrations**
  - Updated `users` table with `role`, `mobile`, `is_active` (`2026_10_17_000002_update_users_table_for_roles.php`).
- [x] **2.3. Franchise & Centre Migrations**
  - Migration for `centres`, `centre_wallets`, and `centre_wallet_transactions` (`2026_10_17_000003_create_centres_and_wallets_tables.php`).
- [x] **2.4. Academic & Course Migrations**
  - Migration for `course_categories`, `courses`, `course_subjects`, and `batches` (`2026_10_17_000004_create_courses_and_subjects_tables.php`).
- [x] **2.5. Student Admission & Fee Migrations**
  - Migration for `students`, `admissions`, `student_fees`, and `fee_payments` (`2026_10_17_000005_create_students_and_admissions_tables.php`).
- [x] **2.6. Exam, Marksheet & Certificate Migrations**
  - Migration for `exams`, `admit_cards`, `exam_results`, `exam_result_subjects`, `marksheets`, `certificates`, and `verification_logs` (`2026_10_17_000006_create_exams_and_certificates_tables.php`).
- [x] **2.7. Marketing, Inquiries & CMS Migrations**
  - Migration for `franchise_applications`, `enquiries`, `notices`, `galleries`, `settings`, and `online_transactions` (`2026_10_17_000007_create_marketing_and_cms_tables.php`).
- [x] **2.8. Eloquent Models & Database Seeding**
  - Implemented all 20+ Eloquent models with full relationships.
  - Seeded real Drishti courses (ADCA, DCA, BCC, CCC, Typing, Steno, Tally Prime, DTP, Python), sample authorized study centres (Noida, Patna, Lucknow, Ranchi, Delhi), real verifiable student certificates (Rahul Sharma, Priya Kumari), notices, and official settings.

---

### Phase 3: Multi-Guard Authentication & Authorization
- [x] **3.1. Authentication Controllers & Views**
  - Unified Login page (`/login`) for Head Office Admin, Centre Managers, and Staff with rate limiting (`throttle:6,1`).
  - Dedicated Student Login page (`/student`) with Registration/Roll No authentication and rate limiting (`throttle:6,1`).
  - Password Reset workflow with secure token generation (`/password/reset` and `/password/email`), 60-min expiry, bcrypt hash updates, and security audit trail.
  - Secure session logout (`/logout`) invalidating sessions and regenerating CSRF tokens.
- [x] **3.2. Role-Based Middleware & Route Protection**
  - Implemented `RoleMiddleware` checking role boundaries (`super_admin`, `admin`, `centre`, `staff`, `student`) and account active status (`is_active`).
  - Implemented `CentreActiveMiddleware` ensuring suspended/inactive centres cannot access backend tools.
  - Implemented `BelongsToCentre` global scope trait and strict Policies (`StudentPolicy`, `AdmissionPolicy`, `FeePaymentPolicy`, `CentrePolicy`, `CertificatePolicy`) enforcing tenant boundary isolation and IDOR defense.
- [x] **3.3. Foundation Services & Transactions**
  - `FeeService` executing student fee assignments and payment collections in atomic `DB::transaction()` blocks with computerized `PaymentReceipt` and SHA-256 QR tokens.
  - `FileUploadService` enforcing MIME validation, size caps, unguessable filenames, and blocking malicious/executable extensions.
  - Secure `SuperAdminSeeder` and Artisan command `app:create-super-admin` using environment variables.

---

### Phase 4: Public Website (Drishti Computer Education)
- [x] **4.1. Homepage (`/`)**
  - Top header bar with helpline number (`9942434236`), email (`info@drishticomputer.com`), Noida corporate office address, and login pills.
  - Main navigation bar with responsive toggle and dropdown menus.
  - Animated marquee ticker displaying latest exam/admission notices.
  - Hero slider section with banner visuals, calls to action, and live demo request form.
  - Live animated metric counters: Franchises (12,042), States (28), Cities (763), Courses (364).
  - Interactive "Request For Live Demo" lead capture form with dependent State & District AJAX loading.
  - Facilities showcase: Global Certificate, Quality Education, Smart Computer Labs, Skilled Teachers.
  - WhatsApp automation highlights (Auto Prospectus, Student Registration alert, Fee payment confirmation, Certificate delivery).
  - Authorizations & Accreditations badge carousel (ISO 9001:2015, MCA, NITI Aayog, MSME).
  - Photo gallery preview and institutional footer with helpline and WhatsApp float.
- [x] **4.2. Institutional About Pages**
  - About Us (`/about-us`), Company Profile (`/company`), Chairman's Message (`/chairman`), Mission & Vision (`/mission`), Corporate Members (`/corporate-members`), Our Facilities (`/facilities`).
- [x] **4.3. Course Catalog**
  - Course Directory (`/courses`) with category filters and search.
  - Course Detail view (`/course/{slug}`) featuring duration, eligibility, module breakdown, subject papers, and online admission enquiry form.
- [x] **4.4. Franchise Public Zone**
  - "Apply for New Franchise" (`/frenchise` & `/franchise`) with complete centre & director application form.
  - "Search Franchise" Centre Locator (`/search_centre` & `/search-centre`) with state/district/code filters.
  - "Franchise Application Status" tracker (`/frenchise-status` & `/franchise-status`) with real-time status lookup.
- [x] **4.5. Student Public Zone**
  - Public Online Admission / Enquiry Form (`/admission-enquiry`).
  - Contact Us (`/contact-us`) with feedback capture and institutional head office details.
  - Policies: Privacy Policy (`/privacy-policy`), Terms & Conditions (`/terms-conditions`), Refund Policy (`/refund-policy`).
  - SEO: `sitemap.xml` and `robots.txt`.

---

### Phase 5: Verification Engine & QR Code Integration
- [x] **5.1. Certificate & Marksheet Public Verification (`/check-certificate-marksheet`)**
  - Verification search form by Registration Number + Father's Name or Certificate Number.
  - Instant verified result card showing: Student photo, candidate name, father's name, course, issuing centre, grade, percentage, issue date, and official verification watermark.
  - Direct QR-code destination endpoint (`/verify/cert/{cert_no}`) enabling instant mobile phone camera scan verification.
- [x] **5.2. Admission Status Verification (`/check-admission`)**
  - Public search by Roll Number or Registration Number returning candidate enrollment verification.
- [x] **5.3. Audit Logging**
  - Record IP address, timestamp, query, and success/failure in `verification_logs` for anti-fraud detection.
- [x] **5.4. Verified Online Fee Payment Engine (`/online-payment`)**
  - Roll number lookup, order checkout, HMAC-SHA256 signature verification, transaction recording in `online_transactions` and `fee_payments`, and printable official receipt (`/online-payment/receipt/{transaction_id}`).

---

### Phase 6: Franchise / Centre Portal (`/centre/*`) - 100% Complete & Verified
- [x] **6.1. Centre Dashboard**
  - Summary metric cards: Total Enrolled Students, Active Batches, Fee Collections, Pending Certificates, Wallet Balance.
  - Recent student admissions table with quick actions.
- [x] **6.2. Student Admission Management**
  - Multi-step Student Admission Form with passport photo, signature, and ID document uploads.
  - Automated Generation of Registration Number (`DCE/{YEAR}/{CENTRE}/{SEQ}`) and Roll Number.
  - Immediate digital Student Identity Card generator with embedded QR code.
- [x] **6.3. Batches & Attendance**
  - Manage daily/shift batches (e.g. 08:00 AM - 09:30 AM).
  - Assign admitted students to batches; mark and report student attendance.
- [x] **6.4. Fee Collection & Payment Receipts**
  - View student installment schedule and overdue accounts.
  - Collect fee installment (Cash / UPI / Online), calculate remaining balance, and generate computerized, printable GST/Standard receipt (`RCP-...`).
- [x] **6.5. Marks Entry & Certificate Requests**
  - Submit student theory, practical, and viva marks for completed course modules.
  - Request Board approval for Marksheet and Certificate issuance.
- [x] **6.6. Centre Profile & Affiliation Certificate**
  - View authorized franchise details, download official Franchise Affiliation Certificate with Board Seal & QR Code.
- [x] **6.7. Franchise Wallet & Billing**
  - View ledger transactions, available credits, and request wallet balance recharge.

---

### Phase 7: Central Board / Super Admin & Centre Management Panel (`/admin/*`)
- [x] **7.1. Module 1: Dashboard (`/admin/dashboard`)**
  - Nationwide KPI cards: Total Students, Active Enrolled, Collected Fees, Outstanding Dues, Online Payment Gateway Breakdown, Operating Expenses.
  - 6-Month Fee Collection trend analysis chart.
  - Recent Course Admissions table with direct access.
  - Recent Fee Collections table with computerized receipt references.
  - Real-time System Audit Trail log viewer.
- [x] **7.2. Module 2: Students (`/admin/students`)**
  - Roster view with search (name, roll no, reg no, mobile), centre filter, status filter, and pagination.
  - Register New Student (`/admin/students/create`): automated Roll No & Reg No generation, user account provisioning with hashed password, photo/signature/KYC document upload via FileUploadService.
  - Student Dossier View (`/admin/students/{id}`): tabbed profile with Personal particulars, Admissions, Fee Breakdown, Certificates/Marksheets, and Attendance log.
  - Edit Student (`/admin/students/{id}/edit`): update particulars and academic status.
  - Soft Delete (`DELETE /admin/students/{id}`): historical retention with audit trail.
  - Streamed CSV Export (`/admin/students/export`).
- [x] **7.3. Module 3: Admissions (`/admin/admissions`)**
  - Applications roster & Active Admissions dual-tab architecture with search, centre filter, status filters, and pagination.
  - Review Application dossier (`/admin/admissions/applications/{id}`): applicant particulars, qualification badges, passport photo, and uploaded KYC/ID proof previews.
  - One-click Application Approval with dynamic fee assignment, concession calculations, automatic Roll No & Registration No generation, User account provisioning, and admission ledger conversion.
  - Formal Application Rejection with mandatory recorded reason and reviewer audit tracking.
  - Direct Admission Creation (`/admin/admissions/create`): student selection, course syllabus, batch assignment, and fee package assignment via `FeeService`.
  - Active Admission Edit & Status updates (`active`, `completed`, `cancelled`).
  - Soft-deletion with historical retention and audit trail.
- [x] **7.4. Module 4: Courses (`/admin/courses`)**
  - Course curriculum directory with search, category filtering, publication status, and KPI summary counters.
  - Add New Course (`/admin/courses/create`): course code, title, duration, eligibility, 4-tier fee breakdown (tuition, registration, exam, certificate), rich syllabus outline, thumbnail upload, popularity & publication status toggles.
  - Automatic provisioning of standard `FeeStructure` breakdown for newly created courses.
  - Course Prospectus Dossier (`/admin/courses/{id}`): syllabus breakdown, fee schedules, linked study batches, and enrolled admissions.
  - Edit & Update Course (`/admin/courses/{id}/edit`): live fee total recalculation, thumbnail updates, syllabus modifications.
  - Soft-delete course archiving with security audit logging.
- [x] **7.5. Module 5: Batches & Attendance (`/admin/batches`)**
  - Shift scheduling directory with centre filter, course filter, and KPI summary counters.
  - Create & Edit Batches (`/admin/batches/create` & `/admin/batches/{id}/edit`): shift timings (start/end), days schedule (e.g. Mon-Sat), and operational status.
  - Batch Dossier (`/admin/batches/{id}`): shift particulars, enrolled student directory, and recent attendance log entries.
  - Daily Attendance Sheet (`/admin/batches/{id}/attendance`): interactive class roll call with date selector, shortcut buttons ("All Present", "All Absent"), per-student status pills (`present`, `absent`, `late`, `leave`), individual remarks, and database persistence.
  - Institutional Attendance Reporting (`/admin/batches/attendance/report`): date range filtering, centre and batch selectors, KPI metrics (Total Logged Sessions, Present Count, Absent Count, Attendance Ratio %), and tabular ledger.
  - Soft-delete batch archiving with audit trail.
- [x] **7.6. Module 6: Examinations (`/admin/exams`)**
  - Exam schedules directory with centre filter, exam type filter, status badges, and KPI counters.
  - Create & Edit Exam Schedules (`/admin/exams/create` & `/admin/exams/{id}/edit`): course, exam title, date, duration, total marks, passing marks, and exam mode (`offline`, `online`, `practical`).
  - MCQ Question Bank (`/admin/exams/questions`): questions filtering by course, question difficulty tiers, interactive options builder (A, B, C, D), correct option identification, marks value, and explanation notes.
  - Student Marks Entry (`/admin/exams/results/create`): dynamic candidate lookup, course curriculum module listing, real-time JavaScript percentage and grade calculation, pass/fail status determination, and ledger persistence.
  - Computerized Marksheet Dossier (`/admin/exams/results/{id}`): printable layout with subject-wise breakdown, maximum marks, marks scored, percentage, grade, and institutional approval workflow.
  - Tested with 100% passing automated test suite (`tests/Feature/AdminExamModuleTest.php`).
- [x] **7.7. Module 7: Study Materials (`/admin/materials`)**
  - Digital resource library with search, course filter, type filter (`document`, `video`, `syllabus`, `question_paper`), centre access scoping, and KPI summary counters.
  - Upload & Publish Material (`/admin/materials/create`): file upload supporting PDF, DOC, DOCX, XLS, PPT, ZIP up to 20MB via `FileUploadService`, or external video lecture embed URL; course and module linkage; centre scoping (`All Centres` or specific franchise).
  - Material Dossier (`/admin/materials/{id}`): file metadata, secure download link, embedded preview, and audit log.
  - One-click instant publish / unpublish status toggle (`POST /admin/materials/{id}/toggle-status`).
  - Soft-delete resource archiving with audit trail.
  - Tested with 100% passing automated test suite (`tests/Feature/AdminMaterialModuleTest.php`).
- [x] **7.8. Module 8: Certificates & Marksheets (`/admin/certificates`)**
  - Central certificate registry with search, status filtering (`issued`, `revoked`, `pending`), and financial/issue KPI counters.
  - Certificate Issuance Engine (`/admin/certificates/create`): student and course selection, automated Certificate Number (`DCE/CERT/YYYY/XXXX`) and Marksheet Number generation, SHA-256 cryptographic security hash computation, and live QR-code generation for public mobile scan verification.
  - Luxury Printable Certificate Layout (`/admin/certificates/{id}/print`): ornate golden borders, official embossed emblem, watermark, QR code, student photo, and signatory authority badges.
  - Official Marksheet Statement (`/admin/certificates/{id}/marksheet`): semester/module wise grade statement with computerized tabular design.
  - Certificate Revocation & Reinstatement (`POST /admin/certificates/{id}/revoke` & `POST /admin/certificates/{id}/reinstate`): mandatory revocation reason recording, instant public verification revocation flag, and security audit log.
  - Tested with 100% passing automated test suite (`tests/Feature/AdminCertificateModuleTest.php`).
- [x] **7.9. Module 9: Fees & Payments (`/admin/fees`)**
  - Financial Collections Roster (`/admin/fees`): search by receipt number, student name, roll number, or transaction reference; filter by centre, payment mode (`cash`, `upi`, `online`, `cheque`, `dd`), and date range; strict separation of collected, online/UPI, cash, discounts, and outstanding dues.
  - Outstanding Fee Dues Roster (`/admin/fees/dues`): real-time balance calculations (`net_fee - total_paid`), course and centre filtering, total institutional dues KPI.
  - Receive Fee Payment (`/admin/fees/create` & `POST /admin/fees`): admission selection, installment linking, payment mode, transaction reference, amount validation preventing excess payment, automatic receipt numbering (`RCP-YYYY-XXXXX`), and `FeeService` transaction handling.
  - Computerized Printable Receipt (`/admin/fees/receipts/{receipt}`): official receipt format with institute header, student particulars, fee components breakdown, payment method, collector signature line, and QR verification stamp.
  - Payment Refund & Adjustment (`POST /admin/fees/{payment}/refund`): refund execution, reversal of paid amount on admission ledger and installment schedule, recorded reason, and audit logging.
  - Tested with 100% passing automated test suite (`tests/Feature/AdminFeeModuleTest.php`).
- [x] **7.10. Module 10: Franchise / Centres (`/admin/centres`)**
  - Nationwide Franchise & Branch directory (`/admin/centres`): search by code, centre name, director, mobile, email, city; state and operational status filters; summary KPI cards (Total Centres, Active Franchises, Pending Inward Applications, Combined Lab PC capacity).
  - Franchise Inward Applications (`/admin/centres/applications`): review proposed centre applications submitted from public portal; status counters (total, pending, reviewed, approved, rejected).
  - Application Review Dossier (`/admin/centres/applications/{id}`): inspect applicant qualifications, facility floor area, proposed computer terminal counts, and address.
  - One-Click Franchise Approval (`POST /admin/centres/applications/{id}/approve`): generates or assigns unique Centre Code (`DCE-XX...`), provisions Franchise Owner user account (`role = centre`), initializes `CentreWallet`, allocates active status, and records `AuditLog`.
  - Formal Application Rejection (`POST /admin/centres/applications/{id}/reject`): mandatory formal reason recording with applicant status update.
  - Direct Centre Registration (`/admin/centres/create` & `POST /admin/centres`): auto-suggested centre code (`DCE-YY-XXX`), director credentials, dynamic AJAX district loader, lab capacity, valid upto, and secure logo/director photo/signature uploads via `FileUploadService`.
  - Comprehensive Centre Dossier (`/admin/centres/{id}`): multi-tab profile covering Institutional details, Enrolled Students list, Study Batches & shifts, and Centre Staff accounts (`staffUsers`).
  - Edit & Update Centre (`/admin/centres/{id}/edit` & `PUT /admin/centres/{id}`): update particulars, dates, capacity, location, and photos.
  - Operational Status Toggle (`POST /admin/centres/{id}/toggle-status`): quick activate/suspend action with security audit logging.
  - Luxury Printable Affiliation Certificate (`/admin/centres/{id}/certificate`): ornate golden borders, official seal, accreditation details, and print layout.
  - Soft-delete centre archiving (`DELETE /admin/centres/{id}`).
  - Tested with 100% passing automated test suite (`tests/Feature/AdminCentreModuleTest.php`).
- [x] **7.11. Module 11: Downloads & Orders (`/admin/downloads`)**
  - Downloadable Catalog Directory (`/admin/downloads`): search by title, version, description; category filters (`software`, `prospectus`, `form`, `template`, `driver`, `study_note`); publication status filter; KPI counters (Total Items, Active, Total Download Hits, Total Orders, Completed Revenue).
  - Upload New Resource (`/admin/downloads/create` & `POST /admin/downloads`): auto-slug generator, secure document/archive upload via `FileUploadService` supporting up to 20MB, human-readable file size calculator, version tagging, free vs paid pricing switch.
  - Resource Edit & File Replacement (`/admin/downloads/{id}/edit` & `PUT /admin/downloads/{id}`): update title, category, description, price, optional file replacement.
  - One-Click Status Toggle (`POST /admin/downloads/{id}/toggle-status`): instant public visibility toggling with security audit logging.
  - Secure File Serving & Download Hits Counter (`GET /admin/downloads/{id}/download`): increments download counter and serves file via storage stream.
  - Resource Archiving (`DELETE /admin/downloads/{id}`): soft-deletion with audit trail.
  - Resource & Kit Orders Directory (`/admin/downloads/orders`): track franchise welcome kits, stationery, certificate paper blank sets, and paid digital downloads.
  - Strict Financial Accounting on Orders: strictly separates completed revenue, pending orders, failed checkouts, and refunded amounts.
  - Order Fulfillment Dossier (`/admin/downloads/orders/{id}`): order particulars, customer/branch details, courier tracking/dispatch notes, and payment status updater workflow (`completed`, `pending`, `failed`, `refunded`).
  - Log Direct Order (`/admin/downloads/orders/create` & `POST /admin/downloads/orders`): auto-generated order numbers (`ORD-YYYY-XXXXXX`), franchise assignment, transaction reference, and notes.
  - Tested with 100% passing automated test suite (`tests/Feature/AdminDownloadModuleTest.php`).
- [x] **7.12. Module 12: Wallet History (`/admin/wallet`)**
  - Nationwide Franchise Wallet & Transaction Ledger (`/admin/wallet`): search by reference/UTR, description, centre code or name; filters by type (`credit`, `debit`), status (`approved`, `pending`, `rejected`), centre, and date range.
  - Strict Financial Accounting on Wallets: tracking circulating balance, lifetime approved credits, lifetime approved debits, pending recharge totals, and frozen wallets.
  - Manual Ledger Adjustment (`/admin/wallet/adjust` & `POST /admin/wallet/adjust`): credit top-up or debit fee/kit adjustments with mandatory reference number and description; strict overdraft prevention blocking unauthorized negative balances.
  - Pending Top-Up Verification Workflow (`POST /admin/wallet/transactions/{id}/approve` & `reject`): bank transfer verification, automated wallet balance credit, balance_after computation, and recorded rejection reasons.
  - Franchise Wallet Freeze / Unfreeze (`POST /admin/wallet/centres/{id}/freeze`): prevent transactions on delinquent centres with security audit logging.
  - Printable Account Statement (`/admin/wallet/centres/{id}/statement`): official running ledger statement with date-wise credits, debits, balance after, and authorized signatory footer.
  - Tested with 100% passing automated test suite (`tests/Feature/AdminWalletModuleTest.php`).
- [x] **7.13. Module 13: Help & Support (`/admin/support`)**
  - Central Helpdesk & Support Tickets Directory (`/admin/support`): search by ticket number, subject, description, user, or centre; filters by status (`open`, `in_progress`, `answered`, `resolved`, `closed`), priority (`urgent`, `high`, `medium`, `low`), category, centre, and assigned staff; KPI counters (total, open, in progress, resolved, urgent).
  - Create Support Ticket (`/admin/support/create` & `POST /admin/support`): unique ticket number generation (`TCK-YYYY-XXXXX`), centre linkage, staff assignment, priority level, categorization, and secure document/screenshot upload via `FileUploadService`.
  - Threaded Conversation Dossier (`/admin/support/{id}`): complete timeline history of replies between requester and support staff with staff badges, timestamps, message text, and file attachment downloads.
  - Official Reply Dispatcher (`POST /admin/support/{id}/reply`): response posting, optional resolution checkbox, last activity timestamp updates, and audit logging.
  - Staff Assignment Workflow (`POST /admin/support/{id}/assign`): reassign tickets to specific admin/staff officers, with automatic status progression to `in_progress`.
  - Status Lifecycle & Closure Management (`POST /admin/support/{id}/status`): transition between `open`, `in_progress`, `answered`, `resolved`, and `closed`.
  - Ticket Archiving (`DELETE /admin/support/{id}`): soft-deletion with audit trail.
  - Tested with 100% passing automated test suite (`tests/Feature/AdminSupportModuleTest.php`).
- [x] **7.14. Module 14: Expenses & Operating Costs (`/admin/expenses`)**
  - Operating Expense Ledger (`/admin/expenses`): search by title, voucher reference, notes, centre name/code; filter by expense category (`rent`, `electricity`, `salaries`, `hardware`, `internet`, `stationery`, `maintenance`, `marketing`, `other`), centre location, payment mode (`cash`, `bank_transfer`, `cheque`, `upi`, `card`), and date ranges.
  - Expense KPIs: real-time metrics for total lifetime disbursements, current calendar month expenses, digital ad & marketing campaign allocations, and top operational cost category breakdown.
  - Record Operational Expense (`/admin/expenses/create` & `POST /admin/expenses`): centre/head-office selection, category tagging, title, amount, payment mode, voucher reference number, receipt invoice document upload via `FileUploadService`, and notes with DB transaction and audit logging.
  - Expense Edit & File Replacement (`/admin/expenses/{id}/edit` & `PUT /admin/expenses/{id}`): modify particulars, voucher reference, or attached invoice voucher document.
  - Soft-delete Expense Archiving (`DELETE /admin/expenses/{id}`): soft-deletion with audit trail.
  - Marketing & Promotional Campaigns Submodule (`/admin/expenses/marketing`): digital ad spending directory with metrics for total campaigns, planned budget, actual amount spent, total leads generated, enrolled admissions/conversions, and Cost Per Lead (CPL) ROI calculation.
  - Create & Edit Marketing Campaigns (`/admin/expenses/marketing/create`, `edit`, `POST`, `PUT`, `DELETE`): track platforms (Facebook/Instagram, Google Ads, YouTube, Newspaper, Outdoor Hoardings, Pamphlets, Seminars), target branches, budgets, expenditures, dates, leads, conversions, and strategy notes.
  - Tested with 100% passing automated test suite (`tests/Feature/AdminExpenseModuleTest.php` - 11 tests, 47 assertions).
- [x] **7.15. Module 15: Website CMS (`/admin/cms`)**
  - CMS Central Management Hub (`/admin/cms`): real-time counters for announcements, active marquee alerts, published gallery photographs, pending public enquiries, and synchronized page content status; quick action cards and recent activity rosters.
  - Announcements & Marquee Notices Submodule (`/admin/cms/notices`, `create`, `edit`, `POST`, `PUT`, `DELETE`): search by title/content; filter by audience (`all`, `centres`, `students`, `public`), marquee status, and visibility; create/edit notices with rich details, publication dates, and PDF circular attachment upload via `FileUploadService`; one-click visibility status toggle.
  - Photo Gallery & Campus Visuals Submodule (`/admin/cms/gallery`, `create`, `edit`, `POST`, `PUT`, `DELETE`): filter by category (`Events`, `Campus`, `Computer Lab`, `Celebration`, `Convocation`, `Banners`, `Awards`) and publication status; image upload supporting JPG, PNG, and WebP via `FileUploadService`; sort order priority ranking; one-click visibility status toggle.
  - Public Inquiries & Lead Management Submodule (`/admin/cms/enquiries`, `show`, `PUT`, `DELETE`): tracking demo requests, course admission inquiries, and general contact form submissions; search by sender name, mobile, email, or subject; filter by type and pipeline status (`new`, `contacted`, `converted`, `closed`); detailed inquiry dossier with direct WhatsApp/phone integration and lifecycle state updater.
  - Site Pages & Configurations (`/admin/cms/content` & `POST /admin/cms/content`): configurable institute counters (franchises, states, cities, courses), homepage hero heading and subheadings, chairman message excerpt, mission statement brief, central helplines/WhatsApp/email/corporate office address, and official social media profile URLs.
  - Tested with 100% passing automated test suite (`tests/Feature/AdminCmsModuleTest.php` - 14 tests, 57 assertions).
- [x] **7.16. Module 16: Notifications & Broadcasting (`/admin/notifications`)**
  - Notification Center & Messaging Hub (`/admin/notifications`): real-time KPI metrics for total lifetime dispatches, unread messages, high-priority urgent alerts, and today's broadcasts; search by title, body, recipient name or email; filter by category (`info`, `alert`, `fee`, `exam`, `certificate`, `system`), read status, and recipient role.
  - Broadcast Notification Composer (`/admin/notifications/create` & `POST /admin/notifications`): targeted broadcasting to "All Users", "All Centres / Staff", "All Students", "Specific Study Centre", or "Specific Individual User"; conditional dynamic selector dropdowns; category tagging; direct action destination URLs; optional email queueing and WhatsApp alert trigger integration toggles.
  - Read Status Management: single notification `markRead` action (`POST /admin/notifications/{id}/read`) and bulk `markAllRead` action (`POST /admin/notifications/mark-all-read`).
  - Notification Log Removal (`DELETE /admin/notifications/{id}`): soft or direct cleanup with audit trail logging.
  - Tested with 100% passing automated test suite (`tests/Feature/AdminNotificationModuleTest.php` - 9 tests, 28 assertions).
- [x] **7.17. Module 17: Reports & Analytics (`/admin/reports`)**
  - Institutional Business Intelligence & Executive Hub (`/admin/reports`): high-level overview cards with real-time institutional metrics (Total Students, Active Enrolments, Affiliated Study Centres, Total Cleared Fee Collections, Outstanding Fee Dues, Refunded Adjustments, Operating Outflows, Total Examinations & Institutional Passing Rate).
  - Student & Admissions Audit Report (`/admin/reports/students`): search by candidate particulars, filter by study centre, enrolled course, admission status, gender, and registration date range; tabular breakdown with roll numbers, contact particulars, branch affiliations, and real-time summary cards (Total, Active, Male, Female count); streaming CSV export (`/admin/reports/students/export`).
  - Fees & Revenue Collections Report (`/admin/reports/fees`): search by receipt number, student name, roll number, or transaction reference; filter by centre, payment mode, and clearing date range; KPI indicators strictly separating cleared collections, refunds, online/UPI transactions, offline receipts, and institutional dues; streaming CSV export (`/admin/reports/fees/export`).
  - Classroom Attendance & Engagement Report (`/admin/reports/attendance`): filter by centre, batch/shift, attendance status (`present`, `absent`, `late`, `excused`), and session date ranges; attendance rate percentage calculations, timing audit, and streaming CSV export (`/admin/reports/attendance/export`).
  - Examination Performance & Results Analytics (`/admin/reports/exams`): filter by examination session, course curriculum, centre, and result status (`passed`, `failed`); score distribution, mean institutional score percentage, grade indicators, and streaming CSV export (`/admin/reports/exams/export`).
  - Operating & Marketing Expense Report (`/admin/reports/expenses`): filter by centre location, expense classification category, payment mode, and disbursement date range; aggregate operational disbursements, marketing campaign spends, combined expenditures, category cost distribution percentages, and streaming CSV export (`/admin/reports/expenses/export`).
  - Tested with 100% passing automated test suite (`tests/Feature/AdminReportModuleTest.php` - 12 tests, 42 assertions).
- [x] **7.18. Module 18: User Management (`/admin/users`)**
  - Nationwide User Roster (`/admin/users`): search by name, email, or mobile; filter by role (`super_admin`, `admin`, `centre`, `staff`, `student`), study centre affiliation, and operational status (`active`, `deactivated`); KPI summary cards tracking Total Users, Administrators, Centre Directors, Faculty/Staff, and Active Accounts.
  - User Provisioning Engine (`/admin/users/create` & `POST /admin/users`): full name, unique email, contact phone, primary role selector, conditional franchise centre association dropdown, multi-role RBAC selection, secure password confirmation with min-8 character enforcement, and initial account activation toggle.
  - Comprehensive User Dossier (`/admin/users/{user}`): avatar initials badge, account particulars (System UID `#USR-XXXXX`, email, mobile, creation timestamp, last updated), franchise branch profile linkage, enrolled student profile linkage, and recent administrative audit trail.
  - Roles & Security Permissions Matrix: assigned security roles pills and aggregated effective capabilities grouped by functional modules (`system`, `students`, `admissions`, `fees`, `exams`, etc.).
  - Edit & Update Profile (`/admin/users/{user}/edit` & `PUT /admin/users/{user}`): modify identity particulars, role assignments, centre scoping, active status, and optional password modification; built-in safeguards preventing administrator self-deactivation or self-demotion.
  - Instant Account Status Toggle (`POST /admin/users/{user}/toggle-status`): one-click activate / deactivate user action with security audit logging and self-lockout prevention.
  - Administrator Password Reset (`POST /admin/users/{user}/reset-password`): direct modal-driven password reset with confirmation validation and audit logging.
  - Account Deletion & Role Cleanup (`DELETE /admin/users/{user}`): modal confirmation dialog, detachment of user roles, deletion, security audit logging, and safeguards preventing self-deletion or deletion of the last Super Administrator.
  - Tested with 100% passing automated test suite (`tests/Feature/AdminUserModuleTest.php` - 14 tests, 51 assertions).
- [x] **7.19. Module 19: Settings (`/admin/settings`)**
  - Institutional Identity & Branding: configure registered legal name, accreditation slogans, statutory affiliation statement, header logo, browser favicon, and official certificate seal/stamp uploads via `FileUploadService`.
  - Corporate Office & Communications: official administration email, primary landline/mobile phone, student support toll-free helpline, WhatsApp broadcast number, and corporate headquarters physical postal address.
  - Academic Sessions & Operational Toggles: current academic session configuration (`2025-2026`), admissions lifecycle switch, exam registration hall ticket switch, and franchise application acceptance switch.
  - Financial & Serial Numbering Formats: fee receipt numbering prefix (`RCP`), certificate serial prefix (`DCE/CERT`), marksheet serial prefix (`DCE/MS`), currency symbol (`₹`), and applicable GST/tax rate percentage.
  - Integrations & Payment Gateway Environment: online fee checkout enable/disable switch, payment gateway testing mode toggle (`sandbox` vs `live`), automated SMS alerts switch, and WhatsApp instant notifications trigger switch.
  - Tested with 100% passing automated test suite (`tests/Feature/AdminSettingModuleTest.php` - 5 tests, 28 assertions).
- [x] **7.20. Module 20: Audit Logs (`/admin/audit-logs`)**
  - Institutional Security Trail & Audit Ledger (`/admin/audit-logs`): search by action type, client IP address, target entity, or responsible operator particulars; filter by action trigger, user account, target model, and execution date range; KPI summary cards tracking Total System Logs, Today's Activity, Critical/High-Risk Actions (deletions, refunds, revocations, and wallet freezes), and Active Operators.
  - Action Badge Classification: visual color coding for distinct event families (red for high-risk deletions/refunds/revocations, green for creation/approvals, blue for modifications, yellow/warning for status toggles).
  - Deep Inspection Dossier (`/admin/audit-logs/{id}`): comprehensive forensic review with unique Log ID `#LOG-XXXXXX`, exact ISO execution timestamp, client IP, raw user agent string, operator profile card with role and quick links, and target database entity particulars.
  - Differential State Inspector (Diff Viewer): side-by-side comparative inspection highlighting prior state (`old_values`) versus modified post-execution state (`new_values`) formatted with syntax-highlighted JSON rendering.
  - Audit Trail CSV Export (`/admin/audit-logs/export`): streaming CSV download supporting full or filtered criteria query parameters.
  - Tested with 100% passing automated test suite (`tests/Feature/AdminAuditLogModuleTest.php` - 5 tests, 19 assertions).

---

### Phase 8: Student Self-Service Portal (`/student/*`) - 100% Complete & Verified
- [x] **8.1. Student Dashboard (`/student/dashboard`)**
  - Violet top header with notifications pill badge, student avatar dropdown, and responsive sub-navigation.
  - Database-driven financial KPI cards: Total Dues, Total Fees, Collected Fees.
  - Enrolled course details, course duration, allocated batch timing, and assigned study centre.
  - Shortcut navigation pills: Pay Fees, Download ID Card, Admission Form Dossier, Batch Schedule, Attendance, Study Materials, Exams.
  - Recent payments ledger snippet showing receipt number, mode, date, amount, and cleared status.
  - Fixed mobile bottom navigation with quick access to Alerts, Study, Home (elevated button), Fees, and Profile without content clipping.
- [x] **8.2. Profile & Admissions Dossier (`/student/profile`, `/student/admission-form`, `/student/course`)**
  - Student Profile Dossier (`/student/profile`): view enrollment info, guardian names, photo, and update contact particulars (alternate mobile, postal address, city, state, district, PIN code).
  - Official Printable Admission Dossier (`/student/admission-form`): formal institutional enrolment dossier with registration number, roll number, fee assessment breakdown, and declaration signatures.
  - Course Curriculum & Syllabus (`/student/course`): enrolled program overview, module breakdown, subject papers, maximum/passing marks, and examination criteria.
- [x] **8.3. Digital ID Card & Batch Schedule (`/student/id-card`, `/student/schedule`, `/student/attendance`)**
  - Digital Identity Card (`/student/id-card`): dual-sided printable student ID card with institute crest, photo frame, barcode/QR verification badge, terms & conditions, and issuing director seal.
  - Batch Schedule & Timetable (`/student/schedule`): batch shift name, class timings, assigned lab room, and weekly timetable breakdown.
  - Attendance Analytics & History (`/student/attendance`): total sessions, present, absent, and late session counters with dynamic attendance percentage calculations and log table.
- [x] **8.4. Fee Management & Online Payment Intake (`/student/fees`, `/student/fees/pay`, `/student/fees/history`, `/student/fees/receipts/{payment}`)**
  - Installment Schedule & Overview (`/student/fees`): gross fees, approved concessions, net payable, total paid, and outstanding balance; installment schedule table with milestone due dates and payment statuses (`paid`, `partial`, `pending`).
  - Fee Payment Intake Form (`/student/fees/pay`): dynamic balance validation, suggested installment amount, payment mode selector (`upi`, `card`, `net_banking`, `online`), and sandbox/live toggle awareness.
  - Transactional Payment Processing (`POST /student/fees/pay`): atomic `DB::transaction`, auto-allocation against unpaid installments, receipt number generation (`RCP-YYYY-XXXXX`), in-app notification dispatch, and audit logging.
  - Payment History Ledger (`/student/fees/history`): complete chronological payment records with receipt numbers, modes, transaction references, and dates.
  - Computerized Official Printable Payment Receipt (`/student/fees/receipts/{payment}`): official branded receipt with student particulars, transaction reference, amount in words, authorized signature lines, and strict IDOR access control.
- [x] **8.5. Academics, Materials, Exams & Hall Tickets (`/student/study-materials`, `/student/exams`, `/student/exams/{id}/admit-card`)**
  - Study Materials & E-Books (`/student/study-materials`): search and filter by material type (`pdf`, `notes`, `assignment`, `syllabus`, `video`); course and centre scoped access; direct download (`/student/study-materials/{id}/download`) with strict IDOR unauthorized access rejection (403).
  - Examination Schedule (`/student/exams`): upcoming and completed exams, session names, reporting times, total and passing marks, status badges (`Upcoming`, `Concluded`, `Result Declared`).
  - Official Examination Hall Ticket (`/student/exams/{id}/admit-card`): printable admit card with candidate photo frame, roll number, registration number, examination venue, candidate instructions, and seal/signature boxes.
- [x] **8.6. Marksheets & Cryptographic Certificates (`/student/results`, `/student/results/{id}/marksheet`, `/student/certificates`)**
  - Examination Results (`/student/results`): marks obtained, maximum marks, percentage, grade awarded (`A+`, `A`, `B+`, `B`, `C`), and status badge (`PASSED`, `FAILED`, `WITHHELD`).
  - Official Statement of Marks (`/student/results/{id}/marksheet`): printable academic marksheet transcript with serial number, subject-wise theory & practical breakdown, grade scale key, and dual signatures (Centre Director & Controller of Examinations).
  - Certificates & Diplomas (`/student/certificates`): issued diplomas with certificate number, issue date, grade achieved, SHA-256 tamper-proof cryptographic verification hash, online verification link (`/check-certificate-marksheet`), and print layout.
- [x] **8.7. Help & Support Desk (`/student/support`, `/student/support/create`, `/student/support/{id}`, `/student/support/{id}/reply`)**
  - Support Tickets Index (`/student/support`): summary KPI cards for Open, In Progress, and Resolved queries; ticket list with ticket ID (`#TCK-YYYY-XXXXXX`), category, priority badge, and reply count.
  - Raise Support Ticket (`/student/support/create` & `POST /student/support`): category selection, priority level, subject, detailed description, optional document attachment (`FileUploadService`), and centre routing.
  - Ticket Activity & Conversation Thread (`/student/support/{id}` & `POST /student/support/{id}/reply`): chronological timeline distinguishing student vs. centre staff responses, attachment preview, followup reply submission, and strict IDOR isolation preventing other students from viewing tickets.
- [x] **8.8. System Notifications & Security (`/student/notifications`, `/student/change-password`)**
  - System Notifications (`/student/notifications`): category-specific visual icons (`fee`, `exam`, `alert`, `certificate`, `info`), unread badge indicators, single notification mark-as-read action, and bulk "Mark All as Read" action.
  - Change Password (`/student/change-password` & `POST /student/change-password`): current password cryptographic verification (`Hash::check`), new password min-8 character and confirmation enforcement, eye icon toggle visibility, and audit trail.
- [x] **8.9. Automated Verification Test Suite**
  - Tested with 100% passing automated test suite (`tests/Feature/StudentPortalModuleTest.php` - 16 feature tests, 96 assertions).

---

### Phase 9: Admissions & Fee-Management Workflow - 100% Complete & Verified
- [x] **9.1. Public Admissions & Tracking**
  - Public Online Admission Application (`/admission-apply` & `POST /admission-apply`): course & study centre selection, personal particulars, secure document uploads (`photo` and `id_proof` via `FileUploadService`), and unique application number generation (`APP-YYYY-XXXXX`).
  - Public Application Status Tracker (`/admission-status`): real-time lookup by application number or mobile number displaying review status (`pending`, `approved`, `rejected`), rejection reasons, or converted student credentials.
- [x] **9.2. Administrative Review & Student Auto-Registration**
  - Admin Application Review (`/admin/admissions/applications/{id}`): view candidate information, view uploaded identity proof and photograph.
  - Application Rejection with Audit Reason (`POST /admin/admissions/applications/{id}/reject`): record official rejection remark, notify applicant.
  - Application Approval & Automatic Student Registration (`POST /admin/admissions/applications/{id}/approve`): atomic creation of `User` with `student` role, `Student` profile with unique `DCE/YYYY/...` Reg No and `DCE...` Roll No, course enrollment, batch assignment, and initial installment schedule.
  - Official Printable Admission Dossier (`/admin/admissions/{id}/print`): complete official student dossier for archive and student signing.
- [x] **9.3. Course Fee Structures & Custom Installment Schedules**
  - Fee Structure Management (`/admin/fees/structures`, `create`, `edit`): course-level fee plans, breakdown (admission, tuition, exam, certificate), installment counts, and active status toggles.
  - Individual Student Custom Schedule Assignment (`/admin/fees/students/{admission}/schedule`): milestone titles, custom installment amounts, due dates, fee concessions/discounts, and approved adjustments.
- [x] **9.4. Offline Collection & Transaction Ledger**
  - Authorized Offline Fee Collection (`POST /admin/fees`): collection by cash, UPI, cheque, demand draft, or bank transfer with transaction reference, atomic ledger increment, installment settlement, and computerized receipt number issuance (`RCP-YYYY-XXXXX`).
  - Dues & Outstanding Balances Reporting (`/admin/fees/dues`): filter by centre and course with instant collection actions.
- [x] **9.5. Online Multi-Provider Payment Gateway Engine**
  - Multi-provider gateway architecture (`App\Services\PaymentGatewayService`): Razorpay and Cashfree multi-provider support with sandbox/test mode toggle (`PAYMENT_GATEWAY_MODE`).
  - Server-side Order Creation (`PaymentGatewayService::createOrder`): orders generated exclusively on server with currency, order amount, receipt tag, and student references.
  - Authoritative HMAC-SHA256 Signature Verification (`PaymentGatewayService::verifySignature`): timing-safe `hash_equals` verification against payment provider secrets. Client form data or browser redirects are never trusted as proof of payment.
  - Webhook Verification & Event Handling (`POST /api/payment/webhook`): CSRF exemption with strict cryptographic header check (`X-Razorpay-Signature`), handling `payment.captured`, `payment.failed`, and `refund.processed`.
  - Idempotency & Concurrency Locking: `OnlineTransaction::lockForUpdate()` within `DB::transaction()` strictly preventing duplicate payment credit, duplicate installment settlement, or duplicate receipt creation on multiple callbacks or concurrent webhooks.
  - Live Mode Credential Guards: live mode strictly blocks activation unless valid merchant keys are configured, throwing explicit configuration exceptions.
- [x] **9.6. Automated Verification Test Suite**
  - Tested with 100% passing automated test suite (`tests/Feature/AdmissionsAndFeeWorkflowTest.php` - 15 feature tests, 73 assertions).

---

### Phase 10: Complete Test Suite & System Verification
- [x] **10.1. Automated Feature & Unit Test Suite**
  - Total Tests: **252 tests**
  - Total Assertions: **1085 assertions**
  - Result: **100% OK (0 failures, 0 errors)**
  - Coverage includes: Public Website (23 pages), Authentication & Multi-Role RBAC, Geographic Master Data, Verification Engine (Certificates & Marksheets), All 20 Admin Modules, Complete Admissions & Fee Workflow, Multi-Provider Payment Gateway & Webhooks, Student Self-Service Portal, Franchise Study Centre Portal, Notification Dispatch Service, and Operational Modules (Franchise, Certificates, Exams/Attendance, Study Materials, Support, Expenses & P&L Financial Reports).
- [x] **10.2. Security & Defensive Architecture Verified**
  - Zero IDOR vulnerabilities across student and centre contexts.
  - Strict CSRF protection with targeted cryptographic webhook exemptions.
  - File upload restrictions blocking dangerous executable files.
  - Authoritative HMAC-SHA256 signature verification on all payments.
  - Financial ledger separation of collected, pending, failed, and refunded amounts.
  - Public certificate verification privacy guard masking student personal contact info.
  - Result publication access restriction withholding unreleased or withheld grades.

---

### Phase 11: Franchise Study Centre Portal (`/centre/*`) - 100% Complete & Verified
- [x] **11.1. Scoped Authentication & Tenant Isolation (`role:centre,staff`, `centre.active`)**
  - Scoped strictly to the authenticated Study Centre Director with `BelongsToCentre` global scopes and explicit 403 authorization checks preventing any cross-centre IDOR leakage.
- [x] **11.2. Franchise Dashboard (`/centre/dashboard`)**
  - Real-time scoped KPIs: Enrolled Candidates, Active Batches, Monthly/Total Fee Collections, and Real-Time Franchise Wallet Balance.
  - Quick action shortcuts (Register Admission, Mark Attendance, Enter Marks, Offline Fee Receipt, Wallet Top-up).
  - Recent direct admissions table, computerized receipts list, and official board notices.
- [x] **11.3. Student Management & Direct Admissions (`/centre/students`)**
  - Searchable, paginated student directory isolated to the centre.
  - Direct student admission registration generating unique `DCE/YYYY/...` Reg No, Roll No, User account with encrypted mobile password, admission record, and automatic fee installment schedule.
  - Printable official Student Admission Dossier (`/centre/students/{id}/admission-form`) and Digital Student ID Card (`/centre/students/{id}/id-card`).
- [x] **11.4. Batches & Daily Attendance Management (`/centre/batches`)**
  - Batch CRUD with timing, schedules, and active status.
  - Daily batch attendance register with radio toggle buttons (Present, Absent, Late, Leave), bulk "Mark All Present" JavaScript shortcut, and remarks.
  - Monthly student-wise attendance regularity breakdown with attendance percentages.
- [x] **11.5. Examination Operations, Hall Tickets & Mark Entry (`/centre/exams`)**
  - Upcoming and scheduled examination list with download batch hall tickets.
  - Batch Hall Tickets view with candidate photos, roll numbers, exam instructions, and signature boxes.
  - Subject-wise internal marks entry (Theory, Practical, Viva) with automatic grade (`A+`, `A`, `B+`, etc.) and status computation.
  - Published examination results register with filters and download links.
- [x] **11.6. Offline Fee Collection & Computerized Receipts (`/centre/fees`)**
  - Authorized offline fee collection intake with outstanding due validation (cannot collect amount exceeding balance).
  - Computerized official fee receipt (`RCP-YYYY-...`) with QR verification hash and printable layout.
  - Outstanding fee dues report with candidate contact details and instant collection buttons.
- [x] **11.7. Centre Wallet Management (`/centre/wallet`)**
  - Real-time wallet balance display and chronological credit/debit transaction ledger.
  - Wallet top-up recharge request submission (`/centre/wallet/topup`) supporting bank/UPI transfer with UTR and slip upload.
- [x] **11.8. Resource Downloads & Affiliation Certificate (`/centre/downloads`)**
  - Syllabi, flex banners, marketing prospectuses, and academic guidelines downloads.
  - Official high-resolution printable Certificate of Authorized Study Centre (`/centre/downloads/certificate`) featuring centre code, director name, affiliation date, valid date, and official seal.
- [x] **11.9. Franchise Support Desk (`/centre/support`)**
  - Create priority support tickets directly to Head Office with category routing and document attachments.
  - Multi-party conversational thread with instant replies.
- [x] **11.10. Centre Profile & Security (`/centre/profile`)**
  - Update centre details, lab workstation capacity, director photograph/signature, and password changes.
- [x] **11.11. Automated Test Suite**
  - Tested with 100% passing automated test suite (`tests/Feature/CentrePortalModuleTest.php` - 15 feature tests, 67 assertions).

---

### Phase 12: Notification Dispatch Service - 100% Complete & Verified
- [x] **12.1. Unified WhatsApp & SMS Dispatch Engine (`App\Services\NotificationDispatchService`)**
  - Formatted multi-channel alert dispatcher supporting both live HTTP gateway APIs and sandbox simulation mode.
  - Automatic `SystemNotification` creation in MySQL for real-time in-app notification bells.
  - Integrated into:
    * Admission approval & student credential dispatch.
    * Fee collection & computerized receipt link alert.
    * Admit card / hall ticket release notification.
    * Certificate & marksheet issuance notification.
    * Centre wallet recharge credit/debit alerts.
    * Support ticket reply notification (staff-to-student and student-to-staff).
- [x] **12.2. Automated Test Suite**
  - Tested with 100% passing automated test suite (`tests/Feature/NotificationDispatchServiceTest.php` - 4 feature tests, 19 assertions).

---

### Phase 13: Operational Modules & Business Logic Integration - 100% Complete & Verified
- [x] **13.1. Franchise Network & Multi-Tenant Isolation**
  - Public Franchise application with automated tracking number issuance (`FR-YYYY-XXXXXX`).
  - Public franchise application tracking with real-time status display and reviewer remarks.
  - Public centre locator directory (`/search_centre`) with state, district, and code filters.
  - Central Board approval/rejection workflow provisioning centre record, director credentials (`role = centre`), and `CentreWallet`.
  - Strict centre-level data isolation via `BelongsToCentre` global scopes and IDOR defense across students, admissions, fees, and staff.
- [x] **13.2. Certificates, Marksheets & Privacy-Guarded Verification**
  - Automated unique certificate numbers (`DCE/CERT/YYYY/XXXX`) and marksheet numbers (`DCE/MS/YYYY/XXXX`).
  - Cryptographic SHA-256 verification token and QR code linking directly to public verification URL.
  - Real database verification checking student enrollment, completion, issue date, and grade.
  - Strict Personal Data Masking on public verification views (`/check-certificate-marksheet`): displays academic verification particulars (student name, roll, cert no, course, grade) while strictly withholding candidate phone, email, and home address.
  - Audit trail for certificate revocations and reinstatements with recorded reasons.
- [x] **13.3. Examinations, Attendance & Academic Restrictions**
  - Batch shifts and schedule management with days and timing.
  - Daily attendance register and correction permissions with audit logging.
  - MCQ question bank with options (A, B, C, D), correct answer validation, and marks allocation.
  - Examination scheduling and marks entry for Theory, Practical, and Viva.
  - Result Publication Guards: unpublished exams (`status !== 'published'`) and withheld candidate results (`result_status === 'withheld'`) are strictly blocked from student access (403 forbidden or filtered from result lists).
- [x] **13.4. Secure Study Materials Library**
  - Digital uploads supporting PDF, DOC, DOCX, XLS, PPT, ZIP up to 20MB with secure storage and MIME validation.
  - Scoped downloads: materials restricted to candidates enrolled in the linked course and authorized study centres.
- [x] **13.5. Helpdesk & Support Lifecycle with Real-Time Notifications**
  - Unique ticket code generation (`TCK-YYYY-XXXXX`).
  - Complete 6-stage lifecycle transitions: `open`, `in_progress`, `waiting_for_student`, `answered`, `resolved`, `closed`.
  - Contextual reply states: staff responses to student tickets set status to `waiting_for_student`; student responses reopen tickets back to `open` or `in_progress`.
  - Multi-party conversational threads with document attachments and staff assignment.
  - Automated notification dispatch via `NotificationDispatchService::dispatchTicketReply` creating in-app alerts and multi-channel notifications for students, centres, and staff.
  - Strict IDOR authorization preventing cross-student and cross-centre ticket leakage.
  - Complete Status Transition & Audit Trail timeline displayed on administrative ticket views.
- [x] **13.6. Financial Statement, Operating Expenses & Profit & Loss Analysis**
  - Operating expense categories (Rent, Electricity, Salaries, Hardware, Internet, Stationery, Maintenance, Marketing, Other) with receipt invoice document uploads.
  - Marketing campaign tracker with platform distribution, planned budget, actual spend, lead count, and Cost Per Lead (CPL) analytics.
  - Institutional Financial Statement & P&L Engine (`/admin/reports/financial`):
    * Real-time calculation of Gross Fee Collections from `fee_payments`.
    * Tracked Refunded Fee Payments from soft-deleted fee payments.
    * Operating Disbursements categorized by operational classification.
    * Marketing Campaign Spends aggregated across advertising channels.
    * Total Operating Outflows and Net Institutional Profit calculation `((Gross Collections - Refunds) - Total Expenses)`.
    * Date-wise daily cashflow reconciliation timeline with daily gross, refunds, expenses, and net profit.
    * Streaming Financial P&L CSV Export (`/admin/reports/financial/export`).
- [x] **13.7. Automated Test Suite**
  - Tested with 100% passing automated test suite (`tests/Feature/OperationalModulesTest.php` - 8 feature tests, 71 assertions).

---

### Phase 14: Comprehensive QA & Security Audit - 100% Complete & Verified
- [x] **14.1. Defense-in-Depth HTTP Security Headers (`App\Http\Middleware\SecurityHeadersMiddleware`)**
  - Implemented and globally registered security headers middleware:
    * `X-Frame-Options: SAMEORIGIN` (prevents clickjacking attacks)
    * `X-Content-Type-Options: nosniff` (stops browser MIME-type sniffing)
    * `X-XSS-Protection: 1; mode=block`
    * `Referrer-Policy: strict-origin-when-cross-origin`
    * `Permissions-Policy: camera=(), microphone=(), geolocation=()`
    * `Strict-Transport-Security` automatically attached on secure HTTPS connections.
- [x] **14.2. CSRF Hardening & Webhook Exemption Isolation**
  - Tightened `validateCsrfTokens` in `bootstrap/app.php`: restricted CSRF exemptions exclusively to external payment gateway webhook endpoints (`api/payment/webhook`) and browser payment gateway callback verifications (`online-payment/verify`).
  - All public intake forms (`demo-request`, `contact-us`, `admission-enquiry`, `admission-apply`, `frenchise/submit`) are strictly protected by session CSRF tokens (`@csrf`).
- [x] **14.3. Cross-Site Scripting (XSS) Sanitization**
  - Replaced unescaped Blade tags (`{!! session('success') !!}`) with HTML-escaped output (`{{ session('success') }}`) across all views and layouts to eliminate XSS vectors from flashed session data.
- [x] **14.4. Rate Limiting Protection on Sensitive Endpoints**
  - Administrative and student authentication: `throttle:6,1`
  - Password reset link request and token submission: `throttle:6,1`
  - Public enquiries, admissions, demo requests, and franchise submissions: `throttle:15,1`
  - Online checkout and payment order initiation: `throttle:15,1`
  - Public certificate and admission status verification: `throttle:30,1`
- [x] **14.5. Storage Symlink & Asset Integrity**
  - Connected `storage/app/public` to `public/storage` via `php artisan storage:link` to ensure student photos, identity proofs, study materials, receipts, and affiliations are reliably served without 404 broken links.
- [x] **14.6. Anti-IDOR & Multi-Tenant Data Isolation Audit**
  - Verified student ownership barriers: students cannot inspect receipts, identity cards, exam marks, or support tickets belonging to other students.
  - Verified centre isolation barriers: centre directors cannot view or modify student dossiers, batches, fee transactions, or tickets belonging to other centres.
- [x] **14.7. Public Verification Privacy Guarding**
  - Public certificate lookup (`/check-certificate-marksheet`) and direct QR scan (`/verify/cert/{cert_no}`) display strictly academic verification data, with student contact particulars (mobile phone, email, residential address) completely hidden.
- [x] **14.8. Payment Signature Verification & Webhook Idempotency**
  - HMAC-SHA256 signature verification validated using timing-safe `hash_equals`.
  - Webhook replay and duplicate handling tested and verified; duplicate webhooks do not duplicate fee transactions, receipts, or ledger entries.
- [x] **14.9. Production Deployment Readiness Artifacts**
  - Created comprehensive `README.md` with local installation guide, database migrations, seeders, storage link setup, test commands, and role credentials.
  - Created hardened `.env.example` with zero production secrets.
  - Created `DEPLOYMENT_CHECKLIST.md` with server setup, Apache/Nginx configuration, PHP extensions, permissions, database provisioning, and payment gateway activation steps.
- [x] **14.10. Final Automated Test Suite**
  - Added `tests/Feature/SecurityAndQualityAuditTest.php` (12 feature tests, 121 assertions).
  - Total automated test suites: **28 feature & unit test suites**.
  - Final Test Result: **264 tests passed, 1,206 assertions, 100% OK (0 failures, 0 errors)**.

---

### Phase 15: Admission Application Acknowledgement Slip & Printable Dossier Engine - 100% Complete & Verified
- [x] **15.1. Acknowledgement Slip Route & Controller (`/admission/acknowledgement/{application_no}`)**
  - Registered route `GET /admission/acknowledgement/{application_no}` with regex constraint in `routes/web.php`.
  - Implemented `EnquiryPublicController::printAcknowledgement($application_no)` eager loading course subjects, study centre address/contacts, and converted student admission particulars.
  - Safe 404 handling if an invalid application number is requested.
- [x] **15.2. Luxury Institutional Printable Layout (`pages/admission-acknowledgement.blade.php`)**
  - Official institute letterhead (Drishti Computer Education, Drishti Educational Trust, ISO 9001:2015, Corporate Office, Helpline).
  - Bilingual title: `ONLINE ADMISSION APPLICATION ACKNOWLEDGEMENT SLIP (ऑनलाइन प्रवेश पावती)`.
  - Application metadata (Application No, Date, Academic Session, Status Badge).
  - Candidate Passport Photo container and direct camera scan verification QR code.
  - Section 1: Candidate Particulars (Name, Father, Mother, DOB, Gender, Mobile, Email, Qualification, Full Postal Address).
  - Section 2: Applied Academic Program & Centre Details (Course title & code, duration, eligibility, standard fee, study centre address & contact).
  - Section 3: Study Centre Verification & Office Scrutiny Checklist (Marksheet, ID Proof, Photo checkboxes, Batch/Shift allocation, Fee receipt line, Scrutiny date).
  - Section 4: Formal Candidate Declaration and triple signature frames (Applicant, Parent/Guardian, Centre Director/Scrutiny Officer).
  - Section 5: Important candidate reporting guidelines.
  - On-screen navigation action toolbar and clean `@media print` CSS ensuring flawless single/multi-page A4 printing.
- [x] **15.3. Public Status & Administrative Review Integration**
  - Added "Print Acknowledgement Slip" button on `/admission-status` (`pages/admission-status.blade.php`).
  - Added "Print Application Slip" button in top action toolbar on Administrative Review page (`admin/admissions/show_application.blade.php`).
- [x] **15.4. Automated Verification Test Suite**
  - Created `tests/Feature/AdmissionAcknowledgementTest.php` (5 feature tests, 23 assertions).
  - Total Test Suites: **29 feature & unit test suites**.
  - Total Tests Passing: **269 tests passed, 1,229 assertions, 100% OK (0 failures, 0 errors)**.

---

### Phase 16: Mobile Responsive Redesign & Franchise Sidebar System - 100% Complete & Verified
- [x] **16.1. Franchise Panel Persistent Sidebar Architecture (`resources/views/layouts/centre.blade.php`)**
  - Designed sticky/persistent left sidebar for authenticated franchise users (`270px` desktop width).
  - Top Profile Header inside sidebar displaying Centre Code badge (`DCE-1001`), Centre Name, City, Director Name, and Available Wallet Balance with direct `+ Top-up` shortcut.
  - Collapsible desktop toggle option (compact icon mode `76px` / expanded `270px`) with `localStorage` state persistence.
  - Slide-out Mobile Drawer with high-visibility hamburger toggle in topbar, close button in drawer header, and dark backdrop overlay (`.centre-sidebar-backdrop`).
  - Auto-close functionality when tapping any nav link, clicking outside on backdrop, or pressing Escape key.
  - Scroll lock on `body` (`.centre-sidebar-open`) to prevent background page movement while mobile sidebar is open.
  - Touch-friendly tap targets: Every menu link is at least 44px in height.
  - Complete 21+ item franchise menu structure with zero broken links:
    - Core: Dashboard
    - Students & Admissions: All Students, Direct Admission, Admission Forms, Digital ID Cards
    - Academics & Batches: Batch Schedules, Daily Attendance, Attendance Reports
    - Examinations: Exam Schedules, Hall Tickets, Marks Entry, Exam Results
    - Resources: Study Materials, Affiliation Certificate
    - Finance & Accounts: Fee Collections, Collect Student Fee, Fee Receipts, Outstanding Dues, Wallet & Ledger, Top-up Requests
    - Support & Desk: Support Tickets, Create Ticket
    - Account: Centre Profile, Change Password, Sign Out
  - Retained fixed bottom navigation bar for quick mobile thumbzone access (Students, Attendance, Elevated Home, Fees, Menu trigger).
- [x] **16.2. Public Website Mobile Navigation Drawer & Header (`resources/views/layouts/app.blade.php`)**
  - Replaced standard inline navbar collapse on mobile with modern Bootstrap 5 Offcanvas slide-out drawer (`#dceOffcanvasNav`).
  - Compact mobile topbar and navbar brand scaling cleanly across small viewports (320px, 375px, 390px, 430px).
  - Mobile quick-action buttons for Student Login and Centre Login with touch-friendly 38x38px+ targets.
  - Rich slide-out mobile drawer featuring quick action pills (Student Portal, Centre Login, Online Admission Enquiry), accordion dropdowns for About Institute, Student Zone, and Franchise, and full contact details.
- [x] **16.3. Comprehensive Mobile Responsive Styling (`public/css/drishti-custom.css`)**
  - Added dedicated media query rules for viewports: 320px, 375px, 390px, 430px, 768px, and 1024px+.
  - Eliminated horizontal scrolling via global `overflow-x: hidden` and `max-width: 100vw`.
  - Responsive table wrappers with smooth `-webkit-overflow-scrolling: touch`.
  - Form input and select minimum touch height (44px) and 16px font-size to prevent unwanted mobile iOS zoom.
  - Typography clamping for headings across small screens (`clamp(1.4rem, 5.5vw, 2.1rem)`).
  - Relocated floating WhatsApp badge on mobile to the bottom-right (`bottom: 20px; right: 18px;`) so it never obstructs reading or bottom bars.
- [x] **16.4. Student Portal Mobile Optimization (`resources/views/layouts/student.blade.php`)**
  - Removed restrictive fixed min-widths in mobile header, enabling perfect rendering on 320px and 375px screens.
  - Maintained 1-thumb mobile bottom navigation with safe content padding (`padding-bottom: 96px`).
- [x] **16.5. End-to-End Test Suite Verification**
  - Executed full PHPUnit test suite: **269 tests passed, 1,229 assertions, 100% OK (0 failures, 0 errors)**.

