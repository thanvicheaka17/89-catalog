# Vercel Deployment Troubleshooting Guide

## 🚨 Common Errors and Solutions

### Error: "404: NOT_FOUND - DEPLOYMENT_NOT_FOUND"

This error typically means:
1. The deployment failed during build
2. The serverless function wasn't created properly
3. Configuration issue with `vercel.json`

#### Solutions:

**1. Check Build Logs**
- Go to Vercel Dashboard → Your Project → Deployments
- Click on the failed deployment
- Check "Build Logs" for errors
- Common issues:
  - Missing dependencies
  - Build command failures
  - PHP runtime errors

**2. Verify vercel.json Configuration**
- Ensure `api/index.php` exists
- Check that the runtime is correct: `vercel-php@0.7.3`
- Verify routes are configured properly

**3. Check Environment Variables**
- Ensure all required environment variables are set
- Especially: `APP_KEY`, `DB_*` variables
- Check Vercel Dashboard → Settings → Environment Variables

**4. Verify File Structure**
```
your-project/
├── api/
│   └── index.php  ← Must exist!
├── public/
│   └── index.php
├── vercel.json
└── .vercelignore
```

**5. Test Locally First**
```bash
# Install Vercel CLI
npm install -g vercel

# Test deployment locally
vercel dev
```

---

### Error: "Function Not Found" or "Runtime Error"

#### Solutions:

**1. Check api/index.php**
Ensure it exists and has correct content:
```php
<?php
require __DIR__ . '/../public/index.php';
```

**2. Verify Build Command**
Check if build command completes successfully:
```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

**3. Check PHP Runtime Version**
- Current: `vercel-php@0.7.3`
- If issues, try: `vercel-php@0.6.2` or `vercel-php@0.5.5`

---

### Error: "Database Connection Failed"

#### Solutions:

**1. Verify Database Variables**
- Check all `DB_*` variables are set in Vercel
- Ensure `DB_HOST` uses public hostname (not `.railway.internal`)
- Verify credentials are correct

**2. Test Database Connection**
```bash
# From your local machine
mysql -h your-db-host.railway.app -P 3306 -u root -p
```

**3. Check Database Firewall**
- Ensure Railway database allows external connections
- Verify public networking is enabled

---

### Error: "500 Internal Server Error"

#### Solutions:

**1. Enable Debug Mode Temporarily**
In Vercel environment variables:
```
APP_DEBUG = true
```
⚠️ **Disable after debugging!**

**2. Check Application Logs**
- Vercel Dashboard → Your Project → Functions → View Logs
- Look for PHP errors or Laravel exceptions

**3. Check Laravel Logs**
- Since logs go to stderr, check Vercel function logs
- Look for stack traces or error messages

**4. Verify APP_KEY**
- Ensure `APP_KEY` is set in Vercel
- Generate new one if needed:
  ```bash
  php artisan key:generate --show
  ```

---

### Error: "Static Assets Not Loading"

#### Solutions:

**1. Check Routes in vercel.json**
Ensure static assets are routed correctly:
```json
{
  "src": "/(.*\\.(js|css|ico|png|jpg|jpeg|gif|svg|webp))",
  "dest": "/public/$1"
}
```

**2. Verify Build Output**
- Check if `npm run build` completes successfully
- Verify `public/build/` directory exists with assets

**3. Check Base URL**
- Ensure `APP_URL` matches your Vercel domain
- Check `VITE_BASE_URL` if using Vite

---

## 🔍 Debugging Steps

### Step 1: Check Deployment Status

1. Go to Vercel Dashboard
2. Click on your project
3. Check "Deployments" tab
4. Look for:
   - ✅ Green checkmark = Success
   - ❌ Red X = Failed
   - ⏳ Yellow = Building

### Step 2: Review Build Logs

1. Click on the deployment
2. Scroll to "Build Logs"
3. Look for:
   - Error messages
   - Failed commands
   - Missing files
   - Permission errors

### Step 3: Check Function Logs

1. Go to "Functions" tab
2. Click on `api/index.php`
3. Check "Logs" for runtime errors

### Step 4: Verify Environment Variables

1. Go to Settings → Environment Variables
2. Verify all required variables are set:
   - `APP_KEY`
   - `DB_*` variables
   - `APP_URL`
   - Cache variables

### Step 5: Test Health Endpoint

Try accessing:
```
https://your-project.vercel.app/up
```

This is Laravel's health check endpoint. If it works, Laravel is running.

---

## 🛠️ Quick Fixes

### Fix 1: Redeploy

```bash
# Via CLI
vercel --prod

