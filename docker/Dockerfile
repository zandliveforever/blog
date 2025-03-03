FROM php:8.1-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    nano \
    git \
    curl \
    libpng-dev \
    zip \
    unzip

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mysqli

RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

COPY --chown=www:www . /var/www

USER www

EXPOSE 9000
