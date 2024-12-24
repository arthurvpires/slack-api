FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
	git \
	curl \
	libpng-dev \
	libonig-dev \
	libxml2-dev \
	zip \
	unzip

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN useradd -G www-data,root -u 1000 -d /home/appuser appuser
RUN mkdir -p /home/appuser/.composer && \
	chown -R appuser:appuser /home/appuser

RUN pecl install -o -f redis \
	&& rm -rf /tmp/pear \
	&& docker-php-ext-enable redis

WORKDIR /var/www

EXPOSE 9000

COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

USER appuser