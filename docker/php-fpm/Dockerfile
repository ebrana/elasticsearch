FROM php:8.3-fpm as elasticsearch-app
ARG TIMEZONE

RUN apt-get update -y && apt-get install -y \
    openssl \
    git \
    unzip

RUN apt-get install -y --no-install-recommends libssl-dev zlib1g-dev curl libxml2-dev libpq-dev libzip-dev \
    libxpm-dev libzip-dev zlib1g-dev libpng-dev libfreetype6-dev libjpeg62-turbo-dev libgd-dev libjpeg-dev && \
    pecl install apcu && \
    docker-php-ext-install -j$(nproc) zip opcache intl pdo pdo_mysql gd && \
    docker-php-ext-enable apcu && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN pecl install xdebug-3.3.0 && docker-php-ext-enable xdebug

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone
RUN printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini
RUN "date"

# Configure Xdebug
RUN echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /var/www/

CMD bash -c "composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev && php-fpm"
