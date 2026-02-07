# Fix Mixed Content Errors (HTTP vs HTTPS)

## Problem
Your site is served over HTTPS, but assets (CSS, JS, images) are being requested over HTTP, causing mixed content errors.

## Root Cause
- `APP_URL` might be set to `http://` instead of `https://` in Vercel
- Laravel's `asset()` helper generates URLs based on `APP_URL`
- Vite build might have been done with wrong base URL

## 🔧 Solution: Update Environment Variables in Vercel

### Step 1: Update APP_URL

1. Go to Vercel Dashboard → Your Project → Settings → Environment Variables
2. Find `APP_URL`
3. **Change it to:** `https://89-catalog-ofi9.vercel.app`
   - ⚠️ Use your ACTUAL Vercel domain (not the old one)
   - ⚠️ Must start with `https://` (not `http://`)
4. Make sure it's set for **Production** environment
5. Click **Save**

### Step 2: Update VITE_BASE_URL

1. Find `VITE_BASE_URL` in Environment Variables
2. **Change it to:** `https://89-catalog-ofi9.vercel.app`
   - ⚠️ Use your ACTUAL Vercel domain
   - ⚠️ Must start with `https://`
3. Make sure it's set for **Production** environment
4. Click **Save**

### Step 3: Update FRONTEND_URL

1. Find `FRONTEND_URL` in Environment Variables
2. **Change it to:** `https://89-catalog-ofi9.vercel.app`
3. Make sure it's set for **Production** environment
4. Click **Save**

### Step 4: Update VITE_REVERB_HOST

1. Find `VITE_REVERB_HOST` in Environment Variables
2. **Change it to:** `89-catalog-ofi9.vercel.app`
   - (No `https://`, just the domain)
3. Make sure it's set for **Production** environment
4. Click **Save**

### Step 5: Redeploy

After updating all URLs:
1. Go to **Deployments** tab
2. Click **"Redeploy"** on the latest deployment
3. This will rebuild with the correct HTTPS URLs

---

## 📋 Complete List of URLs to Update

Update these in Vercel Environment Variables:

```
APP_URL=https://89-catalog-ofi9.vercel.app
VITE_BASE_URL=https://89-catalog-ofi9.vercel.app
FRONTEND_URL=https://89-catalog-ofi9.vercel.app
VITE_REVERB_HOST=89-catalog-ofi9.vercel.app
```

**Important:** Replace `89-catalog-ofi9.vercel.app` with your actual Vercel domain!

---

## 🔍 Verify Current Values

Check what's currently set in Vercel:

1. Go to Settings → Environment Variables
2. Look for:
   - `APP_URL` - Should be `https://89-catalog-ofi9.vercel.app`
   - `VITE_BASE_URL` - Should be `https://89-catalog-ofi9.vercel.app`
   - `FRONTEND_URL` - Should be `https://89-catalog-ofi9.vercel.app`

If any of these are:
- ❌ `http://` (should be `https://`)
- ❌ Wrong domain (should match your Vercel domain)
- ❌ Missing

Then update them!

---

## 🚀 After Redeploying

1. **Clear Browser Cache**
   - Hard refresh: `Ctrl+Shift+R` (Windows/Linux) or `Cmd+Shift+R` (Mac)
   - Or clear browser cache completely

2. **Check Browser Console**
   - Open Developer Tools (F12)
   - Check Console tab
   - Should see no more mixed content errors

3. **Verify Assets Load**
   - CSS should load
   - JavaScript should load
   - Images should load

---

## 💡 Why This Happens

Laravel's `asset()` helper generates URLs like this:
```php
asset('images/logo.png')
// Returns: http://domain.com/images/logo.png (if APP_URL is http://)
// Returns: https://domain.com/images/logo.png (if APP_URL is https://)
```

Vite also uses `VITE_BASE_URL` to generate asset URLs during build.

---

## 🔧 Alternative: Force HTTPS in Code

If updating environment variables doesn't work, you can force HTTPS in Laravel:

### Option 1: Update TrustProxies Middleware

Check `app/Http/Middleware/TrustProxies.php` - it should trust Vercel proxies.

### Option 2: Force HTTPS in AppServiceProvider

Add to `app/Providers/AppServiceProvider.php`:

```php
public function boot(): void
{
    if (config('app.env') === 'production') {
        \URL::forceScheme('https');
    }
}
```

But updating environment variables is the better solution!

---

## ✅ Quick Checklist

- [ ] `APP_URL` is set to `https://your-domain.vercel.app`
- [ ] `VITE_BASE_URL` is set to `https://your-domain.vercel.app`
- [ ] `FRONTEND_URL` is set to `https://your-domain.vercel.app`
- [ ] All URLs use `https://` (not `http://`)
- [ ] Domain matches your actual Vercel domain
- [ ] Variables are set for **Production** environment
- [ ] Redeployed after updating variables
- [ ] Cleared browser cache

---

## 🎯 Most Important

**Update `APP_URL` to use `https://` and your correct domain!**

This is the #1 cause of mixed content errors.
