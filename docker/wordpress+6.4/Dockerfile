FROM php:8.1-apache

COPY ./apache2.conf /etc/apache2/apache2.conf
COPY ./site.conf /etc/apache2/sites-enabled/000-default.conf

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    wget \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql zip intl exif \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN a2enmod rewrite headers proxy proxy_http cache cache_disk cache_socache

RUN ln -sf /dev/stdout /var/log/apache2/access.log \
    && ln -sf /dev/stderr /var/log/apache2/error.log

RUN mkdir /app
WORKDIR /app

RUN wget https://wordpress.org/latest.tar.gz \
    && tar -xzvf latest.tar.gz \
    && mv wordpress/* /app \
    && rm -rf latest.tar.gz wordpress

RUN chown -R www-data:www-data /app

EXPOSE 80

CMD ["apachectl","-D","FOREGROUND"]
