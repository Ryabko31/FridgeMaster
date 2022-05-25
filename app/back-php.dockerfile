FROM php:8.1.1-fpm-alpine

RUN apk --update --no-cache add \
        supervisor freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev libmcrypt-dev libzip-dev libxml2-dev \
        && docker-php-ext-install -j$(nproc) iconv mysqli pdo_mysql zip \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
        && docker-php-ext-install -j$(nproc) gd

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"
