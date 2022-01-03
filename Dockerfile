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
# https://github.com/nodesource/distributions/blob/master/README.md#debmanual

RUN apt-get update
RUN apt-get install -y gnupg lsb-release

ENV KEYRING=/usr/share/keyrings/nodesource.gpg
RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource.gpg.key | gpg --dearmor | tee "$KEYRING" >/dev/null
RUN gpg --no-default-keyring --keyring "$KEYRING" --list-keys

ENV VERSION=node_13.x
ENV DISTRO="$(lsb_release -s -c)"
RUN echo "deb [signed-by=$KEYRING] https://deb.nodesource.com/$VERSION $DISTRO main" | tee /etc/apt/sources.list.d/nodesource.list
RUN echo "deb-src [signed-by=$KEYRING] https://deb.nodesource.com/$VERSION $DISTRO main" | tee -a /etc/apt/sources.list.d/nodesource.list

RUN rm /etc/apt/sources.list.d/nodesource.*
RUN apt-get update
RUN apt-get install -y nodejs npm

# Node.js global packages and dependencies

RUN apt-get install -y libnotify-bin
RUN npm install -g gulp-cli --loglevel verbose

# Node.js packages

COPY package.json /node/package.json
WORKDIR /node
RUN npm install --loglevel verbose
ENV NODE_PATH=/node/node_modules
ENV PATH=$NODE_PATH/.bin:$PATH

# Code

ADD . /code
WORKDIR /code

# PHP packages

RUN composer install

# Yii 2 initialization

RUN ./init --env=Development
