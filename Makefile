.DEFAULT_GOAL := help

CLI_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
$(eval $(CLI_ARGS):;@:)

deploy: ## Run commands after git pull for deployment on a server.
	composer --no-interaction --classmap-authoritative --no-dev install
	npm install
	gulp build --production
	./yii migrate
	./yii cache/flush-all

docs: api guide download ## Make all the docs.


api: api-1.1 api-2.0 api-3.0 ## Make only the api docs.
	./yii search/rebuild api

guide: guide-1.0 guide-1.1 guide-2.0 ## Make only the guide docs.
	./yii search/rebuild guide

download: download-2.0 ## Make only the doc download archives.

api-%: yii-% ## Make only the api docs for version {v} (1.0, 1.1, 2.0).
	./yii api "$(subst api-,,$@)" --interactive=0

guide-%: yii-% ## Make only the guide docs for version {v} (1.0, 1.1, 2.0).
	./yii guide "$(subst guide-,,$@)" --interactive=0
	@echo "PDF errors in the following logs:"
	@find data/$@/ | grep fail.log || echo " - no errors - "


download-%: TARGET_DIR=data/docs-offline ## download-{v} make only the doc download archives for version {v} (2.0).
download-%: SOURCE_DIR=data/yii-$(subst download-,,$@)
download-%: DOC_DIR=yii-docs-$(subst download-,,$@)
download-%: VENDOR_DIR=vendor
download-%: APIDOC_BIN=${VENDOR_DIR}/bin/apidoc
download-%: LANGUAGES=en $(shell find ${SOURCE_DIR}/docs/ | grep -ioP 'guide-[a-z-]+$$' | cut -c 7-)
download-%: yii-%
	cd ${SOURCE_DIR}/docs && ln -sf guide guide-en
	${APIDOC_BIN} api ${SOURCE_DIR}/framework,${SOURCE_DIR}/extensions ${TARGET_DIR}/${DOC_DIR} --interactive=0
	for l in $(shell echo "${LANGUAGES}" | xargs -n1 | sort -u | xargs) ; do \
		echo ""  ; \
		echo "building guide and api package for language $$l..."  ; \
		test -d ${TARGET_DIR}/${DOC_DIR}-$$l && rm -rf ${TARGET_DIR}/${DOC_DIR}-$$l  ; \
		test -f ${TARGET_DIR}/${DOC_DIR}-$$l.tar.gz && rm ${TARGET_DIR}/${DOC_DIR}-$$l.tar.gz  ; \
		test -f ${TARGET_DIR}/${DOC_DIR}-$$l.tar.bz2 && rm ${TARGET_DIR}/${DOC_DIR}-$$l.tar.bz2  ; \
		cp -ar ${TARGET_DIR}/${DOC_DIR} ${TARGET_DIR}/${DOC_DIR}-$$l  ; \
		${APIDOC_BIN} guide ${SOURCE_DIR}/docs/guide-$$l ${TARGET_DIR}/${DOC_DIR}-$$l --interactive=0  ; \
		${APIDOC_BIN} api ${SOURCE_DIR}/framework,${SOURCE_DIR}/extensions ${TARGET_DIR}/${DOC_DIR}-$$l --interactive=0 ; \
		rm -r ${TARGET_DIR}/${DOC_DIR}-$$l/cache  ; \
		cd ${TARGET_DIR} && tar czf ${DOC_DIR}-$$l.tar.gz ${DOC_DIR}-$$l ; cd - ; \
		cd ${TARGET_DIR} && tar cjf ${DOC_DIR}-$$l.tar.bz2 ${DOC_DIR}-$$l ; cd - ; \
		rm -r ${TARGET_DIR}/${DOC_DIR}-$$l  ; \
	done


# Targets for cloning yii repos for building docs.
yii-1.0: composer
	test -d data/yii-1.0 || git clone https://github.com/yiisoft/yii.git data/yii-1.0
	cd data/yii-1.0 && git checkout 1.0.12 && git checkout master build/
	cd data/yii-1.0 && COMPOSER=../../composer.yii-1.0.json php ../composer.phar --no-interaction install

