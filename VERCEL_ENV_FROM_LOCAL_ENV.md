# Step-by-Step: Add Environment Variables to Vercel (Based on Your .env File)

## Your Local .env File Reference

From your `.env` file (lines 10-20):
```env
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=info

DB_CONNECTION=mysql
DB_HOST=db                    ← Docker hostname (change for Vercel!)
DB_PORT=3306                  ← Docker port (change for Railway!)
DB_DATABASE=eightynine-catalog
DB_USERNAME=eightynine-catalog-user
DB_PASSWORD=eightynine-catalog-pass
DB_PORT_BIRD=33906
```

## ⚠️ Important: Railway vs Local Differences

Your local `.env` uses Docker settings. For Vercel with Railway, use these values instead:

| Variable | Local (.env) | Vercel (Railway) |
|----------|--------------|------------------|
| `DB_HOST` | `db` | `switchyard.proxy.rlwy.net` ✅ |
| `DB_PORT` | `3306` | `17113` ✅ |
| `DB_DATABASE` | `eightynine-catalog` | `railway` ✅ |
| `DB_USERNAME` | `eightynine-catalog-user` | `root` ✅ |
| `DB_PASSWORD` | `eightynine-catalog-pass` | `CeIRPTFsoOWmMLkYHiaDlrJytxIknjGZ` ✅ |

---

## Step-by-Step: Add to Vercel

### Step 1: Go to Vercel Dashboard

1. Visit: https://vercel.com/dashboard
2. Click on your project: **89-catalog**
3. Click **"Settings"** tab
4. Click **"Environment Variables"** (left sidebar)

---

### Step 2: Add Database Variables

Click **"Add New"** for each variable:

---

#### Variable 1: DB_CONNECTION

1. **Key:** `DB_CONNECTION`
2. **Value:** `mysql`
   - ✅ Same as your local `.env`
3. **Environment:** Select all three:
   - ✅ Production
   - ✅ Preview  
   - ✅ Development
4. Click **"Save"**

---

#### Variable 2: DB_HOST ⚠️ IMPORTANT!

1. **Key:** `DB_HOST`
2. **Value:** `switchyard.proxy.rlwy.net`
   - ⚠️ **CHANGE:** Your local has `db` (Docker), but use Railway's public hostname
3. **Environment:** Select all three
4. Click **"Save"**

---

#### Variable 3: DB_PORT ⚠️ IMPORTANT!

1. **Key:** `DB_PORT`
2. **Value:** `17113`
   - ⚠️ **CHANGE:** Your local has `3306` (Docker), but Railway uses `17113`
3. **Environment:** Select all three
4. Click **"Save"**

---

#### Variable 4: DB_DATABASE ⚠️ IMPORTANT!

1. **Key:** `DB_DATABASE`
2. **Value:** `railway`
   - ⚠️ **CHANGE:** Your local has `eightynine-catalog`, but Railway database is `railway`
3. **Environment:** Select all three
4. Click **"Save"**

---

#### Variable 5: DB_USERNAME ⚠️ IMPORTANT!

1. **Key:** `DB_USERNAME`
2. **Value:** `root`
   - ⚠️ **CHANGE:** Your local has `eightynine-catalog-user`, but Railway uses `root`
3. **Environment:** Select all three
4. Click **"Save"**

---

#### Variable 6: DB_PASSWORD ⚠️ IMPORTANT!

1. **Key:** `DB_PASSWORD`
2. **Value:** `CeIRPTFsoOWmMLkYHiaDlrJytxIknjGZ`
   - ⚠️ **CHANGE:** Your local has `eightynine-catalog-pass`, but Railway password is different
   - ⚠️ Copy exactly - it's case-sensitive
3. **Environment:** Select all three
4. Click **"Save"**

---

### Step 3: Add Logging Variables (From Your .env)

#### Variable 7: LOG_CHANNEL

1. **Key:** `LOG_CHANNEL`
2. **Value:** `stderr`
   - ⚠️ **CHANGE:** Your local has `stack`, but Vercel needs `stderr` for serverless
3. **Environment:** Select all three
4. Click **"Save"**

---

#### Variable 8: LOG_DEPRECATIONS_CHANNEL

1. **Key:** `LOG_DEPRECATIONS_CHANNEL`
2. **Value:** `null`
   - ✅ Same as your local `.env`
3. **Environment:** Select all three
4. Click **"Save"**

---

#### Variable 9: LOG_LEVEL

1. **Key:** `LOG_LEVEL`
2. **Value:** `info`
   - ✅ Same as your local `.env`
3. **Environment:** Select all three
4. Click **"Save"**

---

### Step 4: Add Required Laravel Variables

#### Variable 10: APP_NAME

1. **Key:** `APP_NAME`
2. **Value:** `CLICKENGINE`
   - (From your .env line 1)
3. **Environment:** Select all three
4. Click **"Save"**

---

#### Variable 11: APP_ENV

