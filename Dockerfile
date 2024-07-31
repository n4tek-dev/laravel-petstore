# Dockerfile

FROM php:8.2-fpm

# Instalacja zależności systemowych
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libzip-dev

# Instalacja rozszerzeń PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalacja Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Ustawienie katalogu roboczego
WORKDIR /var/www

# Kopiowanie plików composer.json i composer.lock
COPY composer.json composer.lock ./

# Instalacja zależności PHP
RUN composer install --no-scripts --no-autoloader

# Kopiowanie pozostałych plików aplikacji do kontenera
COPY . .

# Generowanie autoload
RUN composer dump-autoload --optimize

# Kopiowanie pliku konfiguracyjnego Nginx
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf

# Ustawienie uprawnień do katalogu storage i bootstrap/cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
