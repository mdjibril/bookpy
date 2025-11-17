# Deployment Guide for bookpy

This guide provides step-by-step instructions for deploying the `bookpy` PHP application to a production server.

---

## 1. Server Requirements

Ensure your server meets the following requirements:

- **PHP**: Version 8.1 or higher.
- **PHP Extensions**: `pdo_mysql`, `mbstring`.
- **Web Server**: Apache or Nginx with URL rewriting enabled.
- **Database**: MySQL or MariaDB.
- **Composer**: For managing PHP dependencies.
- **Git**: For cloning the repository.

---

## 2. Deployment on a VPS or Dedicated Server

This method is recommended if you have root access to your server and can configure Apache or Nginx directly.

### Step 1: Clone the Repository

Connect to your server via SSH and clone the project repository into your desired directory (e.g., `/var/www/bookpy`).

```bash
git clone https://github.com/mdjibril/bookpy.git /var/www/bookpy.your-domain.com
cd /var/www/bookpy.your-domain.com
```

### Step 2: Install Dependencies

Install the required PHP dependencies using Composer.

```bash
composer install --no-dev --optimize-autoloader
```

### Step 3: Configure Environment Variables

Copy the example environment file to create your production configuration file.

```bash
cp .env.example .env
```

Now, edit the `.env` file with your production values (database credentials, Resend API key, etc.).

```bash
nano .env
```

**Key variables to set:**
- `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
- `APP_URL` (Your public domain, e.g., `https://book.example.com`)
- `ADMIN_PASSWORD` (Choose a strong, secure password)
- `ADMIN_EMAIL`
- `MAIL_MODE` (Set to `resend` for production)
- `MAIL_FROM` (Must be a verified domain in your Resend account)
- `RESEND_API_KEY`

### Step 4: Set Up the Database

Log in to your MySQL server and create the database and user specified in your `.env` file.

```sql
CREATE DATABASE bookpy;
CREATE USER 'bookpy_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON bookpy.* TO 'bookpy_user'@'localhost';
FLUSH PRIVILEGES;
```

### Step 5: Run Database Migrations

Run the SQL migration files to create the necessary tables. You should run these in order.

```bash
mysql -u bookpy_user -p bookpy < migrations/0001_create_bookings_table.sql
mysql -u bookpy_user -p bookpy < migrations/0002_create_email_templates_table.sql
mysql -u bookpy_user -p bookpy < migrations/0003_create_email_template_versions_table.sql
mysql -u bookpy_user -p bookpy < migrations/0004_add_cancellation_token_to_bookings.sql
```

### Step 6: Configure the Web Server

Your web server's "document root" must be set to the `public` directory. This is critical for security, as it prevents direct web access to your application logic, `.env` file, and other sensitive files.

#### Example for Apache

Create a virtual host configuration file (e.g., `/etc/apache2/sites-available/bookpy.conf`) with the following content:

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/bookpy/public

    <Directory /var/www/bookpy/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

Enable the site and the `rewrite` module:
```bash
sudo a2ensite bookpy.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Example for Nginx

Create a server block configuration file (e.g., `/etc/nginx/sites-available/bookpy`) with the following content:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/bookpy/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock; # Adjust PHP version if needed
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Enable the site by creating a symbolic link:
```bash
sudo ln -s /etc/nginx/sites-available/bookpy /etc/nginx/sites-enabled/
sudo systemctl restart nginx
```

---

Your application should now be live!