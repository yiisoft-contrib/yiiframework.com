# PHP

FROM php:7.4-fpm

# System packages and dependencies

RUN apt-get update
RUN apt-get install -y texlive-full
RUN apt-get install -y python3-pygments
RUN apt-get install -y libnotify-bin

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

RUN npm install -g gulp-cli --loglevel verbose

# PHP packages

ENV COMPOSER_VENDOR_DIR=/code/vendor
COPY composer.* /code/
WORKDIR /code
RUN composer install

# Node.js packages

COPY package.json /code
WORKDIR /code
RUN npm install --loglevel verbose

# Code

ADD . /code/src
WORKDIR /code/src
