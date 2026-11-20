# Smart Gram — Local Development Environment Setup Guide
**Audience:** Beginner Developers  
**Platform:** Windows 10/11  
**Prerequisites:** XAMPP (Apache + MySQL + PHP 8+)

---

## 1. Prerequisites Overview

Smart Gram uses:
- **PHP 8.1+** (The scripting language that powers the backend)
- **MySQL 8.0+** (The database where complaints and master data are stored)
- **Apache** (The web server that serves web pages to your browser)

The easiest way to get all three running simultaneously on Windows is using **XAMPP**.

---

## 2. Setting Up XAMPP

You already have the XAMPP installer downloaded on your computer:
`C:\Users\<YourUser>\Downloads\xampp-windows-x64-8.2.12-0-VS16-installer.exe`

### Step 2.1: Run the Installer
1. Double-click the installer executable in your `Downloads` folder.
2. If Windows displays a UAC (User Account Control) prompt or an antivirus warning, click **Yes** to continue.
3. In the setup wizard:
   - Choose components: Ensure **Apache**, **MySQL**, **PHP**, and **phpMyAdmin** are checked.
   - Installation folder: Keep the standard path: `C:\xampp`.
4. Click **Next** and complete the installation.

### Step 2.2: Launch Services in XAMPP Control Panel
1. Open the **XAMPP Control Panel** from your Windows Start Menu.
2. Click the **Start** button next to **Apache**. (The background will turn green with port 80/443).
3. Click the **Start** button next to **MySQL**. (The background will turn green with port 3306).

---

## 3. Adding PHP & MySQL to your Windows PATH (Recommended)

Adding XAMPP to your Windows PATH allows you to run `php` and `mysql` commands directly from any terminal or IDE.

### Quick PowerShell Method (Run as Administrator):
Open PowerShell as Administrator and run:
```powershell
[Environment]::SetEnvironmentVariable(
    "Path",
    $env:Path + ";C:\xampp\php;C:\xampp\mysql\bin",
    [EnvironmentVariableTarget]::User
)
```

### Verification:
Open a new terminal window and verify:
```bash
php -v
mysql --version
```
You should see PHP 8.2.x and MySQL/MariaDB version information displayed.

---

## 4. Configuring Project Environment (.env)

1. In the project root (`Smart Gram/`), duplicate `.env.example` and rename the copy to `.env`.
2. Ensure the database connection settings match your local XAMPP configuration:
   ```env
   DB_HOST="127.0.0.1"
   DB_PORT="3306"
   DB_NAME="smart_gram_db"
   DB_USER="root"
   DB_PASS=""
   ```
*(In standard XAMPP, the default MySQL user is `root` with an empty password `""`)*.

---

## 5. Creating the Database in phpMyAdmin

1. Open your browser and navigate to: `http://localhost/phpmyadmin`
2. Click **New** in the left sidebar.
3. Enter database name: `smart_gram_db`.
4. Select collation: `utf8mb4_unicode_ci` (Crucial for storing Telugu text correctly).
5. Click **Create**.

---

## 6. Common Troubleshooting & FAQs

### Error 1: "Port 80 in use by another application"
- **Cause:** Skype, IIS, or VMware may be using port 80.
- **Fix:** In XAMPP Control Panel, click **Config** next to Apache -> `httpd.conf`. Search for `Listen 80` and change to `Listen 8080`. Your local URL will then be `http://localhost:8080`.

### Error 2: "Port 3306 in use by another MySQL instance"
- **Cause:** Another standalone MySQL service is running.
- **Fix:** Open Windows Services (`services.msc`), find the existing `MySQL` service, right-click, and select **Stop**. Then start MySQL in XAMPP.

### Error 3: Telugu characters appear as `???` or garbled boxes
- **Cause:** Database or table collation was set to `latin1` instead of `utf8mb4`.
- **Fix:** Always ensure database and tables use `utf8mb4` with `utf8mb4_unicode_ci` collation.
