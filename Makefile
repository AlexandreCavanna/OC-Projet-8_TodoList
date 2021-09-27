SHELL := /bin/bash


php-fix:
	php-cs-fixer fix ${CURDIR}/src --rules=@Symfony

tests: export APP_ENV=test
tests:
	symfony console doctrine:database:drop --force || true
	symfony console doctrine:database:create
	symfony console doctrine:schema:create
	symfony console doctrine:fixtures:load -n
	symfony php bin/phpunit

coverage:
	#unset XDEBUG_MODE
	#export XDEBUG_MODE=coverage
	XDEBUG_MODE=coverage symfony php bin/phpunit  --coverage-html coverage
.PHONY: tests help coverage