yii-1.1: composer
	test -d data/yii-1.1 || git clone https://github.com/yiisoft/yii.git data/yii-1.1
	cd data/yii-1.1 && git pull
	cd data/yii-1.1 && php ../composer.phar require --dev --prefer-dist --no-interaction "phpunit/phpunit:4.8.34" "phpunit/phpunit-selenium:~1.4.0" "pear/archive_tar:~1.5.0"

yii-2.0: yii-2.0-git \
    yii-2.0-ext-apidoc \
    yii-2.0-ext-authclient \
    yii-2.0-ext-bootstrap \
    yii-2.0-ext-debug \
    yii-2.0-ext-elasticsearch \
    yii-2.0-ext-faker \
    yii-2.0-ext-gii \
    yii-2.0-ext-httpclient \
    yii-2.0-ext-imagine \
    yii-2.0-ext-jui \
    yii-2.0-ext-mongodb \
    yii-2.0-ext-redis \
    yii-2.0-ext-shell \
    yii-2.0-ext-smarty \
    yii-2.0-ext-sphinx \
    yii-2.0-ext-swiftmailer \
    yii-2.0-ext-twig

yii-3.0: yii-3.0-access \
  yii-3.0-active-record \
  yii-3.0-aliases \
  yii-3.0-arrays \
  yii-3.0-assets \
  yii-3.0-auth \
  yii-3.0-auth-jwt \
  yii-3.0-bootstrap5 \
  yii-3.0-cache \
  yii-3.0-cache-apcu \
  yii-3.0-cache-db \
  yii-3.0-cache-file \
  yii-3.0-cache-memcached \
  yii-3.0-cache-redis \
  yii-3.0-cache-wincache \
  yii-3.0-classifier \
  yii-3.0-config \
  yii-3.0-code-style \
  yii-3.0-container-proxy \
  yii-3.0-cookies \
  yii-3.0-csrf \
  yii-3.0-data \
  yii-3.0-data-cycle \
  yii-3.0-data-db \
  yii-3.0-data-response \
  yii-3.0-db \
  yii-3.0-db-elasticsearch \
  yii-3.0-db-migration \
  yii-3.0-db-mongodb \
  yii-3.0-db-mssql \
  yii-3.0-db-mysql \
  yii-3.0-db-oracle \
  yii-3.0-db-pgsql \
  yii-3.0-db-redis \
  yii-3.0-db-sphinx \
  yii-3.0-db-sqlite \
  yii-3.0-definitions \
  yii-3.0-di \
  yii-3.0-error-handler \
  yii-3.0-event-dispatcher \
  yii-3.0-factory \
  yii-3.0-file-router \
  yii-3.0-files \
  yii-3.0-form \
  yii-3.0-form-model \
  yii-3.0-friendly-exception \
  yii-3.0-html \
  yii-3.0-http \
  yii-3.0-http-middleware \
  yii-3.0-hydrator \
  yii-3.0-hydrator-validator \
  yii-3.0-i18n \
  yii-3.0-injector \
  yii-3.0-input-http \
  yii-3.0-json \
  yii-3.0-log \
  yii-3.0-log-target-db \
  yii-3.0-log-target-email \
  yii-3.0-log-target-file \
  yii-3.0-log-target-syslog \
  yii-3.0-mailer \
  yii-3.0-mailer-swiftmailer \
  yii-3.0-mailer-symfony \
  yii-3.0-mailer-view \
  yii-3.0-middleware-dispatcher \
  yii-3.0-mutex \
  yii-3.0-mutex-file \
  yii-3.0-mutex-pdo-mysql \
  yii-3.0-mutex-pdo-oracle \
  yii-3.0-mutex-pdo-pgsql \
  yii-3.0-mutex-redis \
  yii-3.0-network-utilities \
  yii-3.0-profiler \
  yii-3.0-proxy \
  yii-3.0-proxy-middleware \
  yii-3.0-psr-emitter \
  yii-3.0-queue \
  yii-3.0-queue-amqp \
  yii-3.0-queue-db \
  yii-3.0-queue-redis \
  yii-3.0-rate-limiter \
  yii-3.0-rbac \
  yii-3.0-rbac-cycle-db \
  yii-3.0-rbac-db \
  yii-3.0-rbac-php \
  yii-3.0-rbac-rules-container \
  yii-3.0-request-body-parser \
  yii-3.0-request-model \
  yii-3.0-request-provider \
  yii-3.0-requirements \
  yii-3.0-response-download \
  yii-3.0-router \
  yii-3.0-router-fastroute \
  yii-3.0-security \
  yii-3.0-session \
  yii-3.0-strings \
  yii-3.0-test-support \
  yii-3.0-translator \
  yii-3.0-translator-extractor \
  yii-3.0-translator-formatter-intl \
  yii-3.0-translator-message-db \
  yii-3.0-translator-message-gettext \
  yii-3.0-translator-message-php \
  yii-3.0-user \
  yii-3.0-validator \
  yii-3.0-var-dumper \
  yii-3.0-view \
  yii-3.0-view-twig \
  yii-3.0-widget \
  yii-3.0-yii-auth-client \
  yii-3.0-yii-bulma \
  yii-3.0-yii-console \
  yii-3.0-yii-cycle \
  yii-3.0-yii-dataview \
  yii-3.0-yii-debug \
  yii-3.0-yii-debug-api \
  yii-3.0-yii-debug-viewer \
  yii-3.0-yii-dev-tool \
  yii-3.0-yii-event \
  yii-3.0-yii-gii \
  yii-3.0-yii-http \
  yii-3.0-yii-middleware \
  yii-3.0-yii-runner \
  yii-3.0-yii-runner-console \
  yii-3.0-yii-runner-frankenphp \
  yii-3.0-yii-runner-http \
  yii-3.0-yii-runner-roadrunner \
  yii-3.0-yii-sentry \
  yii-3.0-yii-swagger \
  yii-3.0-yii-testing \
  yii-3.0-yii-twig \
  yii-3.0-yii-view-renderer \
  yii-3.0-yii-widgets

