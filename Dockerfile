FROM node:20 AS node_builder
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install
COPY vite.config.js .
COPY resources resources
RUN npm run build

FROM php:8.3-apache
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libsqlite3-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring xml tokenizer xmlwriter zip intl pcntl bcmath gd \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
COPY --from=node_builder /app/public/build /var/www/html/public/build
EXPOSE 80
CMD ["apache2-foreground"]
