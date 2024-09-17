FROM php:8.3.7-apache

# Créer un fichier .env avec les variables d'environnement par défaut
COPY ./.env.prod /var/www/.env

# Configurer Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN chown -R www-data:www-data /var/www

# Installer les dépendances requises
RUN apt-get update \
    && apt-get install -qq -y --no-install-recommends \
        cron \
        vim \
        wget \
        locales \
        coreutils \
        apt-utils \
        git \
        libicu-dev \
        g++ \
        libpng-dev \
        libxml2-dev \
        libzip-dev \
        libonig-dev \
        libxslt-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configurer les locales
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen \
    && echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen \
    && locale-gen

# Installer Composer
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls \
    && mv composer.phar /usr/local/bin/composer

# Configurer les extensions PHP
RUN docker-php-ext-configure intl \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mysqli \
        gd \
        opcache \
        intl \
        zip \
        calendar \
        dom \
        mbstring \
        xsl \
    && a2enmod rewrite

# Installer APCu et autres extensions
RUN pecl install apcu \
    && docker-php-ext-enable apcu

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions amqp

# Installer Symfony CLI
RUN wget https://get.symfony.com/cli/installer -O - | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Configurer le répertoire de travail
WORKDIR /var/www

# Copier les fichiers de l'application
COPY ./bin ./bin
COPY ./config ./config
COPY ./public ./public
COPY ./src ./src
COPY ./migrations ./migrations
COPY ./composer.json ./composer.json
COPY ./composer.lock ./composer.lock
COPY ./symfony.lock ./symfony.lock
COPY ./launchApp.sh ./launchApp.sh
COPY ./reboot.sh ./reboot.sh

# Copier la configuration Apache
COPY ./docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/apache/.htaccess /var/www/public/.htaccess

# Activer le module rewrite
RUN a2enmod rewrite

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Assurer les droits d'exécution
RUN chmod +x launchApp.sh

# Changer les permissions (Apache doit avoir accès aux fichiers)
RUN chown -R www-data:www-data /var/www

# Configurer les permissions du répertoire var
RUN chown -R www-data:www-data /var/www/var && chmod -R 775 /var/www/var
RUN chmod -R 777 /var/www/var

EXPOSE 80

# Lancer l'application
CMD [ "sh", "./launchApp.sh" ]
