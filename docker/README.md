# Docker for eightynine-catalog

Usage:

Build and start containers:

```bash
docker-compose up -d --build
```

This will build the `app` image and start two containers:
- `89-catalog-app` (the PHP app) exposed on host port `8000`
- `89-catalog-db` (MySQL) exposed on host port `3306`

The `docker-compose.yml` reads DB credentials from the project `.env` file using these variables:
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

Notes:

- The `app` service mounts the project directory into `/var/www/html` so code changes are reflected immediately.
- For a production-ready setup, consider adding an Nginx container, adjusting PHP-FPM, and handling file permissions more strictly.
