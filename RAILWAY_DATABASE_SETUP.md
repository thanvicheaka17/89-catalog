# Railway Database Setup for Vercel

## ⚠️ Important: Internal vs External Connection

The connection string you provided uses `mysql.railway.internal` which is **only accessible within Railway's network**. For Vercel, you need the **public/external** connection string.

## 🔍 How to Get Railway Public Connection String

### Step-by-Step Guide:

### Step 1: Go to Railway Dashboard
1. Visit: https://railway.app/dashboard
2. Login to your Railway account
3. You should see your projects/services listed

### Step 2: Find Your MySQL Service
1. Click on the project that contains your MySQL database
2. Look for a service named something like:
   - "MySQL"
   - "Database"
   - "PostgreSQL" (if you're using PostgreSQL)
   - Or the service you created for your database

### Step 3: Open the Service
1. Click on your MySQL/database service
2. You'll see several tabs: **Deployments**, **Metrics**, **Settings**, **Variables**, **Connect**, etc.

### Step 4: Check the "Connect" Tab
1. Click on the **"Connect"** tab
2. You'll see connection strings and connection details
3. Look for **"Public Networking"** or **"Public URL"** section

### Step 5: Find the Public Hostname
You're looking for a hostname that:
- ✅ Does NOT contain `.railway.internal`
- ✅ Contains `.railway.app` or `.up.railway.app`
- ✅ Looks like one of these formats:
  ```
  containers-us-west-xxx.railway.app
  mysql-production.up.railway.app
  mysql.railway.app
  [your-service-name].up.railway.app
  ```

### Step 6: Alternative - Check "Variables" Tab
1. Click on the **"Variables"** tab
2. Look for variables like:
   - `MYSQLHOST` or `MYSQL_HOST`
   - `PUBLIC_URL`
   - `DATABASE_URL` (check if it has a public hostname)

### Step 7: Check Service Settings
1. Click on **"Settings"** tab
2. Look for **"Networking"** or **"Public Networking"** section
3. Enable **"Public Networking"** if it's disabled
4. This will generate a public URL/hostname for your database

### Step 8: If Public Networking is Not Enabled
1. Go to **Settings** → **Networking**
2. Toggle **"Public Networking"** to **ON**
3. Railway will generate a public URL
4. Copy this public hostname

### What You're Looking For:
The connection details should show:
- **Public Host:** `containers-us-west-xxx.railway.app` (or similar - THIS is what you need!)
- **Port:** `3306` (or the port shown)
- **Database:** `railway` (or your database name)
- **Username:** `root` (or your username)
- **Password:** `CeIRPTFsoOWmMLkYHiaDlrJytxIknjGZ` (your password)

### Visual Guide:
```
Railway Dashboard
  └── Your Project
      └── MySQL Service
          ├── Connect Tab ← Check here first!
          │   ├── Public Networking: ON
          │   └── Public URL: containers-us-west-xxx.railway.app ← Copy this!
          │
          ├── Variables Tab ← Or check here
          │   └── MYSQLHOST or DATABASE_URL
          │
          └── Settings Tab ← Enable public networking here if needed
              └── Networking → Public Networking: ON
```

---

## 📝 Environment Variables for Vercel

Based on your Railway connection string, here are the exact values to add to Vercel:

### Via Vercel Dashboard:

1. Go to: https://vercel.com/dashboard → Your Project → Settings → Environment Variables

2. Add these variables:

```
DB_CONNECTION = mysql
DB_HOST = containers-us-west-xxx.railway.app  ⚠️ Use your PUBLIC hostname from Railway
DB_PORT = 3306
DB_DATABASE = railway
DB_USERNAME = root
DB_PASSWORD = CeIRPTFsoOWmMLkYHiaDlrJytxIknjGZ
```

**⚠️ CRITICAL:** Replace `containers-us-west-xxx.railway.app` with your actual **public** Railway hostname!

### Via Vercel CLI:

```bash
vercel env add DB_CONNECTION production
# Enter: mysql

vercel env add DB_HOST production
# Enter: containers-us-west-xxx.railway.app (your public Railway host)

vercel env add DB_PORT production
# Enter: 3306

vercel env add DB_DATABASE production
# Enter: railway

vercel env add DB_USERNAME production
# Enter: root

vercel env add DB_PASSWORD production
# Enter: CeIRPTFsoOWmMLkYHiaDlrJytxIknjGZ
```

---

## 🔐 Complete Vercel Environment Variables Setup

In addition to database variables, make sure you have these:

### Required Laravel Variables:
```
APP_NAME = CLICKENGINE
APP_ENV = production
APP_KEY = base64:f/NjDcs1bGoj2CBfqIrrtngJT6qvJZiTGTZ6LPOb0Wg=
APP_DEBUG = false
APP_URL = https://your-project.vercel.app
```

### Required Vercel-Specific:
```
VIEW_COMPILED_PATH = /tmp
CACHE_DRIVER = array
SESSION_DRIVER = cookie
LOG_CHANNEL = stderr
```

### Other Required:
```
JWT_SECRET = 1M0pDl0Rpu8jSnliHUCyOy56Sg4VwvriCFI2rWFBXbujsXcrh94o43OuyhpWynA8
QUEUE_CONNECTION = sync
FILESYSTEM_DISK = s3
```

---

## 🧪 Testing the Connection

### Option 1: Test from Local Machine

```bash
# Test if you can connect to Railway database from your local machine
mysql -h containers-us-west-xxx.railway.app -P 3306 -u root -p railway
# Enter password when prompted: CeIRPTFsoOWmMLkYHiaDlrJytxIknjGZ
```

If this works, the connection will work from Vercel too.

### Option 2: Test via Laravel

Create a temporary test route in `routes/web.php`:

```php
Route::get('/test-db', function() {
    try {
        $pdo = DB::connection()->getPdo();
        $database = DB::connection()->getDatabaseName();
        return response()->json([
            'status' => 'success',
            'message' => 'Database connection successful!',
            'database' => $database,
            'host' => config('database.connections.mysql.host'),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Database connection failed',
            'error' => $e->getMessage(),
        ], 500);
    }
});
```

**⚠️ Remove this route after testing!**

---

## 🚨 Common Issues

### Issue: "Connection refused" or "Host not found"

**Cause:** Using internal Railway hostname (`mysql.railway.internal`)

**Solution:**
- ✅ Use the **public** Railway hostname (usually ends with `.railway.app`)
- ✅ Check Railway dashboard for "Public Networking" or "Public URL"

### Issue: "Access denied for user"

**Cause:** Wrong credentials or user doesn't have remote access

**Solution:**
- ✅ Verify username and password in Railway dashboard
- ✅ Check if Railway allows external connections (should be enabled by default)

### Issue: "Can't connect to MySQL server"

**Cause:** Firewall or network restrictions

**Solution:**
- ✅ Railway databases should allow external connections by default
- ✅ Check Railway service settings for network/security settings
- ✅ Verify the port (usually 3306)

---

## 📋 Quick Checklist

- [ ] Found public Railway hostname (not `.railway.internal`)
- [ ] Added `DB_HOST` with public hostname to Vercel
- [ ] Added `DB_PORT` (usually 3306)
- [ ] Added `DB_DATABASE` (railway)
- [ ] Added `DB_USERNAME` (root)
- [ ] Added `DB_PASSWORD` (your password)
- [ ] Set environment scope (Production, Preview, Development)
- [ ] Added all other required Laravel variables
- [ ] Redeployed application
- [ ] Tested database connection

---

## 🔗 Railway Resources

- [Railway MySQL Documentation](https://docs.railway.app/databases/mysql)
- [Railway Connection Strings](https://docs.railway.app/databases/connect)
- [Railway Dashboard](https://railway.app/dashboard)

---

## 💡 Pro Tips

1. **Use Railway's Public URL**
   - Railway provides both internal and public connection strings
   - Always use the public one for external services like Vercel

2. **Check Railway Service Settings**
   - Go to your MySQL service → Settings
   - Look for "Public Networking" or "Public URL"
   - Enable if not already enabled

3. **Test Connection First**
   - Test the connection from your local machine before deploying to Vercel
   - This helps identify issues early

4. **Use Different Databases for Environments**
   - Consider using separate Railway databases for Production and Preview
   - This prevents data conflicts during testing

5. **Monitor Railway Usage**
   - Railway free tier has limits
   - Monitor your usage in the Railway dashboard

---

## 🆘 Still Having Issues?

1. **Check Railway Logs**
   - Railway Dashboard → Your MySQL Service → Logs
   - Look for connection errors

2. **Verify Railway Service Status**
   - Make sure your MySQL service is running
   - Check Railway status page

3. **Contact Railway Support**
   - Railway has helpful documentation and support
   - Check their Discord or support channels

4. **Test with Railway CLI**
   ```bash
   railway connect mysql
   ```
