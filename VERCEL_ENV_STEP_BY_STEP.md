# Step-by-Step: Add Railway Database Environment Variables to Vercel

## Your Railway Connection String
```
mysql://root:CeIRPTFsoOWmMLkYHiaDlrJytxIknjGZ@switchyard.proxy.rlwy.net:17113/railway
```

## Breaking Down the Connection String

- **Protocol:** `mysql://`
- **Username:** `root`
- **Password:** `CeIRPTFsoOWmMLkYHiaDlrJytxIknjGZ`
- **Host:** `switchyard.proxy.rlwy.net` ✅ (This is a PUBLIC hostname - perfect!)
- **Port:** `17113`
- **Database:** `railway`

---

## Step-by-Step Instructions

### Step 1: Go to Vercel Dashboard

1. Visit: https://vercel.com/dashboard
2. Click on your project: **89-catalog**

### Step 2: Navigate to Environment Variables

1. Click **"Settings"** tab (at the top)
2. Click **"Environment Variables"** (in the left sidebar)

### Step 3: Add Database Variables One by One

Click **"Add New"** button for each variable below:

---

#### Variable 1: DB_CONNECTION

1. **Key:** `DB_CONNECTION`
2. **Value:** `mysql`
3. **Environment:** Select all three:
   - ✅ Production
   - ✅ Preview
   - ✅ Development
4. Click **"Save"**

---

#### Variable 2: DB_HOST

1. **Key:** `DB_HOST`
2. **Value:** `switchyard.proxy.rlwy.net`
   - ⚠️ **Important:** Use exactly this value (no `mysql://`, no port, just the hostname)
3. **Environment:** Select all three:
   - ✅ Production
   - ✅ Preview
   - ✅ Development
4. Click **"Save"**

---

#### Variable 3: DB_PORT

1. **Key:** `DB_PORT`
2. **Value:** `17113`
   - ⚠️ **Note:** Your Railway port is `17113` (not the standard `3306`)
3. **Environment:** Select all three:
   - ✅ Production
   - ✅ Preview
   - ✅ Development
4. Click **"Save"**

---

#### Variable 4: DB_DATABASE

1. **Key:** `DB_DATABASE`
2. **Value:** `railway`
3. **Environment:** Select all three:
   - ✅ Production
   - ✅ Preview
   - ✅ Development
4. Click **"Save"**

---

#### Variable 5: DB_USERNAME

1. **Key:** `DB_USERNAME`
2. **Value:** `root`
3. **Environment:** Select all three:
   - ✅ Production
   - ✅ Preview
   - ✅ Development
4. Click **"Save"**

---

#### Variable 6: DB_PASSWORD

1. **Key:** `DB_PASSWORD`
2. **Value:** `CeIRPTFsoOWmMLkYHiaDlrJytxIknjGZ`
   - ⚠️ **Important:** Copy this exactly, it's case-sensitive
3. **Environment:** Select all three:
   - ✅ Production
   - ✅ Preview
   - ✅ Development
4. Click **"Save"**

---

## Step 4: Add Other Required Laravel Variables

After adding database variables, add these essential Laravel variables:

### Variable 7: APP_NAME

1. **Key:** `APP_NAME`
2. **Value:** `CLICKENGINE`
3. **Environment:** All three
4. Click **"Save"**

---

### Variable 8: APP_ENV

1. **Key:** `APP_ENV`
2. **Value:** `production`
3. **Environment:** All three
4. Click **"Save"**

---

### Variable 9: APP_KEY ⚠️ CRITICAL!

1. **Key:** `APP_KEY`
2. **Value:** Generate a new one:
   ```bash
   php artisan key:generate --show
   ```
   Copy the output (starts with `base64:`)
3. **Environment:** All three
4. Click **"Save"**

**If you don't have PHP locally, use this temporary key:**
```
base64:f/NjDcs1bGoj2CBfqIrrtngJT6qvJZiTGTZ6LPOb0Wg=
```
⚠️ **Generate a new one for production later!**

---

### Variable 10: APP_DEBUG

1. **Key:** `APP_DEBUG`
2. **Value:** `false`
3. **Environment:** All three
4. Click **"Save"**

---

### Variable 11: APP_URL

1. **Key:** `APP_URL`
2. **Value:** `https://89-catalog23424s.vercel.app`
   - ⚠️ Replace with your actual Vercel domain
3. **Environment:** All three
4. Click **"Save"**

---

### Variable 12: VIEW_COMPILED_PATH

1. **Key:** `VIEW_COMPILED_PATH`
2. **Value:** `/tmp`
3. **Environment:** All three
4. Click **"Save"**

---

### Variable 13: CACHE_DRIVER

