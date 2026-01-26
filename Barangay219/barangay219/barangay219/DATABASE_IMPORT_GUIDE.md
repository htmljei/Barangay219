# Database Import Guide for Barangay 219

## Quick Import (Recommended)

Use the **complete import file** that includes everything:

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click **"Import"** tab
3. Choose file: `database/import-all.sql`
4. Click **"Go"**

This will:
- ✅ Create the database `barangay219_db`
- ✅ Create all tables
- ✅ Insert admin user (username: `admin`, password: `admin123`)
- ✅ Insert sample data for testing

## Step-by-Step Import

If you prefer to import separately:

### Step 1: Create Database
```sql
CREATE DATABASE barangay219_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 2: Import Schema
1. In phpMyAdmin, select `barangay219_db` database
2. Click **"Import"** tab
3. Choose file: `database/schema.sql`
4. Click **"Go"**

### Step 3: Import Seed Data
1. Still in phpMyAdmin with `barangay219_db` selected
2. Click **"Import"** tab again
3. Choose file: `database/seeds.sql`
4. Click **"Go"**

## Database Configuration

The database configuration is in `config/constants.php`:
- **Host:** `localhost`
- **Database:** `barangay219_db`
- **User:** `root`
- **Password:** `` (empty - default XAMPP)

If your MySQL has a password, update `DB_PASS` in `config/constants.php`.

## Default Login Credentials

After importing:
- **Username:** `admin`
- **Password:** `admin123`

⚠️ **Important:** Change the password immediately after first login!

## Troubleshooting

### Database Connection Failed
1. Make sure MySQL is running in XAMPP
2. Check database name matches: `barangay219_db`
3. Verify credentials in `config/constants.php`

### Login Fails
1. Run the test script: `http://localhost/Barangay219/barangay219/barangay219/test-db.php`
2. Check if admin user exists in database
3. Regenerate password hash using `fix-password.php`
4. Update the password in database using the generated SQL

### Import Errors
- Make sure you're using MySQL 5.7+ or MariaDB 10.2+
- Check that foreign key constraints are enabled
- Ensure the database doesn't already exist with conflicting data

## Testing Database Connection

Access: `http://localhost/Barangay219/barangay219/barangay219/test-db.php`

This will show:
- ✓ Database connection status
- ✓ Which tables exist
- ✓ If admin user exists
- ✓ If password hash is correct
