FROM php:8.2-fpm-alpine

# ── Build tools (required by PECL to compile extensions) ──────────────────────
RUN apk add --no-cache \
    autoconf \
    g++ \
    make \
    linux-headers

# ── System dependencies ────────────────────────────────────────────────────────
RUN apk add --no-cache \
    bash \
    curl \
    git \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    mariadb-client \
    nodejs \
    npm

# ── PHP extensions (compiled) ──────────────────────────────────────────────────
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# ── Redis extension via PECL ───────────────────────────────────────────────────
# Build tools must be installed before this step
RUN pecl install redis-6.0.2 \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/pear

# ── Clean up build tools to keep image small ──────────────────────────────────
RUN apk del autoconf g++ make linux-headers

# ── PHP config ─────────────────────────────────────────────────────────────────
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini

# ── Composer ───────────────────────────────────────────────────────────────────
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# ── Working directory ──────────────────────────────────────────────────────────
WORKDIR /var/www/html

# ── Entrypoint ─────────────────────────────────────────────────────────────────
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
