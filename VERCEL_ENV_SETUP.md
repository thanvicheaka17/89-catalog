# Vercel Environment Variables Setup Guide

This guide shows you how to add environment variables to Vercel to support your Laravel application and database.

## 📋 Prerequisites

Before adding environment variables, you need:
1. A Vercel account (already set up ✅)
2. Your project linked to Vercel (already done ✅)
3. An external database (Vercel doesn't provide databases)

---

## 🗄️ Database Setup Options

Since Vercel doesn't provide databases, you need an external database. Here are recommended options:

### Option 1: PlanetScale (Recommended for MySQL)
- **Free tier available**
- Serverless MySQL
- **Sign up:** https://planetscale.com
- **Connection:** Use the connection string provided

### Option 2: Railway
- **Free tier available**
- MySQL/PostgreSQL
- **Sign up:** https://railway.app
- **Connection:** Use the connection details from Railway dashboard

### Option 3: Supabase
- **Free tier available**
- PostgreSQL (you may need to adjust Laravel config)
- **Sign up:** https://supabase.com

### Option 4: AWS RDS / DigitalOcean / Other
- Managed database services
- More control, but may require payment

---

## 🔧 Method 1: Add Environment Variables via Vercel Dashboard (Recommended)

### Step-by-Step Instructions

1. **Go to Vercel Dashboard**
   - Visit: https://vercel.com/dashboard
   - Click on your project: **89-catalog**

2. **Navigate to Settings**
   - Click **"Settings"** tab at the top
   - Click **"Environment Variables"** in the left sidebar

3. **Add Database Variables**

   Click **"Add New"** and add each variable one by one:

   #### Required Database Variables:
   
   ```
   DB_CONNECTION = mysql
   DB_HOST = your-database-host.com (e.g., aws-0-us-east-1.pooler.supabase.com)
   DB_PORT = 3306 (or 5432 for PostgreSQL)
   DB_DATABASE = your-database-name
   DB_USERNAME = your-database-username
   DB_PASSWORD = your-database-password
   ```

   **Important:** Replace `DB_HOST` with your actual database host (NOT "db" which is for Docker)

4. **Add Laravel Application Variables**

   ```
   APP_NAME = CLICKENGINE
   APP_ENV = production
   APP_KEY = base64:f/NjDcs1bGoj2CBfqIrrtngJT6qvJZiTGTZ6LPOb0Wg=
   APP_DEBUG = false
   APP_URL = https://your-project.vercel.app
   ```

   **Note:** Generate a new `APP_KEY` for production:
   ```bash
   php artisan key:generate --show
   ```

5. **Add Vercel-Specific Cache Variables**

   ```
   VIEW_COMPILED_PATH = /tmp
   CACHE_DRIVER = array
   SESSION_DRIVER = cookie
   LOG_CHANNEL = stderr
   ```

6. **Add Other Required Variables**

   ```
   JWT_SECRET = 1M0pDl0Rpu8jSnliHUCyOy56Sg4VwvriCFI2rWFBXbujsXcrh94o43OuyhpWynA8
   QUEUE_CONNECTION = sync
   FILESYSTEM_DISK = s3 (or local, but S3 recommended for Vercel)
   ```

7. **Set Environment Scope**
   - For each variable, select which environments it applies to:
     - ✅ **Production** (for production deployments)
     - ✅ **Preview** (for preview deployments)
     - ✅ **Development** (for local development with Vercel CLI)

8. **Save**
   - Click **"Save"** after adding each variable

---

## 💻 Method 2: Add Environment Variables via Vercel CLI

### Setup

```bash
# Install Vercel CLI (if not already installed)
npm install -g vercel

# Login (if not already logged in)
vercel login

# Navigate to your project
cd "/media/garry/Data/vicheaka/backend/eightynine-catalog (Copy)"
```

### Add Variables One by One

```bash
# Database variables
vercel env add DB_CONNECTION production
# When prompted, enter: mysql

vercel env add DB_HOST production
# When prompted, enter: your-database-host.com

vercel env add DB_PORT production
# When prompted, enter: 3306

vercel env add DB_DATABASE production
# When prompted, enter: your-database-name

vercel env add DB_USERNAME production
# When prompted, enter: your-database-username

vercel env add DB_PASSWORD production
# When prompted, enter: your-database-password (it will be hidden)

# Laravel app variables
vercel env add APP_NAME production
vercel env add APP_ENV production
vercel env add APP_KEY production
vercel env add APP_DEBUG production
vercel env add APP_URL production

# Vercel-specific
vercel env add VIEW_COMPILED_PATH production
vercel env add CACHE_DRIVER production
vercel env add SESSION_DRIVER production
vercel env add LOG_CHANNEL production

# Other required
vercel env add JWT_SECRET production
vercel env add QUEUE_CONNECTION production
```

### Add Multiple Variables from File

```bash
# Pull existing environment variables to a file
vercel env pull .env.production

# Edit the file with your variables
nano .env.production

# Push all variables back (this will add new ones)
vercel env push .env.production production
```

---

## 📝 Complete Environment Variables List

Here's the complete list of variables you should add to Vercel:

### Core Application
```
APP_NAME=CLICKENGINE
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-project.vercel.app
```

### Database (MySQL Example)
```
DB_CONNECTION=mysql
DB_HOST=your-database-host.com
DB_PORT=3306
DB_DATABASE=your-database-name
DB_USERNAME=your-database-user
DB_PASSWORD=your-database-password
```

### Vercel-Specific (Required)
```
VIEW_COMPILED_PATH=/tmp
CACHE_DRIVER=array
SESSION_DRIVER=cookie
LOG_CHANNEL=stderr
```

### Cache Paths (Optional but Recommended)
```
APP_CONFIG_CACHE=/tmp/config.php
APP_EVENTS_CACHE=/tmp/events.php
APP_PACKAGES_CACHE=/tmp/packages.php
APP_ROUTES_CACHE=/tmp/routes.php
APP_SERVICES_CACHE=/tmp/services.php
```

### Application Features
```
JWT_SECRET=your-jwt-secret-key
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=s3
BROADCAST_DRIVER=log
```

### AWS S3 (If using cloud storage)
```
AWS_ACCESS_KEY_ID=your-aws-access-key
AWS_SECRET_ACCESS_KEY=your-aws-secret-key
AWS_DEFAULT_REGION=ap-southeast-2
AWS_BUCKET=your-bucket-name
AWS_USE_PATH_STYLE_ENDPOINT=false
```

### Frontend Variables (If needed)
```
VITE_BASE_URL=https://your-project.vercel.app
VITE_API_KEY=your-api-key
```

### Mail Configuration (If needed)
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 🔐 Security Best Practices

1. **Never commit `.env` files** ✅ (already in `.gitignore`)
2. **Use different `APP_KEY` for production** - Generate a new one
3. **Use strong database passwords**
4. **Don't share environment variables** publicly
5. **Use Vercel's environment variable encryption** (automatic)
6. **Rotate secrets regularly**

---

## 🧪 Testing Your Database Connection

After adding environment variables, test the connection:

### Option 1: Create a Test Route (Temporary)

Add to `routes/web.php`:
```php
Route::get('/test-db', function() {
    try {
        DB::connection()->getPdo();
        return 'Database connection successful!';
    } catch (\Exception $e) {
        return 'Database connection failed: ' . $e->getMessage();
    }
});
```

**⚠️ Remove this route after testing!**

### Option 2: Check Deployment Logs

1. Deploy your application
2. Check Vercel deployment logs
3. Look for database connection errors

---

## 🔄 Updating Environment Variables

### Via Dashboard:
1. Go to Settings → Environment Variables
2. Click on the variable you want to edit
3. Update the value
4. Click Save
5. Redeploy your application

### Via CLI:
```bash
# Update a variable
vercel env rm VARIABLE_NAME production
vercel env add VARIABLE_NAME production

# Or pull, edit, and push
vercel env pull .env.production
# Edit .env.production
vercel env push .env.production production
```

---

## 🚨 Common Issues & Solutions

### Issue: Database Connection Failed

**Symptoms:**
- Error: "SQLSTATE[HY000] [2002] Connection refused"
- Error: "Access denied for user"

**Solutions:**
1. ✅ Check `DB_HOST` - Make sure it's your actual database host (not "db" or "localhost")
2. ✅ Verify database credentials are correct
3. ✅ Check if database allows external connections
4. ✅ Verify firewall/security groups allow Vercel IPs
5. ✅ Check `DB_PORT` is correct (3306 for MySQL, 5432 for PostgreSQL)

### Issue: APP_KEY Missing

**Solution:**
```bash
# Generate a new key locally
php artisan key:generate --show

# Copy the output and add to Vercel as APP_KEY
```

### Issue: Cache/Storage Errors

**Solution:**
- Ensure `VIEW_COMPILED_PATH=/tmp` is set
- Set `CACHE_DRIVER=array` (not "file")
- Use cloud storage (S3) for file uploads

### Issue: Environment Variables Not Loading

**Solutions:**
1. ✅ Check environment scope (Production/Preview/Development)
2. ✅ Redeploy after adding variables
3. ✅ Verify variable names match exactly (case-sensitive)
4. ✅ Check for typos in variable names

---

## 📊 Environment Variable Checklist

Use this checklist to ensure you have all required variables:

### ✅ Required for Database
- [ ] `DB_CONNECTION`
- [ ] `DB_HOST` (⚠️ NOT "db" - use your actual database host)
- [ ] `DB_PORT`
- [ ] `DB_DATABASE`
- [ ] `DB_USERNAME`
- [ ] `DB_PASSWORD`

### ✅ Required for Laravel
- [ ] `APP_NAME`
- [ ] `APP_ENV` (set to "production")
- [ ] `APP_KEY` (generate new for production)
- [ ] `APP_DEBUG` (set to "false")
- [ ] `APP_URL` (your Vercel URL)

### ✅ Required for Vercel
- [ ] `VIEW_COMPILED_PATH=/tmp`
- [ ] `CACHE_DRIVER=array`
- [ ] `SESSION_DRIVER=cookie`
- [ ] `LOG_CHANNEL=stderr`

### ✅ Recommended
- [ ] `JWT_SECRET`
- [ ] `QUEUE_CONNECTION=sync`
- [ ] `FILESYSTEM_DISK=s3` (if using cloud storage)

---

## 🎯 Quick Start: Database Setup Example

### Using PlanetScale (MySQL)

1. **Sign up at PlanetScale**
2. **Create a database**
3. **Get connection string** - It looks like:
   ```
   mysql://username:password@host:port/database
   ```

4. **Add to Vercel:**
   ```
   DB_CONNECTION=mysql
   DB_HOST=aws-0-us-east-1.pooler.supabase.com (from connection string)
   DB_PORT=3306
   DB_DATABASE=your-db-name
   DB_USERNAME=your-username
   DB_PASSWORD=your-password
   ```

5. **Deploy and test!**

---

## 📚 Additional Resources

- [Vercel Environment Variables Docs](https://vercel.com/docs/concepts/projects/environment-variables)
- [Laravel Database Configuration](https://laravel.com/docs/database)
- [PlanetScale Documentation](https://planetscale.com/docs)
- [Railway Documentation](https://docs.railway.app)

---

## 💡 Pro Tips

1. **Use different databases for production and preview**
   - Set different `DB_*` variables for Production vs Preview environments

2. **Use Vercel's environment variable templates**
   - Create a template for common variables

3. **Test in Preview first**
   - Deploy to preview environment before production

4. **Monitor database connections**
   - Check your database provider's dashboard for connection logs

5. **Backup your database**
   - Regular backups are essential for production

---

## 🆘 Need Help?

If you encounter issues:
1. Check Vercel deployment logs
2. Verify database connection from your local machine
3. Test database credentials
4. Check Vercel status page: https://vercel-status.com
