# How to Find Railway Public Hostname - Visual Guide

## 🎯 Quick Answer

You need to find the **public hostname** from Railway dashboard. It will look like:
- `containers-us-west-xxx.railway.app`
- `mysql-production.up.railway.app`
- `[service-name].up.railway.app`

**NOT** `mysql.railway.internal` (that's internal only)

---

## 📸 Step-by-Step with Screenshots Guide

### Method 1: Using "Connect" Tab (Easiest)

1. **Go to Railway Dashboard**
   ```
   https://railway.app/dashboard
   ```

2. **Click on Your Project**
   - Find the project that has your MySQL database
   - Click on it

3. **Click on MySQL/Database Service**
   - Look for a service card that says "MySQL" or "Database"
   - Click on it

4. **Click "Connect" Tab**
   - At the top, you'll see tabs: Deployments, Metrics, Settings, **Connect**, etc.
   - Click on **"Connect"**

5. **Look for Public Networking Section**
   - You should see something like:
     ```
     Public Networking: Enabled
     Public URL: containers-us-west-xxx.railway.app:3306
     ```
   - **Copy the hostname part** (without the port): `containers-us-west-xxx.railway.app`

6. **If Public Networking is Disabled**
   - You'll see: "Public Networking: Disabled"
   - Click "Enable Public Networking" button
   - Railway will generate a public URL
   - Copy that hostname

---

### Method 2: Using "Variables" Tab

1. **Go to Your MySQL Service**
   - Same as above, click on your MySQL service

2. **Click "Variables" Tab**
   - Look for environment variables

3. **Find Database Variables**
   - Look for variables like:
     - `MYSQLHOST` or `MYSQL_HOST`
     - `DATABASE_URL`
     - `MYSQL_URL`
   - Check their values - they might contain the public hostname

4. **Check Connection String Format**
   - If you see `DATABASE_URL`, it might look like:
     ```
     mysql://root:password@containers-us-west-xxx.railway.app:3306/railway
     ```
   - Extract the hostname: `containers-us-west-xxx.railway.app`

---

### Method 3: Using Settings → Networking

1. **Go to Your MySQL Service**
2. **Click "Settings" Tab**
3. **Scroll to "Networking" Section**
4. **Enable Public Networking**
   - Toggle "Public Networking" to **ON**
   - Railway will show you the public URL
   - Copy the hostname

---

## 🔍 What to Look For

### ✅ CORRECT (Public Hostname - Use This):
```
containers-us-west-xxx.railway.app
mysql-production.up.railway.app
your-service-name.up.railway.app
```

### ❌ WRONG (Internal Hostname - Don't Use This):
```
mysql.railway.internal
localhost
127.0.0.1
```

---

## 📋 Quick Checklist

- [ ] Logged into Railway Dashboard
- [ ] Found your MySQL/database service
- [ ] Checked "Connect" tab
- [ ] Found "Public Networking" section
- [ ] Copied the public hostname (ends with `.railway.app`)
- [ ] Verified it does NOT contain `.railway.internal`

---

## 🎬 Example Workflow

```
1. Open Railway Dashboard
   ↓
2. Click Project → MySQL Service
   ↓
3. Click "Connect" Tab
   ↓
4. See: Public URL: containers-us-west-123.railway.app:3306
   ↓
5. Copy: containers-us-west-123.railway.app
   ↓
6. Use in Vercel as DB_HOST
```

---

## 🚨 Troubleshooting

### Problem: Can't find "Connect" tab
**Solution:** 
- Make sure you're looking at the MySQL/database service, not your application service
- Try refreshing the page

### Problem: Public Networking is disabled and can't enable it
**Solution:**
- Check your Railway plan - some free tiers might have limitations
- Contact Railway support or upgrade your plan

### Problem: Only see internal hostname
**Solution:**
- Enable Public Networking in Settings → Networking
- Wait a few seconds for Railway to generate the public URL

### Problem: Can't find the service
**Solution:**
- Check if the database service is running
- Look in "Deployments" tab to see service status
- Make sure you're in the correct project

---

## 💡 Pro Tips

1. **Bookmark the Connect Tab**
   - Once you find it, bookmark it for quick access

2. **Copy All Connection Details**
   - While you're there, copy:
     - Hostname
     - Port
     - Database name
     - Username
     - Password

3. **Test Connection First**
   - Before adding to Vercel, test the connection from your local machine:
     ```bash
     mysql -h containers-us-west-xxx.railway.app -P 3306 -u root -p
     ```

4. **Save Connection String**
   - Railway shows the full connection string - save it somewhere safe
   - Format: `mysql://username:password@hostname:port/database`

---

## 📞 Still Can't Find It?

1. **Check Railway Documentation**
   - https://docs.railway.app/databases/mysql
   - https://docs.railway.app/databases/connect

2. **Check Railway Status**
   - Make sure Railway services are operational

3. **Contact Railway Support**
   - Railway has a Discord community
   - Or check their support channels

4. **Alternative: Use Railway CLI**
   ```bash
   railway connect mysql
   ```
   This might show connection details

---

## ✅ Once You Have It

After you get the public hostname, add it to Vercel:

```
DB_HOST = containers-us-west-xxx.railway.app  ← Your public hostname here
DB_PORT = 3306
DB_DATABASE = railway
DB_USERNAME = root
DB_PASSWORD = CeIRPTFsoOWmMLkYHiaDlrJytxIknjGZ
```

Then redeploy your Vercel application!