yii-2.0-git:
	test -d data/yii-2.0 || git clone https://github.com/yiisoft/yii2.git data/yii-2.0
	cd data/yii-2.0 && git pull

yii-2.0-ext-%:
	test -d data/yii-2.0/extensions/$(subst yii-2.0-ext-,,$@) || git clone https://github.com/yiisoft/yii2-$(subst yii-2.0-ext-,,$@).git data/yii-2.0/extensions/$(subst yii-2.0-ext-,,$@)
	cd data/yii-2.0/extensions/$(subst yii-2.0-ext-,,$@) && git pull

yii-3.0-%:
	test -d data/yii-3.0/$(subst yii-3.0-,,$@) || git clone https://github.com/yiisoft/$(subst yii-3.0-,,$@).git data/yii-3.0/$(subst yii-3.0-,,$@)
	cd data/yii-3.0/$(subst yii-3.0-,,$@) && git pull

composer:
	cd data && (test -f composer.phar || (php -r "readfile('https://getcomposer.org/installer');" | php))

build: ## Build docker images.
	docker compose build $(CLI_ARGS)

up:  ## Up the dev environment.
	docker compose up -d --remove-orphans

down: ## Down the dev environment.
	docker compose down --remove-orphans

clear: ## Remove development docker containers and volumes.
	docker compose down --volumes --remove-orphans

shell: ## Get into container shell.
	docker compose exec web /bin/bash

yii: ## Execute Yii command.
	docker compose run --rm web ./yii $(CLI_ARGS)
.PHONY: yii

composer-docker: ## Run Composer.
	docker compose run --rm web composer $(CLI_ARGS)

codecept: ## Run Codeception.
	docker compose run --rm web ./vendor/bin/codecept $(CLI_ARGS)

# Output the help for each task, see https://marmelab.com/blog/2016/02/29/auto-documented-makefile.html
help: ## This help.
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)
