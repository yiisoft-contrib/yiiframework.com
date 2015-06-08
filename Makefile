# This Makefile is used to generate the yii2 and yii1 api documentation and guide
#
# run `make help` for a list of available targets. Run a target with `make <target>`.
#

help:
	@echo "the following targets are available:"
	@echo ""
	@echo " - docs         make all the docs"
	@echo " - guide        make only the guide docs"
	@echo " - guide-{v}    make only the guide docs for version {v} (1.0, 1.1, 2.0)"
	@echo " - api          make only the api docs"
	@echo " - api-{v}      make only the api docs for version {v} (1.0, 1.1, 2.0)"
	@echo " - download     make only the doc download archives"
	@echo " - download-{v} make only the doc download archives for version {v} (2.0)"

docs: api guide download

api: api-1.0 api-1.1 api-2.0
guide: guide-1.0 guide-1.1 guide-2.0
# TODO: blog tutorial
download: download-2.0

api-%: yii-%
	./yii api "$(subst api-,,$@)" --interactive=0

guide-%: yii-%
	./yii guide "$(subst guide-,,$@)" --interactive=0

download-%: TARGET_DIR=data/docs-offline
download-%: SOURCE_DIR=data/yii-$(subst download-,,$@)
download-%: DOC_DIR=yii-docs-$(subst download-,,$@)
download-%: yii-%
	-test -d ${TARGET_DIR} && rm -rf ${TARGET_DIR}
	vendor/bin/apidoc guide ${SOURCE_DIR}/docs/guide ${TARGET_DIR}/${DOC_DIR} --interactive=0
	vendor/bin/apidoc api ${SOURCE_DIR}/framework ${TARGET_DIR}/${DOC_DIR} --interactive=0
	vendor/bin/apidoc guide ${SOURCE_DIR}/docs/guide ${TARGET_DIR}/${DOC_DIR} --interactive=0
	rm -r ${TARGET_DIR}/${DOC_DIR}/cache
	cd ${TARGET_DIR} && tar czf ${DOC_DIR}.tar.gz ${DOC_DIR}
	cd ${TARGET_DIR} && tar cjf ${DOC_DIR}.tar.bz2 ${DOC_DIR}
	rm -r ${TARGET_DIR}/${DOC_DIR}

# targets for cloning yii repos for building docs
yii-1.0:
	test -d data/yii-1.0 || git clone git@github.com:yiisoft/yii.git data/yii-1.0
	cd data/yii-1.0 && git checkout 1.0.12 && git checkout master build/

yii-1.1: composer
	test -d data/yii-1.1 || git clone git@github.com:yiisoft/yii.git data/yii-1.1
	cd data/yii-1.1 && git pull
	cd data/yii-1.1 && (grep "phpunit/phpunit" composer.json > /dev/null || php ../composer.phar require --dev --prefer-dist "phpunit/phpunit:~3.7" "phpunit/phpunit-selenium:~1.4.0")

yii-2.0:
	test -d data/yii-2.0 || git clone git@github.com:yiisoft/yii2.git data/yii-2.0
	cd data/yii-2.0 && git pull

# the following targets are internal only

composer:
	cd data && (test -f composer.phar || (php -r "readfile('https://getcomposer.org/installer');" | php))

clean-es:
	./yii api/drop-elasticsearch-index --interactive=0

