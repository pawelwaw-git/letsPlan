FROM php:8.1-apache

ENV COMPOSER_ALLOW_SUPERUSER 1

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    build-essential \
    curl \
    g++ \
    gifsicle \
    git \
    jpegoptim \
    libfontconfig1 \
    libfreetype6-dev \
    libicu-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libpq-dev \
    libxrender1 \
    libzip-dev \
    locales \
    optipng \
    pngquant \
    unzip \
    vim \
    zip \
    zlib1g-dev \
    libmagickwand-dev \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Configure php extensions
RUN docker-php-ext-configure intl &&  \
    docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg

# Install php extensions
RUN docker-php-ext-install \
    exif \
    gd \
    intl \
    pcntl \
    pdo \
    pdo_mysql \
    zip \
    bcmath

# Install XDebug \
RUN pecl install xdebug-3.2.1 && docker-php-ext-enable xdebug

RUN groupadd -g 1000 1000
RUN useradd -u 1000 -ms /bin/bash -g 1000 www

RUN chown -R www-data:www-data /var/www/html

#change document root in apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# enable apache modules
RUN a2enmod rewrite
RUN a2enmod ssl

USER www

WORKDIR /var/www/html