FROM php:8.2-cli

# System and PHP extensions required for Laravel + MySQL
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    && docker-php-ext-install pdo_mysql bcmath \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Node.js (for Vite build)
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Copy full source before composer scripts that require artisan
COPY . .

# Install dependencies and build frontend assets
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
RUN npm ci && npm run build && npm prune --omit=dev

# Ensure runtime writable paths
RUN chown -R www-data:www-data storage bootstrap/cache

COPY start.sh /app/start.sh
RUN chmod +x /app/start.sh

EXPOSE 10000

CMD ["/app/start.sh"]
