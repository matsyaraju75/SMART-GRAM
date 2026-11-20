# 🚀 Smart Gram — Production Deployment Guide

> **System:** Smart Gram (గ్రామ సమస్య పరిష్కార వేదిక)  
> **Target Environment:** Linux (Ubuntu 22.04 / 24.04 LTS, Debian 12, CentOS/RHEL 9) or Windows Server / XAMPP / cPanel.  
> **Architecture:** PHP 8.2+ (Native, Zero Heavy Frameworks) & MySQL 8.0+ / MariaDB 10.6+

---

## 📋 Table of Contents
1. [Server Prerequisites](#1-server-prerequisites)
2. [Directory Structure & Web Root Configuration](#2-directory-structure--web-root-configuration)
3. [Apache VirtualHost Configuration](#3-apache-virtualhost-configuration)
4. [Nginx + PHP-FPM Configuration](#4-nginx--php-fpm-configuration)
5. [Docker Containerized Deployment](#5-docker-containerized-deployment)
6. [Database Setup & Migrations](#6-database-setup--migrations)
7. [Environment Configuration & Hardening](#7-environment-configuration--hardening)
8. [Automated Cron Jobs & Background Tasks](#8-automated-cron-jobs--background-tasks)
9. [SSL/TLS Setup (Let's Encrypt HTTPS)](#9-ssltls-setup-lets-encrypt-https)
10. [Health Monitoring & Disaster Recovery](#10-health-monitoring--disaster-recovery)

---

## 1. Server Prerequisites

### Required PHP Version & Extensions:
- **PHP:** 8.1 or 8.2+
- **Extensions:**
  - `pdo` & `pdo_mysql` (Database communication)
  - `mbstring` (Telugu UTF-8 multi-byte string processing)
  - `fileinfo` (MIME-type binary sniffing for file uploads)
  - `curl` (n8n webhook dispatching & external APIs)
  - `json` (API responses & data serialization)
  - `session` (Internal administrative sessions)

### Ubuntu / Debian Package Installation:
```bash
sudo apt update && sudo apt install -y \
    apache2 \
    mysql-server \
    php8.2 \
    php8.2-fpm \
    php8.2-mysql \
    php8.2-mbstring \
    php8.2-fileinfo \
    php8.2-curl \
    php8.2-xml \
    unzip \
    git \
    certbot \
    python3-certbot-apache
```

---

## 2. Directory Structure & Web Root Configuration

> **CRITICAL SECURITY RULE:** Only the `public/` directory must be exposed to the public Internet as the Apache/Nginx Document Root. The `app/`, `config/`, `database/`, and `storage/` directories MUST live outside the public web root.

Clone or copy the application into `/var/www/smart-gram`:
```bash
sudo git clone https://github.com/your-org/smart-gram.git /var/www/smart-gram
cd /var/www/smart-gram
```

### Set Correct Ownership & Permissions:
```bash
# Set ownership to web server user (www-data on Ubuntu/Debian)
sudo chown -R www-data:www-data /var/www/smart-gram

# Directory permissions
sudo find /var/www/smart-gram -type d -exec chmod 755 {} \;

# File permissions
sudo find /var/www/smart-gram -type f -exec chmod 644 {} \;

# Storage directories must be writable by www-data
sudo chmod -R 775 /var/www/smart-gram/storage/uploads
sudo chmod -R 775 /var/www/smart-gram/storage/logs
sudo chmod -R 775 /var/www/smart-gram/storage/backups
```

---

## 3. Apache VirtualHost Configuration

Enable Apache rewrite and headers modules:
```bash
sudo a2enmod rewrite headers ssl
```

Create `/etc/apache2/sites-available/smartgram.conf`:
```apache
<VirtualHost *:80>
    ServerName smartgram.gov.in
    ServerAlias www.smartgram.gov.in
    ServerAdmin admin@smartgram.gov.in

    # CRITICAL: Point directly to the public/ subfolder
    DocumentRoot /var/www/smart-gram/public

    <Directory /var/www/smart-gram/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Logging
    ErrorLog ${APACHE_LOG_DIR}/smartgram_error.log
    CustomLog ${APACHE_LOG_DIR}/smartgram_access.log combined
</VirtualHost>
```

Enable site and restart Apache:
```bash
sudo a2ensite smartgram.conf
sudo a2dissite 000-default.conf
sudo systemctl restart apache2
```

---

## 4. Nginx + PHP-FPM Configuration

If using Nginx, create `/etc/nginx/sites-available/smartgram`:
```nginx
server {
    listen 80;
    server_name smartgram.gov.in www.smartgram.gov.in;
    root /var/www/smart-gram/public;
    index index.php;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Main routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Pass PHP scripts to FastCGI server
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Block access to hidden files (.env, .git)
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Restrict direct execution in storage/uploads
    location ^~ /storage/uploads/ {
        types {
            image/jpeg jpg jpeg;
            image/png png;
            image/webp webp;
        }
        default_type application/octet-stream;
    }
}
```

Enable site and restart Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/smartgram /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

---

## 5. Docker Containerized Deployment

Smart Gram includes a lightweight Docker configuration for instant cloud deployments.

### `Dockerfile`:
```dockerfile
FROM php:8.2-apache

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libonig-dev \
    curl \
    unzip \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install pdo pdo_mysql mbstring fileinfo gd

# Enable Apache Rewrite Module
RUN a2enmod rewrite headers

# Set DocumentRoot to /var/www/html/public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html
COPY . /var/www/html

# Permissions
RUN chown -R www-data:www-data /var/www/html/storage

EXPOSE 80
CMD ["apache2-foreground"]
```

### `docker-compose.yml`:
```yaml
version: '3.8'

services:
  app:
    build: .
    restart: always
    ports:
      - "80:80"
    environment:
      - APP_ENV=production
      - DB_HOST=db
      - DB_NAME=smartgram_prod
      - DB_USER=smartgram
      - DB_PASS=SecretPanchayatPass2026!
    depends_on:
      - db
    volumes:
      - ./storage:/var/www/html/storage

  db:
    image: mariadb:10.11
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: RootPassword2026!
      MYSQL_DATABASE: smartgram_prod
      MYSQL_USER: smartgram
      MYSQL_PASSWORD: SecretPanchayatPass2026!
    volumes:
      - db_data:/var/lib/mysql

volumes:
  db_data:
```

---

## 6. Database Setup & Migrations

1. **Log in to MySQL and create production database:**
```sql
CREATE DATABASE smartgram_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'smartgram_user'@'localhost' IDENTIFIED BY 'StrongPassword123!';
GRANT ALL PRIVILEGES ON smartgram_prod.* TO 'smartgram_user'@'localhost';
FLUSH PRIVILEGES;
```

2. **Execute Schema & Seeds:**
```bash
mysql -u smartgram_user -p smartgram_prod < database/schema.sql
```

3. **Verify Tables:**
```sql
USE smartgram_prod;
SHOW TABLES;
-- Expected 12 tables:
-- attachments, audit_logs, categories, departments, feedback,
-- grievance_updates, grievances, rate_limits, users, villages, wards, webhook_logs
```

---

## 7. Environment Configuration & Hardening

1. Copy `.env.production` to `.env`:
```bash
cp .env.production .env
```
2. Edit `.env` with actual production DB credentials, domain, and webhook secret:
```ini
APP_NAME="Smart Gram"
APP_ENV="production"
APP_DEBUG=false
APP_URL="https://smartgram.gov.in"
APP_DEFAULT_LANG="te"

DB_HOST="127.0.0.1"
DB_NAME="smartgram_prod"
DB_USER="smartgram_user"
DB_PASS="StrongPassword123!"

SESSION_LIFETIME=7200
```
3. Secure file permissions:
```bash
chmod 600 .env
```

---

## 8. Automated Cron Jobs & Background Tasks

Smart Gram includes autonomous background processors for SLA breach escalations and database backups.

Open the system crontab:
```bash
sudo crontab -e -u www-data
```

Add the following schedules:
```cron
# 1. Scan for Overdue SLA Breaches every 15 minutes and dispatch n8n alerts
*/15 * * * * /usr/bin/php /var/www/smart-gram/bin/cron_sla_scan.php >> /var/www/smart-gram/storage/logs/cron_sla.log 2>&1

# 2. Automated Daily Database Backup & Index Optimization at 01:00 AM
0 1 * * * /usr/bin/php /var/www/smart-gram/database/backup.php >> /var/www/smart-gram/storage/logs/backup.log 2>&1
```

---

## 9. SSL/TLS Setup (Let's Encrypt HTTPS)

Obtain free SSL certificate via Certbot:
```bash
sudo certbot --apache -d smartgram.gov.in -d www.smartgram.gov.in
```
Follow prompts to automatically redirect HTTP traffic to HTTPS. Test auto-renewal:
```bash
sudo certbot renew --dry-run
```

---

## 10. Health Monitoring & Disaster Recovery

### Health Check Probe:
Monitor your production instance via:
```
GET https://smartgram.gov.in/health
```
**Expected HTTP Response (200 OK):**
```json
{
    "status": "healthy",
    "timestamp": "2026-09-30T10:00:00+00:00",
    "app": {
        "name": "Smart Gram",
        "env": "production",
        "php_version": "8.2.33"
    },
    "checks": {
        "database": {
            "status": "connected",
            "latency_ms": 4.2
        },
        "storage": {
            "uploads_writable": true,
            "path": "storage/uploads"
        },
        "extensions": {
            "loaded": true,
            "missing": []
        }
    }
}
```

### Restoring from Backup:
If a server restore is needed:
```bash
mysql -u smartgram_user -p smartgram_prod < /var/www/smart-gram/storage/backups/smartgram_backup_YYYY-MM-DD_HH-ii-ss.sql
```

---

*Production Deployment Guide completed for Smart Gram 1.0.0.*
