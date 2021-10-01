SHELL := /bin/bash

COMPOSER = composer
EXEC_PHP = php

SYMFONY = $(EXEC_PHP) bin/console

php-fix:
	php-cs-fixer fix ${CURDIR}/src --rules=@Symfony

install:
	$(COMPOSER) install --no-progress --prefer-dist --optimize-autoloader

load-fixtures: export APP_ENV=test
load-fixtures:
	$(SYMFONY) doctrine:cache:clear-metadata
	$(SYMFONY) doctrine:database:create --if-not-exists
	$(SYMFONY) doctrine:schema:drop --force
	$(SYMFONY) doctrine:schema:create
	$(SYMFONY) doctrine:schema:validate
	$(SYMFONY) doctrine:fixtures:load -n

coverage:
	#unset XDEBUG_MODE
	#export XDEBUG_MODE=coverage
	XDEBUG_MODE=coverage symfony php bin/phpunit  --coverage-html coverage
.PHONY: tests help coverage
