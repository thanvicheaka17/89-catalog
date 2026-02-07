FROM php:8.4-fpm

# -----------------------------
# System dependencies
# -----------------------------
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    ca-certificates \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libxml2-dev \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libsqlite3-dev \
    nginx \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        pdo_sqlite \
        mbstring \
        zip \
        bcmath \
        xml \
        intl \
        gd \
        pcntl \
        sockets \
    && rm -rf /var/lib/apt/lists/*

# -----------------------------
# Install Composer
# -----------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# -----------------------------
# Install Node.js (LTS)
# -----------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && node -v \
    && npm -v

# -----------------------------
# App setup
# -----------------------------
WORKDIR /var/www/html

COPY . .

# Install PHP dependencies
RUN if [ -f composer.json ]; then \
      composer install --no-dev --optimize-autoloader --no-interaction; \
    fi

# Build frontend assets
RUN if [ -f package.json ]; then \
      npm install && npm run build; \
    fi

# Fix permissions (CRITICAL for Laravel)
RUN chown -R www-data:www-data storage bootstrap/cache public/images \
    && chmod -R 775 storage bootstrap/cache public/images

# -----------------------------
# Configure Nginx
# -----------------------------
RUN rm -f /etc/nginx/sites-enabled/default
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# -----------------------------
# Configure Supervisor
# -----------------------------
RUN echo "[supervisord]" > /etc/supervisor/conf.d/supervisord.conf \
    && echo "nodaemon=true" >> /etc/supervisor/conf.d/supervisord.conf \
    && echo "" >> /etc/supervisor/conf.d/supervisord.conf \
    && echo "[program:php-fpm]" >> /etc/supervisor/conf.d/supervisord.conf \
    && echo "command=php-fpm" >> /etc/supervisor/conf.d/supervisord.conf \
    && echo "autostart=true" >> /etc/supervisor/conf.d/supervisord.conf \
    && echo "autorestart=true" >> /etc/supervisor/conf.d/supervisord.conf \
    && echo "" >> /etc/supervisor/conf.d/supervisord.conf \
    && echo "[program:nginx]" >> /etc/supervisor/conf.d/supervisord.conf \
    && echo "command=nginx -g 'daemon off;'" >> /etc/supervisor/conf.d/supervisord.conf \
    && echo "autostart=true" >> /etc/supervisor/conf.d/supervisord.conf \
    && echo "autorestart=true" >> /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
