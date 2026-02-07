# Deployment Guide: Local Machine to Vercel

This guide explains how to deploy your Laravel application from your local machine to Vercel.

## Method 1: GitHub Integration (Recommended) 🔄

This is the **easiest and most common** method. Vercel automatically deploys whenever you push to GitHub.

### Setup (One-time)

1. **Connect GitHub Repository to Vercel:**
   - Go to [Vercel Dashboard](https://vercel.com/dashboard)
   - Click **"Add New Project"**
   - Select your GitHub repository: `thanvicheaka17/89-catalog`
   - Click **"Import"**

2. **Configure Environment Variables:**
   - In Vercel project settings → **Environment Variables**
   - Add all your Laravel `.env` variables (APP_KEY, DB credentials, etc.)

### Deploy (Every time you make changes)

```bash
# 1. Make your code changes locally
# ... edit files ...

# 2. Stage your changes
git add .

# 3. Commit your changes
git commit -m "Your commit message"

# 4. Push to GitHub
git push origin main

# 5. Vercel automatically detects the push and deploys!
# Check your Vercel dashboard to see the deployment progress
```

**That's it!** Vercel will automatically:
- Detect the push to GitHub
- Start a new deployment
- Build your application
- Deploy to production

---

## Method 2: Vercel CLI (Direct Deployment) 🚀

Deploy directly from your local machine without pushing to GitHub first.

### Setup (One-time)

1. **Install Vercel CLI:**
   ```bash
   npm install -g vercel
   # or
   yarn global add vercel
   ```

2. **Login to Vercel:**
   ```bash
   vercel login
   ```
   This will open your browser to authenticate.

3. **Link Your Project (if not already linked):**
   ```bash
   cd "/media/garry/Data/vicheaka/backend/eightynine-catalog (Copy)"
   vercel link
   ```
   
   You'll be prompted to:
   - Select your Vercel account/team
   - Link to existing project or create new one
   - Your project is already linked (see `.vercel/project.json`)

### Deploy (Every time you make changes)

#### Deploy to Preview Environment:
```bash
# Deploy to a preview URL (for testing)
vercel
```

#### Deploy to Production:
```bash
# Deploy directly to production
vercel --prod
```

#### Deploy with Environment Variables:
```bash
# Set environment variables during deployment
vercel --prod --env APP_ENV=production --env APP_DEBUG=false
```

#### Deploy Specific Files/Directories:
```bash
# Deploy only specific files
vercel --prod --cwd /path/to/project
```

### Useful Vercel CLI Commands

```bash
# Check deployment status
vercel ls

# View deployment logs
vercel logs [deployment-url]

# Inspect a deployment
vercel inspect [deployment-url]

# Remove a deployment
vercel remove [deployment-url]

# View project info
vercel project ls

# View environment variables
vercel env ls

# Add environment variable
vercel env add VARIABLE_NAME production

# Pull environment variables to local .env
vercel env pull .env.local
```

---

## Comparison: GitHub vs CLI

| Feature | GitHub Integration | Vercel CLI |
|---------|-------------------|------------|
| **Ease of Use** | ⭐⭐⭐⭐⭐ Very Easy | ⭐⭐⭐⭐ Easy |
| **Automatic Deploy** | ✅ Yes, on every push | ❌ Manual command |
| **Preview URLs** | ✅ Automatic for PRs | ✅ Manual with `vercel` |
| **Production Deploy** | ✅ Automatic on main branch | ✅ Manual with `vercel --prod` |
| **No Git Required** | ❌ Requires Git push | ✅ Can deploy without Git |
| **CI/CD Integration** | ✅ Built-in | ⚠️ Manual setup needed |

**Recommendation:** Use **GitHub Integration** for regular development workflow. Use **Vercel CLI** for quick testing or when you don't want to commit to Git.

---

## Quick Reference

### Typical Workflow (GitHub Integration)

```bash
# 1. Make changes
# 2. Commit
git add .
git commit -m "Update feature"

# 3. Push (triggers automatic Vercel deployment)
git push origin main

# 4. Check deployment status
# Visit: https://vercel.com/dashboard
```

### Typical Workflow (CLI)

```bash
# 1. Make changes
# 2. Deploy to preview
vercel

# 3. Test preview URL
# 4. Deploy to production
vercel --prod
```

---

## Troubleshooting

### GitHub Integration Not Working?

1. **Check GitHub Connection:**
   - Vercel Dashboard → Settings → Git
   - Ensure repository is connected

2. **Check Branch Settings:**
   - Vercel Dashboard → Settings → Git
   - Ensure `main` branch is set for production deployments

3. **Check Build Logs:**
   - Vercel Dashboard → Deployments
   - Click on failed deployment → View Build Logs

### CLI Not Working?

1. **Check Authentication:**
   ```bash
   vercel whoami
   ```
   If not logged in, run `vercel login`

2. **Check Project Link:**
   ```bash
   vercel link
   ```
   Re-link if needed

3. **Check Build Command:**
   - Ensure `vercel.json` has correct `buildCommand`
   - Check if dependencies are installed locally

### Common Issues

**Issue:** Build fails with "Missing dependencies"
```bash
# Solution: Install dependencies locally first
composer install
npm install
```

**Issue:** Environment variables not set
```bash
# Solution: Set in Vercel dashboard or via CLI
vercel env add APP_KEY production
```

**Issue:** Deployment timeout
- Check build logs for slow operations
- Consider optimizing build command
- Check if database migrations are blocking

---

## Best Practices

1. ✅ **Always test locally first** before deploying
2. ✅ **Use preview deployments** (`vercel` without `--prod`) for testing
3. ✅ **Set up environment variables** in Vercel dashboard (not in code)
4. ✅ **Monitor deployment logs** for errors
5. ✅ **Use Git branches** - Vercel creates preview URLs for each branch/PR
6. ✅ **Keep `.vercelignore` updated** to exclude unnecessary files
7. ✅ **Use production environment variables** only for production deployments

---

## Your Current Setup

✅ **Project is already linked to Vercel**
- Project ID: `prj_biUmwDt7Hv0zW2Z3ceTsC8KgM321`
- Project Name: `89-catalog`
- Configuration: `.vercel/project.json`

✅ **GitHub Repository Connected**
- Repository: `thanvicheaka17/89-catalog`
- Branch: `main`

**Recommended Workflow:**
```bash
# Make changes → Commit → Push → Auto-deploy! 🚀
git add .
git commit -m "Your changes"
git push origin main
```

---

## Need Help?

- [Vercel Documentation](https://vercel.com/docs)
- [Vercel CLI Reference](https://vercel.com/docs/cli)
- [Laravel on Vercel Guide](./VERCEL_DEPLOYMENT.md)
