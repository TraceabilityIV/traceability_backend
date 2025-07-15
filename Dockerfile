FROM php:8.2.28-fpm-alpine

# Instalar dependencias necesarias y compilación
RUN apk add --no-cache --update \
    bash \
    curl \
    git \
    unzip \
    libzip-dev \
    libbz2 \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    postgresql-dev \
    supervisor \
    build-base \
    autoconf \
    make \
    g++ \
    zlib-dev \
    curl-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        zip \
        bz2 \
        intl \
        xml \
        opcache \
    && apk del build-base autoconf make g++ \
    && rm -rf /var/cache/apk/* /tmp/*

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

RUN mkdir -p /var/log/supervisor

# Configurar PHP
RUN echo "upload_max_filesize = 20M" >> /usr/local/etc/php/php.ini && \
    echo "post_max_size = 20M" >> /usr/local/etc/php/php.ini && \
    echo "max_execution_time = 600" >> /usr/local/etc/php/php.ini && \
    echo "request_terminate_timeout = 600" >> /usr/local/etc/php/php.ini

ENV COMPOSER_ALLOW_SUPERUSER=1

# Copiar configuración de supervisor
COPY docker/supervisor /etc/supervisor

# Comando por defecto para arrancar supervisord
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]