1. **Key:** `CACHE_DRIVER`
2. **Value:** `array`
3. **Environment:** All three
4. Click **"Save"**

---

### Variable 14: SESSION_DRIVER

1. **Key:** `SESSION_DRIVER`
2. **Value:** `cookie`
3. **Environment:** All three
4. Click **"Save"**

---

### Variable 15: LOG_CHANNEL

1. **Key:** `LOG_CHANNEL`
2. **Value:** `stderr`
3. **Environment:** All three
4. Click **"Save"**

---

### Variable 16: JWT_SECRET

1. **Key:** `JWT_SECRET`
2. **Value:** `1M0pDl0Rpu8jSnliHUCyOy56Sg4VwvriCFI2rWFBXbujsXcrh94o43OuyhpWynA8`
   - (From your .env file)
3. **Environment:** All three
4. Click **"Save"**

---

### Variable 17: QUEUE_CONNECTION

1. **Key:** `QUEUE_CONNECTION`
2. **Value:** `sync`
3. **Environment:** All three
4. Click **"Save"**

---

## Step 5: Verify All Variables

After adding all variables, verify they're all there:

1. Scroll through the Environment Variables list
2. Check that you have:
   - ✅ `DB_CONNECTION`
   - ✅ `DB_HOST`
   - ✅ `DB_PORT`
   - ✅ `DB_DATABASE`
   - ✅ `DB_USERNAME`
   - ✅ `DB_PASSWORD`
   - ✅ `APP_NAME`
   - ✅ `APP_ENV`
   - ✅ `APP_KEY` ← **Most Important!**
   - ✅ `APP_DEBUG`
   - ✅ `APP_URL`
   - ✅ `VIEW_COMPILED_PATH`
   - ✅ `CACHE_DRIVER`
   - ✅ `SESSION_DRIVER`
   - ✅ `LOG_CHANNEL`
   - ✅ `JWT_SECRET`
   - ✅ `QUEUE_CONNECTION`

---

## Step 6: Redeploy

After adding all environment variables:

1. Go to **"Deployments"** tab
2. Click the **"..."** menu on the latest deployment
3. Click **"Redeploy"**
4. Or push a new commit to trigger automatic deployment

---

## Quick Reference: All Values

Copy-paste reference:

```
DB_CONNECTION=mysql
DB_HOST=switchyard.proxy.rlwy.net
DB_PORT=17113
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=CeIRPTFsoOWmMLkYHiaDlrJytxIknjGZ

APP_NAME=CLICKENGINE
APP_ENV=production
APP_KEY=base64:f/NjDcs1bGoj2CBfqIrrtngJT6qvJZiTGTZ6LPOb0Wg=
APP_DEBUG=false
APP_URL=https://89-catalog23424s.vercel.app

VIEW_COMPILED_PATH=/tmp
CACHE_DRIVER=array
SESSION_DRIVER=cookie
LOG_CHANNEL=stderr

JWT_SECRET=1M0pDl0Rpu8jSnliHUCyOy56Sg4VwvriCFI2rWFBXbujsXcrh94o43OuyhpWynA8
QUEUE_CONNECTION=sync
```

---

## Important Notes

1. **DB_PORT is 17113** - Not the standard 3306, this is Railway's proxy port
2. **DB_HOST is public** - `switchyard.proxy.rlwy.net` is perfect for Vercel
3. **APP_KEY is critical** - Without it, Laravel will throw 500 errors
4. **APP_URL** - Make sure it matches your actual Vercel domain
5. **Environment Scope** - Select all three (Production, Preview, Development) for each variable

---

## Testing After Setup

1. **Test Health Endpoint:**
   ```
   https://89-catalog23424s.vercel.app/up
   ```
   Should return: `{"status":"ok"}` or similar

2. **Check Function Logs:**
   - Go to Functions → api/index.php → Logs
   - Should see no database connection errors

3. **If Still Getting 500 Error:**
   - Enable `APP_DEBUG=true` temporarily
   - Check function logs for specific errors
   - Verify all variables are set correctly

---

## Troubleshooting

### If Database Connection Fails:

1. **Verify Railway Database:**
   - Go to Railway Dashboard
   - Check if database service is running
   - Verify public networking is enabled

2. **Test Connection Locally:**
   ```bash
   mysql -h switchyard.proxy.rlwy.net -P 17113 -u root -p railway
   # Enter password: CeIRPTFsoOWmMLkYHiaDlrJytxIknjGZ
   ```

3. **Check Variable Names:**
   - Ensure exact spelling (case-sensitive)
   - No extra spaces
   - No quotes around values

---

## Done! 🎉

After completing all steps and redeploying, your Laravel app should connect to Railway database successfully!
