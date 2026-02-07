# Debug 500 Server Error - Step by Step

## 🚨 Immediate Steps to Find the Error

### Step 1: Check Vercel Function Logs (MOST IMPORTANT!)

1. **Go to Vercel Dashboard**
   - https://vercel.com/dashboard
   - Click your project: **89-catalog**

2. **Check Function Logs**
   - Click **"Functions"** tab
   - Click on **`api/index.php`**
   - Click **"Logs"** tab
   - **Look for red errors** - this will tell you EXACTLY what's wrong!

3. **Check Deployment Logs**
   - Go to **"Deployments"** tab
   - Click on latest deployment
   - Check **"Function Logs"** or **"Runtime Logs"**

**The logs will show you the exact error!** Common errors you might see:

---

## 🔍 Common 500 Error Causes

### 1. Missing APP_KEY (Most Common!)

**Error in logs:** `No application encryption key has been specified`

**Fix:**
- Go to Vercel → Settings → Environment Variables
- Verify `APP_KEY` is set
- Value should be: `base64:f/NjDcs1bGoj2CBfqIrrtngJT6qvJZiTGTZ6LPOb0Wg=`
- Make sure it's set for **Production** environment
- Redeploy

---

### 2. Database Connection Failed

**Error in logs:** `SQLSTATE[HY000] [2002]` or `Connection refused`

**Fix:**
- Verify all DB variables are set:
  - `DB_HOST=switchyard.proxy.rlwy.net`
  - `DB_PORT=17113`
  - `DB_DATABASE=railway`
  - `DB_USERNAME=root`
  - `DB_PASSWORD=CeIRPTFsoOWmMLkYHiaDlrJytxIknjGZ`
- Test connection:
  ```bash
  mysql -h switchyard.proxy.rlwy.net -P 17113 -u root -p railway
  # Password: CeIRPTFsoOWmMLkYHiaDlrJytxIknjGZ
  ```

---

### 3. Missing Environment Variables

**Error in logs:** `Undefined array key` or `config() returned null`

**Fix:**
- Check if ALL required variables are set
- Especially: `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_URL`
- Verify they're set for **Production** environment

---

### 4. Composer Dependencies Not Installed

**Error in logs:** `Class '...' not found` or `require(): Failed opening required`

**Fix:**
- The `vercel-php` runtime should install composer automatically
- Check build logs to see if composer install ran
- Verify `composer.json` and `composer.lock` are in the repo

---

### 5. File Permissions or Path Issues

**Error in logs:** `Permission denied` or `No such file or directory`

**Fix:**
- Ensure cache paths use `/tmp`:
  - `VIEW_COMPILED_PATH=/tmp`
  - `APP_CONFIG_CACHE=/tmp/config.php`
- Vercel uses read-only filesystem except `/tmp`

---

## 🔧 Quick Fix: Enable Debug Mode

**⚠️ TEMPORARY - Disable after debugging!**

1. Go to Vercel → Settings → Environment Variables
2. Find `APP_DEBUG`
3. Change value to: `true`
4. Save
5. Redeploy
6. Visit your site - you'll see detailed error messages
7. **After fixing, set `APP_DEBUG=false` again!**

---

## 🧪 Test Health Endpoint

Try accessing:
```
https://your-domain.vercel.app/up
```

- ✅ If it works: Laravel is running, but a route has an error
- ❌ If 500: Laravel bootstrap is failing (check logs)

---

## 📋 Environment Variables Checklist

Verify these are ALL set in Vercel (Production environment):

### Critical (Required):
- [ ] `APP_KEY` ← **MOST IMPORTANT!**
- [ ] `APP_NAME`
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` (your Vercel domain)

### Database:
- [ ] `DB_CONNECTION=mysql`
- [ ] `DB_HOST=switchyard.proxy.rlwy.net`
- [ ] `DB_PORT=17113`
- [ ] `DB_DATABASE=railway`
- [ ] `DB_USERNAME=root`
- [ ] `DB_PASSWORD=CeIRPTFsoOWmMLkYHiaDlrJytxIknjGZ`

### Vercel-Specific:
- [ ] `VIEW_COMPILED_PATH=/tmp`
- [ ] `CACHE_DRIVER=array`
- [ ] `SESSION_DRIVER=cookie`
- [ ] `LOG_CHANNEL=stderr`

---

## 🎯 Most Likely Issues (Check These First!)

### Issue #1: APP_KEY Not Set
**Symptom:** 500 error immediately
**Fix:** Add `APP_KEY` to Vercel environment variables

### Issue #2: Wrong Environment Scope
**Symptom:** Variables exist but not applied
**Fix:** Make sure variables are set for **Production** environment

### Issue #3: Database Connection
**Symptom:** Error after page loads
**Fix:** Verify Railway database is accessible and credentials are correct

### Issue #4: Missing Variables
**Symptom:** Config errors
**Fix:** Add all variables from `.env.vercel` file

---

## 📞 What to Do Right Now

1. **Check Function Logs** ← Do this FIRST!
   - Vercel Dashboard → Functions → api/index.php → Logs
   - Copy the error message

2. **Enable Debug Mode Temporarily**
   - Set `APP_DEBUG=true`
   - Redeploy
   - See detailed error

3. **Verify APP_KEY is Set**
   - Most common cause of 500 errors
   - Check Vercel → Settings → Environment Variables

4. **Check Database Connection**
   - Verify all DB_* variables are correct
   - Test connection from your local machine

---

## 💡 Pro Tip

**The function logs will tell you exactly what's wrong!**

Go to:
- Vercel Dashboard
- Your Project
- Functions tab
- api/index.php
- Logs tab

Look for:
- Red error messages
- Stack traces
- PHP fatal errors
- Laravel exceptions

**Share the error message from the logs, and I can help you fix it specifically!**

---

## 🆘 Still Stuck?

1. **Share the error from Function Logs**
2. **Share which variables you've added**
3. **Check if Railway database is running**
4. **Verify you redeployed after adding variables**

The logs are your best friend - they'll tell you exactly what's failing!
