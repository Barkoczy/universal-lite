FROM php:8.1-fpm

# Maintainer
LABEL Henrich Barkoczy <me@barkoczy.social>

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \ 
    build-essential \
    locales \
    autoconf \
    pkg-config \
    libzip-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    libonig-dev \
    libxml2-dev \
    libmcrypt-dev \
    libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    jpegoptim optipng pngquant gifsicle \
    git \
    curl \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install bcmath mbstring intl curl zip gd exif pcntl pdo_mysql json mcrypt xml

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

USER $user