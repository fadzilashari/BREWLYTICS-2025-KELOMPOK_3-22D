FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    default-mysql-client

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Create system user to run composer and artisan commands
# (Optional but recommended for consistency)
# RUN useradd -G www-data,root -u 1000 -d /home/dev dev
# RUN mkdir -p /home/dev/.composer && \
#    chown -R dev:dev /home/dev

# Pre-create Laravel directories and set permissions
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache && \
    chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port (Internal)
EXPOSE 9000

CMD ["php-fpm"]
