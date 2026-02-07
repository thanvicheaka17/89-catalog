# Fix: HTTP 500 Error on Vercel

## Error
```
HTTP ERROR 500
This page isn't working
89-catalog23424s.vercel.app is currently unable to handle this request.
```

## What This Means
The deployment succeeded, but there's a **runtime error** when the PHP function executes. This is different from build errors.

## 🔍 Step 1: Check Vercel Function Logs

### Get Detailed Error Information:

1. **Go to Vercel Dashboard**
   - Visit: https://vercel.com/dashboard
   - Click your project: **89-catalog**

2. **Check Function Logs**
   - Click **"Functions"** tab
   - Click on **`api/index.php`**
   - Click **"Logs"** tab
   - Look for PHP errors, Laravel exceptions, or stack traces

3. **Check Deployment Logs**
   - Go to **"Deployments"** tab
   - Click on the latest deployment
   - Check **"Function Logs"** or **"Runtime Logs"**

## 🚨 Common Causes & Solutions

### 1. Missing APP_KEY (Most Common)

**Symptom:** Laravel can't encrypt/decrypt data

**Solution:**
1. Go to Vercel Dashboard → Settings → Environment Variables
2. Add/Verify `APP_KEY` is set
3. Generate a new key if needed:
   ```bash
   php artisan key:generate --show
   ```
4. Copy the output and set it as `APP_KEY` in Vercel
5. Redeploy

### 2. Database Connection Failed

**Symptom:** Database errors in logs

**Solution:**
1. Verify all `DB_*` variables are set:
   - `DB_CONNECTION=mysql`
   - `DB_HOST` (use PUBLIC Railway hostname, not `.railway.internal`)
   - `DB_PORT=3306`
   - `DB_DATABASE`
   - `DB_USERNAME`
   - `DB_PASSWORD`

2. Test database connection:
   ```bash
   mysql -h your-public-host.railway.app -P 3306 -u root -p
   ```

3. Ensure Railway database has public networking enabled

### 3. Missing Environment Variables

**Symptom:** Config errors or undefined variables

**Solution:**
Add all required variables to Vercel:
```
APP_NAME=CLICKENGINE
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://89-catalog23424s.vercel.app

VIEW_COMPILED_PATH=/tmp
CACHE_DRIVER=array
SESSION_DRIVER=cookie
LOG_CHANNEL=stderr

DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

JWT_SECRET=...
```

### 4. Composer Dependencies Not Installed

**Symptom:** "Class not found" or "File not found" errors

**Solution:**
- The `vercel-php` runtime should install composer automatically
- Verify `composer.json` and `composer.lock` are in the repo
- Check build logs to see if composer install ran

### 5. File Permissions or Path Issues

**Symptom:** "Permission denied" or "File not found"

**Solution:**
- Vercel uses read-only filesystem except `/tmp`
- Ensure cache paths use `/tmp`:
  ```
  VIEW_COMPILED_PATH=/tmp
  APP_CONFIG_CACHE=/tmp/config.php
  ```

### 6. Laravel Bootstrap Errors

**Symptom:** Errors during Laravel initialization

**Solution:**
- Check if `bootstrap/cache` directory exists
- Verify `storage` directories are writable (though Vercel is read-only)
- Check Laravel logs in function logs

## 🔧 Step 2: Enable Debug Mode Temporarily

**⚠️ Only for debugging - disable after!**

1. Go to Vercel → Settings → Environment Variables
2. Set `APP_DEBUG=true`
3. Redeploy
4. Check the error page - it will show detailed error messages
5. **IMPORTANT:** Set `APP_DEBUG=false` after debugging!

## 🧪 Step 3: Test Health Endpoint

Try accessing:
```
https://89-catalog23424s.vercel.app/up
```

This is Laravel's health check. If it works, Laravel is running but routes might have issues.

## 📋 Step 4: Create Debug Route (Temporary)

Add to `routes/web.php`:

```php
Route::get('/debug', function() {
    return response()->json([
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'env' => config('app.env'),
        'debug' => config('app.debug'),
        'db_connected' => function_exists('DB') ? 'yes' : 'no',
        'app_key_set' => !empty(config('app.key')),
    ]);
});
```

Then access: `https://89-catalog23424s.vercel.app/debug`

**⚠️ Remove this route after debugging!**

## 🔍 Step 5: Check Specific Error Types

### If you see "Class not found":
- Composer dependencies not installed
- Check function logs for composer errors

### If you see "Database connection failed":
- Check `DB_*` environment variables
- Verify Railway database is accessible

### If you see "APP_KEY not set":
- Add `APP_KEY` to environment variables

### If you see "Permission denied":
- Cache/storage paths issue
- Ensure using `/tmp` for cache

## 📝 Complete Environment Variables Checklist

Ensure these are ALL set in Vercel:

### Required:
- [ ] `APP_NAME`
- [ ] `APP_ENV=production`
- [ ] `APP_KEY` ← **CRITICAL!**
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL=https://89-catalog23424s.vercel.app`

### Database:
- [ ] `DB_CONNECTION=mysql`
- [ ] `DB_HOST` (public Railway hostname)
- [ ] `DB_PORT=3306`
- [ ] `DB_DATABASE`
- [ ] `DB_USERNAME`
- [ ] `DB_PASSWORD`

### Vercel-Specific:
- [ ] `VIEW_COMPILED_PATH=/tmp`
- [ ] `CACHE_DRIVER=array`
- [ ] `SESSION_DRIVER=cookie`
- [ ] `LOG_CHANNEL=stderr`

### Other:
- [ ] `JWT_SECRET`
- [ ] `QUEUE_CONNECTION=sync`
- [ ] Any other variables your app needs

## 🚀 Quick Fix Workflow

1. **Check Function Logs** → Identify the error
2. **Enable APP_DEBUG=true** → See detailed error
3. **Fix the issue** → Usually missing env var or DB connection
4. **Redeploy**
5. **Disable APP_DEBUG=false** → After fixing

## 💡 Pro Tips

1. **Always check logs first** - They tell you exactly what's wrong
2. **Test health endpoint** - `/up` tells you if Laravel is running
3. **Enable debug temporarily** - See full error messages
4. **Verify all env vars** - Missing vars cause 500 errors
5. **Check database connection** - Most common issue

## 🆘 Still Not Working?

1. Check Vercel Status: https://vercel-status.com
2. Review Vercel PHP Runtime docs
3. Check Laravel logs in function logs
4. Test database connection separately
5. Verify all environment variables are set

---

## Most Likely Fix

Based on common issues, **check these first:**

1. ✅ **APP_KEY is set** in Vercel environment variables
2. ✅ **Database variables** are correct (especially `DB_HOST` using public Railway hostname)
3. ✅ **APP_URL** matches your Vercel domain
4. ✅ Check **Function Logs** for specific error messages

The logs will tell you exactly what's wrong!
