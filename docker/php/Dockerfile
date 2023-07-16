FROM php:8.2-apache

RUN apt-get update && \
    apt-get install -y \
    libzip-dev \
    zip

RUN a2enmod rewrite

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN pecl install xdebug
RUN docker-php-ext-install pdo_mysql zip opcache && docker-php-ext-enable pdo_mysql zip opcache
COPY conf.d /usr/local/etc/php/conf.d

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 볼륨 마운팅후 composer install 필요. docker exec -it 컨테이너이름 composer install
