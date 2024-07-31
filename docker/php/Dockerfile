FROM php:8.3.7-apache

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN apt-get update \
    && apt-get install -qq -y --no-install-recommends \
    cron \
     vim \
     wget \
     locales coreutils apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev;

RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql mysqli gd opcache intl zip calendar dom mbstring zip gd xsl && a2enmod rewrite
RUN pecl install apcu && docker-php-ext-enable apcu
    
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions amqp

RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

WORKDIR /var/www
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY ./bin ./bin
COPY ./config ./config
COPY ./public ./public
COPY ./src ./src
COPY migrations ./migrations
COPY vendor ./vendor
COPY composer.json .
COPY composer.phar .
COPY composer.lock .
COPY symfony.lock .
COPY launchApp.sh .
COPY reboot.sh .
RUN chmod +x launchApp.sh
RUN touch .env

CMD [ "sh","./launchApp.sh" ]