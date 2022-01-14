# PHP

FROM php:7.4-fpm

# System packages

RUN apt-get update
RUN apt-get install -y texlive-full
RUN apt-get install -y python3-pygments
RUN apt-get install -y libnotify-bin
RUN apt-get install -y git
RUN apt-get install -y nginx
RUN apt-get install -y rsync

# PHP extensions
# https://github.com/mlocati/docker-php-extension-installer

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions gd intl pdo_mysql opcache zip

# Composer

COPY --from=composer:2.2.3 /usr/bin/composer /usr/local/bin/composer

# Node.js
# https://github.com/nvm-sh/nvm

RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh | bash
SHELL ["/bin/bash", "--login", "-c"]
RUN command -v nvm
RUN nvm install 13.14.0

# Node.js global packages

RUN npm install -g gulp-cli --loglevel=verbose
RUN npm install -g pageres-cli --loglevel=verbose --unsafe-perm=true

# PHP configuration

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN sed -i 's,^memory_limit =.*$,memory_limit = -1,' "$PHP_INI_DIR/php.ini"

ENV VENDOR_DIR=/code/vendor

# Nginx configuration

COPY nginx-site.conf /etc/nginx/sites-enabled/default

# PHP packages

COPY composer.* /code/
WORKDIR /code
RUN composer config vendor-dir $VENDOR_DIR
RUN composer install
RUN composer config vendor-dir vendor

# Node.js packages

COPY package.json /code
WORKDIR /code
RUN npm install --loglevel=verbose

# Code

ADD . /code/src
WORKDIR /code/src
