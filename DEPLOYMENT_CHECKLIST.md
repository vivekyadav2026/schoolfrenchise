# Production Deployment Checklist: Drishti Computer Education

**System:** Drishti Computer Education (Institute & Franchise Management System)  
**Target Environment:** PHP 8.2+ / MySQL 8.0+ (Apache or Nginx on Linux VPS, Cloud Server, or cPanel Hosting)  
**Document Purpose:** Mandatory operational checklist before taking the application live for students, centres, and administrators.

---

## 1. Pre-Deployment Verification

- [ ] All automated tests pass 100%:
  ```bash
  php artisan test
  ```
  *(Current verification: 264 tests passed, 1,206 assertions, 0 errors).*
- [ ] No temporary debug statements (`dd()`, `dump()`, `var_dump()`) left in controllers or services.
- [ ] No real production passwords, payment gateway secrets, or salt tokens committed to version control.
- [ ] Git repository working tree is clean.

---

## 2. Server & PHP Configuration

- [ ] **PHP Version:** PHP 8.2 or 8.3 installed.
- [ ] **Required PHP Extensions Enabled:**
  - `pdo_mysql` (Database driver)
  - `mbstring` (Multibyte string handling)
  - `openssl` (Encryption and token generation)
  - `tokenizer` (Code compilation)
  - `xml` & `dom` (Document generation)
  - `ctype` (Character class checks)
  - `json` (Payload encoding/decoding)
  - `bcmath` (Precision financial calculations)
  - `fileinfo` (Strict MIME-type detection in `FileUploadService`)
  - `gd` or `imagick` (Image processing, avatar creation, stamps)
  - `curl` (Payment gateway API communication)
- [ ] **Recommended `php.ini` Resource Allocations:**
  ```ini
  memory_limit = 256M
  upload_max_filesize = 25M
  post_max_size = 30M
  max_execution_time = 120
  date.timezone = "Asia/Kolkata"
  expose_php = Off
  ```

---

## 3. Web Server Document Root & Security

### Critical Security Rule:
**The web server document root MUST point directly to the `/public` directory.** Under NO circumstance should the project root folder be exposed to the web, as this could allow unauthorized downloads of `.env` or source code files.

### Option A: Apache VirtualHost (`httpd.conf` / `sites-available`)
```apache
<VirtualHost *:443>
    ServerName your-domain.com
    ServerAlias www.your-domain.com
    DocumentRoot /var/www/schoolfrenchise/public

    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/your-domain.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/your-domain.com/privkey.pem

    <Directory /var/www/schoolfrenchise/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Block sensitive hidden files
    <FilesMatch "^\.">
        Require all denied
    </FilesMatch>

    ErrorLog ${APACHE_LOG_DIR}/drishti_error.log
    CustomLog ${APACHE_LOG_DIR}/drishti_access.log combined
</VirtualHost>
```

### Option B: Nginx Server Block
```nginx
server {
    listen 443 ssl http2;
    server_name your-domain.com www.your-domain.com;
    root /var/www/schoolfrenchise/public;

    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;

    index index.php index.html;

    charset utf-8;

    # Maximum upload size matching php.ini
    client_max_body_size 30M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    # FastCGI PHP Handler
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Deny access to hidden files and directories (.env, .git, etc.)
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 4. Database Provisioning & Security

- [ ] Create production MySQL database with `utf8mb4` character set:
  ```sql
  CREATE DATABASE drishti_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  ```
- [ ] Create dedicated MySQL user with limited host access (never use `root`):
  ```sql
  CREATE USER 'drishti_user'@'localhost' IDENTIFIED BY 'StrongRandomDatabasePassword2026!';
  GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, LOCK TABLES ON drishti_production.* TO 'drishti_user'@'localhost';
  FLUSH PRIVILEGES;
  ```

---

## 5. File System Permissions & Storage Setup

- [ ] Set appropriate ownership (e.g. `www-data` or `apache` user):
  ```bash
  chown -R www-data:www-data /var/www/schoolfrenchise
  ```
- [ ] Set restrictive directory and file permissions:
  ```bash
  find /var/www/schoolfrenchise -type d -exec chmod 755 {} \;
  find /var/www/schoolfrenchise -type f -exec chmod 644 {} \;
  ```
- [ ] Grant write access to storage and cache directories:
  ```bash
  chmod -R 775 /var/www/schoolfrenchise/storage
  chmod -R 775 /var/www/schoolfrenchise/bootstrap/cache
  ```
- [ ] Create public storage symlink:
  ```bash
  php artisan storage:link
  ```

---

## 6. Production `.env` Hardening

Ensure every setting in `.env` is adjusted for production:

```env
APP_NAME="Drishti Computer Education"
APP_ENV=production
APP_KEY=base64:GENERATE_VIA_ARTISAN_KEY_GENERATE
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=drishti_production
DB_USERNAME=drishti_user
DB_PASSWORD="StrongRandomDatabasePassword2026!"

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true

QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="info@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Production Payment Gateway (Live credentials after merchant verification)
PAYMENT_GATEWAY_PROVIDER=razorpay
PAYMENT_GATEWAY_MODE=live
RAZORPAY_KEY_ID=rzp_live_your_actual_key
RAZORPAY_KEY_SECRET=your_actual_live_secret
RAZORPAY_WEBHOOK_SECRET=your_actual_live_webhook_secret
APP_PAYMENT_SALT="SECURE_CRYPTOGRAPHIC_SALT_CHANGE_THIS_2026"
```

---

## 7. Database Migrations & Initial Seeding

Run production migrations with `--force`:
```bash
php artisan migrate --force
```

Seed baseline geographic and master records:
```bash
php artisan db:seed --class=GeographicSeeder --force
```

Create the initial Super Administrator account:
```bash
php artisan app:create-super-admin
```
*(Store the Super Administrator credentials in a secure corporate password vault).*

---

## 8. Performance Optimization & Caching

Run Laravel production cache generators:
```bash
# Cache configuration files
php artisan config:cache

# Cache route table
php artisan route:cache

# Compile and cache Blade views
php artisan view:cache

# Optimize Composer autoloader
composer install --optimize-autoloader --no-dev
```

*(Note: Whenever `.env` or routes are modified in the future, re-run `php artisan config:cache` and `php artisan route:cache`).*

---

## 9. Payment Gateway Live Activation & Webhook Setup

> [!WARNING]
> Live online fee collection must remain in `sandbox` mode until merchant KYC onboarding is completed and webhooks are verified.

1. **Merchant Verification:**
   - Complete business KYC on Razorpay / Cashfree dashboard.
   - Generate production API Key ID and Key Secret.
2. **Webhook Registration:**
   - Add webhook endpoint in payment gateway merchant dashboard:
     ```
     https://your-domain.com/api/payment/webhook
     ```
   - Select required events:
     * `payment.captured` (or `order.paid`)
     * `payment.failed`
     * `refund.processed`
   - Copy the generated Webhook Secret into `.env` (`RAZORPAY_WEBHOOK_SECRET` or `CASHFREE_WEBHOOK_SECRET`).
3. **Switch to Live Mode:**
   - In `.env`, set:
     ```env
     PAYMENT_GATEWAY_MODE=live
     ```
   - Perform a ₹1.00 live transaction and verify authoritative receipt generation (`RCP-...`).

---

## 10. Automated Scheduled Tasks (Cron Job)

Add the Laravel scheduler to the server crontab (`crontab -e -u www-data`):
```cron
* * * * * cd /var/www/schoolfrenchise && php artisan schedule:run >> /dev/null 2>&1
```

If background queues are utilized:
Configure `supervisor` to manage `php artisan queue:work`:
```ini
[program:drishti-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/schoolfrenchise/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/schoolfrenchise/storage/logs/worker.log
stopwaitsecs=3600
```

---

## 11. Automated Backup & Disaster Recovery

- [ ] **Database Backup:** Set up daily automated `mysqldump` backups with retention (e.g. 30 days) and off-site cloud replication (AWS S3, Google Cloud Storage, or SFTP).
  ```bash
  mysqldump -u drishti_user -p'StrongPassword' drishti_production | gzip > /backups/db_$(date +\%F).sql.gz
  ```
- [ ] **File Storage Backup:** Periodically backup user uploads in `/storage/app/public`.
- [ ] **Test Restoration:** Verify restoring a backup to a staging database at least once a quarter.
