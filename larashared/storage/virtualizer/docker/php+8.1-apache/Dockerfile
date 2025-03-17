FROM php:8.1-apache

COPY ./resolve.conf /etc/resolv.conf
COPY ./sources.list /etc/apt/sources.list
COPY ./apache2.conf /etc/apache2/apache2.conf
COPY ./site.conf /etc/apache2/sites-enabled/000-default.conf

# RUN apt update
# RUN apt install -y \
#     libpng-dev \
#     libjpeg-dev \
#     libfreetype6-dev \
#     libmagickwand-dev \
#     libzip-dev \
#     libicu-dev \
#     && docker-php-ext-configure gd --with-freetype --with-jpeg \
#     && docker-php-ext-install gd \
#     && apt-get install -y --no-install-recommends git autoconf pkg-config libssl-dev \
#     && pecl install imagick \
#     && docker-php-ext-enable imagick \
#     && docker-php-ext-install mysqli pdo pdo_mysql zip intl exif \
#     && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN a2enmod rewrite headers proxy proxy_http cache cache_disk cache_socache

RUN ln -sf /dev/stdout /var/log/apache2/access.log \
    && ln -sf /dev/stderr /var/log/apache2/error.log

RUN mkdir /app
WORKDIR /app

EXPOSE 80

CMD ["apachectl","-D","FOREGROUND"]