1. **Key:** `APP_ENV`
2. **Value:** `production`
   - ⚠️ **CHANGE:** Your local has `local`, but production should be `production`
3. **Environment:** Select all three
4. Click **"Save"**

---

#### Variable 12: APP_KEY ⚠️ CRITICAL!

1. **Key:** `APP_KEY`
2. **Value:** `base64:f/NjDcs1bGoj2CBfqIrrtngJT6qvJZiTGTZ6LPOb0Wg=`
   - ✅ From your local .env (line 3)
   - ⚠️ **IMPORTANT:** This is required! Without it, you'll get 500 errors
3. **Environment:** Select all three
4. Click **"Save"**

---

#### Variable 13: APP_DEBUG

1. **Key:** `APP_DEBUG`
2. **Value:** `false`
   - ⚠️ **CHANGE:** Your local has `true`, but production should be `false`
3. **Environment:** Select all three
4. Click **"Save"**

---

#### Variable 14: APP_URL

1. **Key:** `APP_URL`
2. **Value:** `https://89-catalog23424s.vercel.app`
   - ⚠️ **CHANGE:** Your local has `http://localhost`, but use your Vercel domain
   - Replace `89-catalog23424s` with your actual Vercel domain
3. **Environment:** Select all three
4. Click **"Save"**

---

#### Variable 15: VIEW_COMPILED_PATH

1. **Key:** `VIEW_COMPILED_PATH`
2. **Value:** `/tmp`
   - ⚠️ **REQUIRED for Vercel:** Not in your local .env, but needed for serverless
3. **Environment:** Select all three
4. Click **"Save"**

---

#### Variable 16: CACHE_DRIVER

1. **Key:** `CACHE_DRIVER`
2. **Value:** `array`
   - ⚠️ **CHANGE:** Your local might have `file`, but Vercel needs `array` (read-only filesystem)
3. **Environment:** Select all three
4. Click **"Save"**

---

#### Variable 17: SESSION_DRIVER

1. **Key:** `SESSION_DRIVER`
2. **Value:** `cookie`
   - ⚠️ **REQUIRED for Vercel:** Not in your local .env, but needed for serverless
3. **Environment:** Select all three
4. Click **"Save"**

---

### Step 5: Add Other Variables from Your .env

Check your full `.env` file and add any other variables your app needs, such as:

- `JWT_SECRET` (if you have it)
- `QUEUE_CONNECTION` (if you have it)
- `BROADCAST_DRIVER` (if you have it)
- `FILESYSTEM_DISK` (if you have it)
- Any API keys or secrets

---

## Quick Reference: Complete List

Here's everything you need to add:

### Database (Railway):
```
DB_CONNECTION=mysql
DB_HOST=switchyard.proxy.rlwy.net
DB_PORT=17113
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=CeIRPTFsoOWmMLkYHiaDlrJytxIknjGZ
```

### Logging:
```
LOG_CHANNEL=stderr
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=info
```

### Laravel Core:
```
APP_NAME=CLICKENGINE
APP_ENV=production
APP_KEY=base64:f/NjDcs1bGoj2CBfqIrrtngJT6qvJZiTGTZ6LPOb0Wg=
APP_DEBUG=false
APP_URL=https://89-catalog23424s.vercel.app
```

### Vercel-Specific:
```
VIEW_COMPILED_PATH=/tmp
CACHE_DRIVER=array
SESSION_DRIVER=cookie
```

---

## Summary of Changes from Local .env

| Variable | Local Value | Vercel Value | Reason |
|----------|-------------|--------------|--------|
| `DB_HOST` | `db` | `switchyard.proxy.rlwy.net` | Railway public hostname |
| `DB_PORT` | `3306` | `17113` | Railway proxy port |
| `DB_DATABASE` | `eightynine-catalog` | `railway` | Railway database name |
| `DB_USERNAME` | `eightynine-catalog-user` | `root` | Railway username |
| `DB_PASSWORD` | `eightynine-catalog-pass` | `CeIRPTFsoOWmMLkYHiaDlrJytxIknjGZ` | Railway password |
| `APP_ENV` | `local` | `production` | Production environment |
| `APP_DEBUG` | `true` | `false` | Production should not debug |
| `APP_URL` | `http://localhost` | `https://your-domain.vercel.app` | Vercel domain |
| `LOG_CHANNEL` | `stack` | `stderr` | Serverless logging |
| `CACHE_DRIVER` | `file` | `array` | Read-only filesystem |
| `SESSION_DRIVER` | (not set) | `cookie` | Serverless sessions |
| `VIEW_COMPILED_PATH` | (not set) | `/tmp` | Vercel temp directory |

---

## After Adding All Variables

1. **Verify:** Scroll through the list and ensure all variables are there
2. **Redeploy:** Go to Deployments → Redeploy
3. **Test:** Visit your Vercel URL and check if it works
4. **Check Logs:** If still 500 error, check Function Logs for specific errors

---

## Done! 🎉

After completing all steps, your Laravel app should work on Vercel with Railway database!
