# PHP

FROM php:7.4-fpm

# PHP extensions
# https://github.com/mlocati/docker-php-extension-installer

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions gd intl pdo_mysql opcache zip

# Composer
# https://getcomposer.org/download/

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

# Node.js
# https://github.com/nvm-sh/nvm

RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh | bash
SHELL ["/bin/bash", "--login", "-c"]
RUN command -v nvm
RUN nvm install 13.14.0

# Node.js global packages and dependencies

RUN apt-get update
RUN apt-get install -y libnotify-bin
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
