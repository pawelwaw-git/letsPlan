FROM php:8.1-apache

ENV COMPOSER_ALLOW_SUPERUSER 1

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install wget
RUN apt-get update && apt-get install -y wget unzip

# Get Chrome
RUN apt-get install -y wget
RUN wget -q https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
RUN apt-get install -y ./google-chrome-stable_current_amd64.deb

# Install Chrome WebDriver
# Chrome Driver

RUN wget https://edgedl.me.gvt1.com/edgedl/chrome/chrome-for-testing/121.0.6167.85/linux64/chromedriver-linux64.zip
RUN unzip chromedriver-linux64.zip \
    && mv chromedriver-linux64/chromedriver /usr/bin/chromedriver \
    && chmod +x /usr/bin/chromedriver

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
    npm \
    sudo

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

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | sudo -E bash
RUN sudo apt install symfony-cli

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


# FROM https://gitlab.com/behat-chrome/docker-chrome-headless/-/blob/main/Dockerfile?ref_type=heads
RUN useradd headless --shell /bin/bash --create-home \
  && usermod -a -G sudo headless \
  && echo 'ALL ALL = (ALL) NOPASSWD: ALL' >> /etc/sudoers \
  && echo 'headless:nopassword' | chpasswd

# Copy configurations
COPY memory-limit-php.ini /usr/local/etc/php/conf.d/memory-limit-php.ini

USER www

WORKDIR /var/www/html

CMD bash -c "composer install"
CMD bash -c "npm install"
CMD bash -c "run dev"
CMD bash -c "cp behat.yml.dist behat.yml"
CMD bash -c "php bin/console doctrine:schema:create --env=test"
