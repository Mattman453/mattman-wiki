FROM php:8.5-fpm-alpine

ARG user
ARG uid

# Set working directory
WORKDIR /var/www

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install system dependencies
RUN apk update && apk add --no-cache \
    autoconf \
    curl \
    clang \
    git \
    gpg \
    libpng-dev \
    libxml2-dev \
    make \
    npm \ 
    oniguruma-dev \
    openssl-dev \
    sudo \
    unixodbc-dev \
    unzip \
    zip

# Install Yarn
RUN npm install --global yarn

# Install PIE && MongoDB setup
RUN --mount=type=bind,from=ghcr.io/php/pie:bin,source=/pie,target=/usr/local/bin/pie \
    export DEBIAN_FRONTEND="noninteractive"; \
    set -eux; \
    pie install --no-cache mongodb/mongodb-extension -vvv;

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

EXPOSE 9000
