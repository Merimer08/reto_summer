# ===== PHP + Apache =====
FROM php:8.3-apache

# ===== Extensiones necesarias =====
RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_sqlite sqlite3 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ===== Apache optimizado =====
RUN a2enmod rewrite headers expires

# ===== Config PHP (ligero para producciÃ³n) =====
RUN echo "memory_limit=256M" > /usr/local/etc/php/conf.d/custom.ini \
 && echo "upload_max_filesize=20M" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "post_max_size=20M" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/custom.ini

# ===== Data persistente =====
RUN mkdir -p /data \
 && chown -R www-data:www-data /data

# ===== Copiar proyecto =====
COPY . /var/www/html/

# ===== Permisos (IMPORTANTÃSIMO para sqlite/json) =====
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

# ===== DocumentRoot =====
WORKDIR /var/www/html

# ===== Puerto Render =====
EXPOSE 80
