

help:
	@echo "the following targets are available:"
	@echo ""
	@echo " - docs        make all the docs"
	@echo " - guide       make only the guide docs"
	@echo " - guide-{v}   make only the guide docs for version {v}"
	@echo " - api         make only the api docs"
	@echo " - api-{v}     make only the api docs for version {v}"

docs: api guide

api: api-1.0 api-1.1 api-2.0
guide: guide-1.0 guide-1.1 guide-2.0
# TODO: blog tutorial

api-%: yii-%
	./yii api "$(subst api-,,$@)" --interactive=0

guide-%: yii-%
	./yii guide "$(subst guide-,,$@)" --interactive=0


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

composer:
	cd data && (test -f composer.phar || (php -r "readfile('https://getcomposer.org/installer');" | php))
