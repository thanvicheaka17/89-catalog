# Vercel Deployment Guide

This guide will help you deploy your Laravel application to Vercel using Docker.

## Prerequisites

1. A GitHub account with your repository pushed
2. A Vercel account (sign up at https://vercel.com)
3. Your Laravel application configured with the necessary environment variables

## Deployment Steps

### 1. Connect Your GitHub Repository to Vercel

1. Go to [Vercel Dashboard](https://vercel.com/dashboard)
2. Click **"Add New Project"** or **"Import Project"**
3. Select **"Import Git Repository"**
4. Choose your GitHub repository: `thanvicheaka17/89-catalog`
5. Click **"Import"**

### 2. Configure Project Settings

Vercel should automatically detect your `vercel.json` file. Configure the following:

#### Project Settings:
- **Framework Preset**: Other (or leave as auto-detected)
- **Root Directory**: `./` (root of your repository)
- **Build Command**: `composer install --no-dev --optimize-autoloader && npm install && npm run build`
- **Output Directory**: Leave empty (not applicable for PHP)
- **Install Command**: Leave empty (handled by build command)

### 3. Set Environment Variables

In the Vercel project settings, go to **Settings → Environment Variables** and add all your Laravel environment variables:

**Required Environment Variables:**
```
APP_NAME=YourAppName
APP_ENV=production
APP_KEY=your-generated-app-key
APP_DEBUG=false
APP_URL=https://your-project.vercel.app

# Cache configuration (Vercel uses read-only filesystem)
VIEW_COMPILED_PATH=/tmp
CACHE_DRIVER=array
SESSION_DRIVER=cookie
LOG_CHANNEL=stderr

# Database configuration
DB_CONNECTION=mysql
DB_HOST=your-database-host
DB_PORT=3306
DB_DATABASE=your-database-name
DB_USERNAME=your-database-user
DB_PASSWORD=your-database-password

# Additional cache paths (use /tmp for Vercel)
APP_CONFIG_CACHE=/tmp/config.php
APP_EVENTS_CACHE=/tmp/events.php
APP_PACKAGES_CACHE=/tmp/packages.php
APP_ROUTES_CACHE=/tmp/routes.php
APP_SERVICES_CACHE=/tmp/services.php

# Add any other environment variables your app needs
# (JWT secret, API keys, etc.)
```

**Important Notes:**
- Generate a new `APP_KEY` using: `php artisan key:generate --show`
- Make sure `APP_URL` matches your Vercel deployment URL
- Never commit `.env` file to git (it's already in `.gitignore`)

### 4. Database Configuration

Since Vercel doesn't provide managed databases, you'll need to use an external database:

**Options:**
- **PlanetScale** (MySQL-compatible, serverless)
- **Railway** (MySQL/PostgreSQL)
- **Supabase** (PostgreSQL)
- **AWS RDS** (MySQL/PostgreSQL)
- **DigitalOcean Managed Database**

Update your `DB_*` environment variables in Vercel with your external database credentials.

### 5. Deploy

1. Click **"Deploy"** in Vercel
2. Vercel will:
   - Install PHP dependencies via Composer
   - Install Node.js dependencies via npm
   - Build frontend assets (Vite)
   - Deploy your Laravel application as a serverless function
3. Once deployment completes, you'll get a URL like: `https://your-project.vercel.app`

**Note:** The first deployment may take 5-10 minutes as it installs all dependencies.

### 6. Post-Deployment Setup

After the first deployment, you may need to run Laravel migrations:

**Option 1: Using Vercel CLI**
```bash
# Install Vercel CLI
npm i -g vercel

# Login
vercel login

# Link your project
vercel link

# Run migrations (if you have a way to execute commands)
# Note: Vercel doesn't support running commands directly
# You may need to create an API endpoint or use a migration service
```

**Option 2: Create a Migration Endpoint**
Create a temporary route in `routes/web.php`:
```php
Route::get('/run-migrations', function() {
    Artisan::call('migrate', ['--force' => true]);
    return 'Migrations completed';
})->middleware('auth'); // Protect this route!
```

**Important:** Remove or protect this route after running migrations!

### 7. Configure Custom Domain (Optional)

1. Go to **Settings → Domains** in your Vercel project
2. Add your custom domain
3. Follow DNS configuration instructions

## Troubleshooting

### Build Failures

1. **Check Build Logs**: Go to your deployment → "View Build Logs"
2. **Common Issues**:
   - Missing environment variables
   - Database connection issues
   - Permission errors (storage/bootstrap/cache)

### Application Errors

1. **Check Application Logs**: Vercel provides logs in the deployment dashboard
2. **Enable Debug Mode Temporarily**: Set `APP_DEBUG=true` to see detailed errors
3. **Check Laravel Logs**: If using external logging service

### Database Connection Issues

- Verify database credentials in environment variables
- Check if your database allows connections from Vercel's IP ranges
- Ensure database firewall/security groups allow external connections

### Storage Issues

Laravel's file storage is ephemeral on Vercel. Consider:
- Using **AWS S3** or **Cloudinary** for file storage
- Configure `config/filesystems.php` to use cloud storage
- Update `FILESYSTEM_DISK` environment variable

## Important Considerations

### Limitations

1. **Serverless Nature**: Vercel runs serverless functions, which may affect:
   - Long-running processes
   - WebSocket connections (Laravel Reverb) - **Not supported on Vercel**
   - Scheduled tasks (Laravel Scheduler) - Use Vercel Cron Jobs instead

2. **Read-Only Filesystem**: Vercel uses a read-only filesystem except for `/tmp`:
   - All cache paths must use `/tmp` directory
   - File uploads must use cloud storage (S3, Cloudinary, etc.)
   - Logs are written to stderr (configured via LOG_CHANNEL=stderr)

3. **Queue Workers**: Vercel doesn't support long-running queue workers. Consider:
   - Using external queue service (Redis Queue, AWS SQS)
   - Using Vercel Cron Jobs for scheduled tasks
   - Processing queues via API endpoints triggered by cron jobs

4. **Cold Starts**: Serverless functions may have cold start delays (1-3 seconds) on first request after inactivity

### Recommended Architecture

For production Laravel apps on Vercel:
- **Database**: External managed database (PlanetScale, Railway, etc.)
- **File Storage**: AWS S3, Cloudinary, or similar
- **Cache**: Redis (Upstash, Redis Cloud)
- **Queue**: Redis Queue or external queue service
- **Scheduled Tasks**: Vercel Cron Jobs or external scheduler

## Next Steps

1. Set up your external database
2. Configure cloud file storage
3. Set up Redis for caching/queues
4. Configure Vercel Cron Jobs for scheduled tasks
5. Set up monitoring and error tracking (Sentry, etc.)

## Resources

- [Vercel Documentation](https://vercel.com/docs)
- [Laravel Deployment Guide](https://laravel.com/docs/deployment)
- [Vercel Docker Support](https://vercel.com/docs/deployments/builds/build-with-docker)