# Or trigger via GitHub push
git commit --allow-empty -m "Trigger redeploy"
git push origin main
```

### Fix 2: Clear Build Cache

1. Vercel Dashboard → Settings → General
2. Scroll to "Build Cache"
3. Click "Clear Build Cache"
4. Redeploy

### Fix 3: Update vercel.json

Ensure you're using the latest configuration format:
```json
{
  "version": 2,
  "functions": {
    "api/index.php": {
      "runtime": "vercel-php@0.7.3"
    }
  },
  "routes": [...]
}
```

### Fix 4: Check .vercelignore

Ensure `.vercelignore` doesn't exclude necessary files:
```
# Should NOT ignore:
# /api
# /public
# /bootstrap
# /config
# /routes
# /app
```

---

## 📋 Pre-Deployment Checklist

Before deploying, ensure:

- [ ] `api/index.php` exists and is correct
- [ ] `vercel.json` is properly configured
- [ ] `.vercelignore` is set up correctly
- [ ] All environment variables are set in Vercel
- [ ] `APP_KEY` is generated and set
- [ ] Database variables are configured
- [ ] Build command works locally
- [ ] `composer.json` and `package.json` are present
- [ ] No syntax errors in PHP files
- [ ] Routes are properly defined

---

## 🧪 Testing Deployment

### Test 1: Health Check
```bash
curl https://your-project.vercel.app/up
```
Should return: `{"status":"ok"}` or similar

### Test 2: Database Connection
Create a test route (temporary):
```php
Route::get('/test-db', function() {
    try {
        DB::connection()->getPdo();
        return 'Database OK';
    } catch (\Exception $e) {
        return 'Database Error: ' . $e->getMessage();
    }
});
```

### Test 3: Static Assets
```bash
curl https://your-project.vercel.app/build/app.js
```
Should return JavaScript content (not 404)

---

## 📞 Getting Help

1. **Check Vercel Logs**
   - Dashboard → Deployments → Build Logs
   - Dashboard → Functions → Logs

2. **Check Laravel Logs**
   - Since `LOG_CHANNEL=stderr`, check Vercel function logs

3. **Vercel Documentation**
   - https://vercel.com/docs
   - https://vercel.com/docs/functions/runtimes/php

4. **Community Support**
   - Vercel Discord
   - Laravel Community Forums

---

## 🔄 Common Workflows

### Workflow 1: Fix Failed Deployment

1. Check build logs for errors
2. Fix the issue locally
3. Test locally: `vercel dev`
4. Commit and push: `git push origin main`
5. Monitor new deployment

### Workflow 2: Update Environment Variables

1. Go to Vercel Dashboard → Settings → Environment Variables
2. Add/Update variables
3. Redeploy (automatic or manual)
4. Test the changes

### Workflow 3: Debug Production Issue

1. Enable `APP_DEBUG=true` temporarily
2. Check function logs
3. Identify the issue
4. Fix and redeploy
5. Disable `APP_DEBUG=false`

---

## 💡 Pro Tips

1. **Always test locally first**
   ```bash
   vercel dev
   ```

2. **Use preview deployments**
   - Test changes in preview before production
   - Each PR gets its own preview URL

3. **Monitor build times**
   - Long builds (>10 min) might timeout
   - Optimize build command if needed

4. **Keep dependencies updated**
   - Regularly update Composer and npm packages
   - Check for security vulnerabilities

5. **Use environment-specific configs**
   - Different DB for preview vs production
   - Different APP_URL for each environment
