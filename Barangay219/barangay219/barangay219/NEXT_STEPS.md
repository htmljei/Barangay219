# Next Steps - Getting Your Login Working

Follow these steps in order to get your login system working:

## Step 1: Start XAMPP Services ✅

1. Open **XAMPP Control Panel**
2. Click **Start** for **Apache**
3. Click **Start** for **MySQL**
4. Both should show green "Running" status

## Step 2: Test Database Connection 🔍

1. Open your browser
2. Go to: `http://localhost/e-barangay-system/e-barangay-system/test-db.php`
3. This will show you:
   - ✓ Database connection status
   - ✓ Which tables exist
   - ✓ If admin user exists
   - ✓ If password hash is correct

**What to look for:**
- If you see "Database connection FAILED" → Go to Step 3
- If you see "Admin user NOT FOUND" → Go to Step 4
- If you see "Password DOES NOT verify" → Go to Step 5
- If everything shows ✓ → Go to Step 6

## Step 3: Create Database (if needed) 🗄️

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click **"New"** in the left sidebar
3. Database name: `ebarangay_db`
4. Collation: `utf8mb4_unicode_ci`
5. Click **"Create"**

## Step 4: Import Database Schema 📥

1. In phpMyAdmin, select `ebarangay_db` database
2. Click **"Import"** tab
3. Click **"Choose File"**
4. Select: `e-barangay-system/database/schema.sql`
5. Click **"Go"** at the bottom
6. Wait for "Import has been successfully finished" message

## Step 5: Import Seed Data (Admin User) 👤

1. Still in phpMyAdmin with `ebarangay_db` selected
2. Click **"Import"** tab again
3. Click **"Choose File"**
4. Select: `e-barangay-system/database/seeds.sql`
5. Click **"Go"**
6. This creates the admin user with password `admin123`

## Step 6: Fix Password Hash (if login still fails) 🔐

If login doesn't work after importing seeds:

1. Open: `http://localhost/e-barangay-system/e-barangay-system/fix-password.php`
2. Copy the SQL statement shown
3. Go to phpMyAdmin → `ebarangay_db` → **SQL** tab
4. Paste the SQL and click **"Go"**

## Step 7: Test Login 🚀

1. Open: `http://localhost/e-barangay-system/e-barangay-system/public/index.php`
2. Enter credentials:
   - **Username:** `admin`
   - **Password:** `admin123`
3. Click **Login**

**Expected result:**
- You should be redirected to the dashboard
- If you see an error, check browser console (F12) for JavaScript errors

## Step 8: Verify Everything Works ✅

After successful login, you should see:
- Dashboard page loads
- No error messages
- You can navigate the system

## Troubleshooting

### "Database connection failed"
- Check MySQL is running in XAMPP
- Verify database name is `ebarangay_db`
- Check `config/constants.php` for correct credentials

### "Invalid username or password"
- Run `test-db.php` to check admin user exists
- Run `fix-password.php` to regenerate password hash
- Update password in database using the generated SQL

### "404 Not Found" or page doesn't load
- Check Apache is running
- Verify file paths are correct
- Check URL: `http://localhost/e-barangay-system/e-barangay-system/public/index.php`

### JavaScript errors in browser console
- Open browser Developer Tools (F12)
- Check Console tab for errors
- Verify `auth.js` file is loading correctly

## Quick Test URLs

- **Login Page:** `http://localhost/e-barangay-system/e-barangay-system/public/index.php`
- **Database Test:** `http://localhost/e-barangay-system/e-barangay-system/test-db.php`
- **Password Generator:** `http://localhost/e-barangay-system/e-barangay-system/fix-password.php`

---

**Start with Step 1 and work through each step. If you get stuck at any step, let me know!**
