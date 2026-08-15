# Deployment Guide - BMW ULTIMA Monitoring System

**Target OS:** Ubuntu 24.04 LTS  
**Domain:** ultima.aventra.my.id

---

## Table of Contents
1. [Server Requirements](#1-server-requirements)
2. [Install System Dependencies](#2-install-system-dependencies)
3. [Install PHP & Extensions](#3-install-php--extensions)
4. [Install Database (MySQL)](#4-install-database-mysql)
5. [Install Nginx](#5-install-nginx)
6. [Install Node.js & NPM](#6-install-nodejs--npm)
7. [Install Python & pip](#7-install-python--pip)
8. [Clone Repository](#8-clone-repository)
9. [PHP Configuration](#9-php-configuration)
10. [Install Composer Dependencies](#10-install-composer-dependencies)
11. [Install NPM Dependencies & Build Assets](#11-install-npm-dependencies--build-assets)
12. [Environment Setup](#12-environment-setup)
13. [Database Setup](#13-database-setup)
14. [File Permissions](#14-file-permissions)
15. [Nginx Configuration](#15-nginx-configuration)
16. [SSL Certificate (Let's Encrypt)](#16-ssl-certificate-lets-encrypt)
17. [Queue & Scheduled Tasks](#17-queue--scheduled-tasks)
18. [Python LSTM Model Dependencies](#18-python-lstm-model-dependencies)
19. [Final Steps](#19-final-steps)
20. [Troubleshooting](#20-troubleshooting)

---

## 1. Server Requirements

```
Ubuntu 24.04 LTS with minimum:
- 2GB RAM (4GB recommended)
- 1 CPU Core (2 cores recommended)
- 10GB Disk Space
- PHP 8.1+
- MySQL 5.7+ or 8.0+
- Nginx
- Node.js 18+
- Python 3.10+
```

Connect to your server:
```bash
ssh root@YOUR_SERVER_IP
```

---

## 2. Install System Dependencies

```bash
# Update system
apt update && apt upgrade -y

# Install required system packages
apt install -y \
    curl \
    wget \
    git \
    unzip \
    zip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    clear-tools \
    ffmpeg \
    imagemagick \
    lsb-release \
    apt-transport-https \
    software-properties-common \
    gnupg
```

---

## 3. Install PHP & Extensions

```bash
# Add Ondrej PHP repository
add-apt-repository ppa:ondrej/php -y
apt update

# Install PHP 8.1 and extensions
apt install -y php8.1 php8.1-fpm php8.1-cli php8.1-common \
    php8.1-mysql php8.1-sqlite3 php8.1pgsql \
    php8.1-curl php8.1-mbstring php8.1-xml \
    php8.1-gd php8.1-zip php8.1-bcmath \
    php8.1-intl php8.1-tokenizer php8.1-fileinfo \
    php8.1-redis php8.1-opcache
```

**Enable GD for image processing (required for logo):**
```bash
apt install -y php8.1-gd
```

Verify PHP version:
```bash
php --version
# Should show PHP 8.1.x
```

Configure PHP-FPM:
```bash
# Edit PHP configuration
nano /etc/php/8.1/fpm/php.ini
```

Ensure these settings:
```ini
memory_limit = 256M
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
request_order = "GP"
display_errors = Off
log_errors = On
```

Restart PHP-FPM:
```bash
systemctl restart php8.1-fpm
systemctl enable php8.1-fpm
```

---

## 4. Install Database (MySQL)

```bash
# Install MySQL Server
apt install -y mysql-server-8.0

# Secure MySQL installation
mysql_secure_installation

# Follow prompts:
# - Set VALIDATE PASSWORD PLUGIN? No
# - Remove anonymous users? Yes
# - Disallow root login remotely? Yes
# - Remove test database? Yes
# - Reload privilege tables now? Yes

# Login to MySQL
mysql -u root -p

# Create database and user
CREATE DATABASE ultima_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ultima_user'@'localhost' IDENTIFIED BY 'YourStrongPassword123!';
GRANT ALL PRIVILEGES ON ultima_db.* TO 'ultima_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Test the connection:
```bash
mysql -u ultima_user -p ultima_db -e "SELECT 1;"
```

---

## 5. Install Nginx

```bash
# Install Nginx
apt install -y nginx

# Start and enable Nginx
systemctl start nginx
systemctl enable nginx
```

---

## 6. Install Node.js & NPM

```bash
# Install Node.js 20.x
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# Verify
node --version   # Should show v20.x.x
npm --version    # Should show 10.x.x
```

---

## 7. Install Python & pip

```bash
# Install Python 3.10+ and pip
apt install -y python3 python3-pip python3-venv

# Verify
python3 --version   # Should show Python 3.10+
pip3 --version      # Should show pip 23.x.x
```

---

## 8. Clone Repository

```bash
# Navigate to web directory
cd /var/www

# Clone repository
git clone https://github.com/ChaisarAbi/ultima.git
cd ultima

# Create release branch if needed
git checkout main
```

---

## 9. PHP Configuration

Set PHP CLI version to 8.1:
```bash
update-alternatives --set php /usr/bin/php8.1
update-alternatives --set phpcfgconifg /usr/bin/php-config8.1
update-alternatives --set phpize /usr/bin/phpize8.1
```

Verify:
```bash
php --version  # Should show PHP 8.1.x
```

---

## 10. Install Composer Dependencies

```bash
cd /var/www/ultima

# Install composer dependencies (this may take a few minutes)
composer install --no-dev --optimize-autoloader

# If composer is not installed:
# curl -sS https://getcomposer.org/installer | php
# mv composer.phar /usr/local/bin/composer
```

---

## 11. Install NPM Dependencies & Build Assets

```bash
# Install NPM packages
npm install

# Build production assets
npm run build
```

Expected output:
```
> build
> vite build

assets/build/assets/app-CIomGrQN.js
assets/build/assets/app-l0sNRNKZ.css
```

---

## 12. Environment Setup

```bash
# Copy .env.example to .env
cp .env.example .env

# Generate application key
php artisan key:generate

# Optional: If you have an existing .env, copy it instead:
# cp /path/to/your/.env /var/www/ultima/.env
# php artisan key:generate
```

Edit `.env` file:
```bash
nano /var/www/ultima/.env
```

Configure these values:
```ini
APP_NAME="BMW ULTIMA"
APP_ENV=production
APP_KEY=base64:xxxxxxxxxxxxxxx       # Auto-generated by key:generate
APP_URL=https://ultima.aventra.my.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ultima_db
DB_USERNAME=ultima_user
DB_PASSWORD=YourStrongPassword123!

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Queue Settings
QUEUE_CONNECTION=database

# Mail Settings (configure your email)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@aventra.my.id"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 13. Database Setup

```bash
cd /var/www/ultima

# Run migrations
php artisan migrate --force

# If you want to seed with sample data (optional):
# php artisan db:seed --force

# Or use the big seeder for demo data:
# php artisan db:seed --class=BigDatabaseSeeder
```

**Storage link:**
```bash
# Create symbolic link for uploaded files
php artisan storage:link
```

---

## 14. File Permissions

```bash
# Set correct ownership
chown -R www-data:www-data /var/www/ultima

# Set correct permissions
chmod -R 755 /var/www/ultima
chmod -R 775 /var/www/ultima/storage
chmod -R 775 /var/www/ultima/bootstrap/cache
```

---

## 15. Nginx Configuration

Create Nginx config:
```bash
nano /etc/nginx/sites-available/ultima
```

Paste this configuration:
```nginx
server {
    listen 80;
    server_name ultima.aventra.my.id;
    root /var/www/ultima/public;
    index index.php index.html;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Main Laravel entry point
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM processing
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }

    # Deny access to sensitive files
    location ~* (\.env|\.git|\.svn) {
        deny all;
    }

    # Static assets caching
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff2?|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable the site:
```bash
# Enable site
ln -s /etc/nginx/sites-available/ultima /etc/nginx/sites-enabled/

# Remove default site
rm -f /etc/nginx/sites-enabled/default

# Test Nginx configuration
nginx -t

# Restart Nginx
systemctl restart nginx
systemctl enable nginx
```

---

## 16. SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
apt install -y certbot python3-certbot-nginx

# Get SSL certificate
certbot --nginx -d ultima.aventra.my.id

# Follow prompts:
# - Enter email address for renewals
# - Agree to terms of service
# - Share email? (optional)
# - Redirect HTTP to HTTPS? Yes (2)

# Auto-renewal is automatically set up by certbot
# Test renewal:
certbot renew --dry-run
```

---

## 17. Queue & Scheduled Tasks

### Queue Worker

```bash
cd /var/www/ultima

# Start queue worker as a systemd service
cat > /etc/systemd/system/ultima-queue.service << EOF
[Unit]
Description=Brem Ultima Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
WorkingDirectory=/var/www/ultima
ExecStart=/usr/bin/php /var/www/ultima/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
Restart=on-failure

[Install]
WantedBy=multi-user.target
EOF

# Start queue worker
systemctl daemon-reload
systemctl start ultima-queue
systemctl enable ultima-queue
```

### Scheduled Tasks (Cron)

```bash
# Edit crontab
crontab -e

# Add this line at the end:
* * * * * /usr/bin/php /var/www/ultima/artisan schedule:run >> /dev/null 2>&1
```

Or create a systemd timer for better reliability:
```bash
cat > /etc/systemd/system/ultima-schedule.service << EOF
[Unit]
Description=Brem Ultima Scheduled Tasks
After=network.target

[Service]
Type=oneshot
User=www-data
WorkingDirectory=/var/www/ultima
ExecStart=/usr/bin/php /var/www/ultima/artisan schedule:run
EOF

cat > /etc/systemd/system/ultima-schedule.timer << EOF
[Unit]
Description=Run Brem Ultima Schedule Every Minute

[Timer]
Unit=ultima-schedule.service
OnCalendar=*:0:0
Persistent=true

[Install]
WantedBy=timers.target
EOF

systemctl daemon-reload
systemctl enable ultima-schedule.timer
systemctl start ultima-schedule.timer
```

---

## 18. Python LSTM Model Dependencies

The application uses Python for LSTM predictions. Install dependencies:

```bash
cd /var/www/ultima/scripts

# Create virtual environment
python3 -m venv venv

# Activate virtual environment
source venv/bin/activate

# Install Python packages
pip install numpy pandas scikit-learn tensorflow flask requests

# Deactivate virtual environment
deactivate
```

**For TensorFlow on ARM servers, use pre-built binary:**
```bash
pip install --upgrade tensorflow-macos  # For Apple Silicon only
# For ARM64 Ubuntu:
pip install --ignore-installed tensorflow
```

### Create systemd service for LSTM Flask API (optional):

```bash
cat > /etc/systemd/system/ultima-lstm-api.service << EOF
[Unit]
Description=Brem Ultima LSTM Prediction API
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/ultima/scripts
ExecStart=/var/www/ultima/scripts/venv/bin/python app.py
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
EOF

systemctl daemon-reload
systemctl enable ultima-lstm-api
systemctl start ultima-lstm-api
```

---

## 19. Final Steps

```bash
# Clear all caches
cd /var/www/ultima
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verify installation
php artisan about

# Check if everything is working
curl -I https://ultima.aventra.my.id
```

**Access your application:**
- Visit: `https://ultima.aventra.my.id`
- Default admin login: Check `database/seeders/UserSeeder.php` for default credentials

---

## 20. Troubleshooting

### Issue: 502 Bad Gateway
```bash
# Check if PHP-FPM is running
systemctl status php8.1-fpm

# Check Nginx error log
tail -f /var/log/nginx/error.log

# Check PHP-FPM log
tail -f /var/log/php8.1-fpm.log
```

### Issue: Permission denied
```bash
chown -R www-data:www-data /var/www/ultima
chmod -R 755 /var/www/ultima
chmod -R 775 /var/www/ultima/storage
chmod -R 775 /var/www/ultima/bootstrap/cache
```

### Issue: Database connection failed
```bash
# Check MySQL is running
systemctl status mysql

# Test database connection
mysql -u ultima_user -p ultima_db -e "SELECT 1;"

# Check .env database settings
cat /var/www/ultima/.env | grep DB_
```

### Issue: Queue not processing
```bash
# Check queue worker status
systemctl status ultima-queue

# View queue worker logs
journalctl -u ultima-queue -f

# Manually process queue
cd /var/www/ultima
sudo -u www-data php artisan queue:work database --tries=3
```

### Issue: Assets not loading
```bash
# Rebuild assets
cd /var/www/ultima
npm run build

# Clear Laravel cache
php artisan optimize:clear
```

### Issue: SSL certificate not working
```bash
# Check certificate
certbot certificates

# Renew certificate manually if needed
certbot renew --force-renewal

# Reload Nginx after renewal
systemctl reload nginx
```

### Common Commands Reference

```bash
# Application maintenance mode
php artisan down          # Enable maintenance mode
php artisan up            # Disable maintenance mode

# Clear all caches
php artisan optimize:clear

# Rebuild caches
php artisan optimize

# Check Laravel version
php artisan --version

# List all routes
php artisan route:list

# Check app status
php artisan about

# Run migrations again (rollback and re-migrate)
php artisan migrate:fresh --seed
```

---

## Quick Deployment Checklist

- [ ] System updated (`apt update && apt upgrade`)
- [ ] PHP 8.1 + FPM installed
- [ ] MySQL 8.0 installed and configured
- [ ] Nginx installed
- [ ] Node.js 20 installed
- [ ] Python 3.10+ installed
- [ ] Repository cloned
- [ ] Composer dependencies installed
- [ ] NPM dependencies installed & assets built
- [ ] `.env` configured with correct database credentials
- [ ] `php artisan key:generate` run
- [ ] Migrations executed
- [ ] Storage link created
- [ ] File permissions set correctly
- [ ] Nginx configuration added and enabled
- [ ] SSL certificate obtained via Certbot
- [ ] Queue worker started
- [ ] Cron/scheduled tasks configured
- [ ] Python LSTM dependencies installed
- [ ] All caches cleared and optimized
- [ ] Site accessible via domain
- [ ] Login works with default credentials

---

**Support:** Untuk pertanyaan atau masalah deployment, hubungi tim developer.