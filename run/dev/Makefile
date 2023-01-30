# Setup ————————————————————————————————————————————————————————————————————————
SUPPORTED_COMMANDS := update
SUPPORTS_MAKE_ARGS := $(findstring $(firstword $(MAKECMDGOALS)), $(SUPPORTED_COMMANDS))
ifneq "$(SUPPORTS_MAKE_ARGS)" ""
  COMMAND_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
  $(eval $(COMMAND_ARGS):;@:)
endif

EXEC_DOCKER_PHP_ROOT = docker-compose exec php
EXEC_DOCKER_PHP = docker-compose exec -u web php
EXEC_DOCKER_REDIS = docker-compose exec redis
EXEC_DOCKER_API = docker-compose exec api
SYMFONY = $(EXEC_DOCKER_PHP) bin/console
COMPOSER = $(EXEC_DOCKER_PHP) composer
OS := $(shell uname -s)
.DEFAULT_GOAL := help

help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Git ——————————————————————————————————————————————————————————————————————
g-hook:
	git config core.hooksPath .githooks

## —— Composer —————————————————————————————————————————————————————————————————
c-install: ## Install vendors according to the current composer.lock file
	$(COMPOSER) install

## —— Workflow —————————————————————————————————————————————————————————————————
workflow-s: ## Generate schema workflow
	$(shell $(EXEC_DOCKER_PHP) bash -c 'for a in `ls config/workflows | cut -d\. -f1`; do php bin/console workflow:dump $$a | dot -Tpng -o workflows/$$a.png; done')

## —— Symfony ——————————————————————————————————————————————————————————————————
sf: ## List Symfony commands
	$(SYMFONY)

cc: ## Clear cache
	$(SYMFONY) c:c

## —— Doctrine —————————————————————————————————————————————————————————————————
migrate: ## launch migration
	$(SYMFONY) doctrine:migrations:migrate

diff: ## generate diff migration according to current model
	$(SYMFONY) doctrine:migration:diff

filo: ## play all fixtures
	$(SYMFONY) doctrine:fixture:load --append

fixtures: ## Load fixtures. You can add the option env to specify the environment (default dev) : env=test
	$(eval env := dev)
	$(SYMFONY) doctrine:schema:drop --full-database --force -n --env=$(env)
	$(SYMFONY) doctrine:migration:migrate -n --env=$(env)
	$(SYMFONY) hautelook:fixtures:load -n --env=$(env)

reset-db: ## Reset databases
	$(SYMFONY) doctrine:schema:drop --full-database --force -n
	$(SYMFONY) doctrine:migration:migrate -n
	$(SYMFONY) hautelook:fixtures:load -n
	$(SYMFONY) doctrine:schema:drop --full-database --force -n --env=test
	$(SYMFONY) doctrine:migration:migrate -n --env=test
	$(SYMFONY) hautelook:fixtures:load -n --env=test

clean-db: ## Reset databases without fixtures
	$(SYMFONY) doctrine:schema:drop --full-database --force -n
	$(SYMFONY) doctrine:migration:migrate -n
migration:
	$(SYMFONY) doctrine:schema:drop --full-database --force -n
	$(SYMFONY) doctrine:migration:migrate -n
	$(SYMFONY) make:migration
	$(SYMFONY) doctrine:migration:migrate -n
	$(SYMFONY) hautelook:fixtures:load -n

## —— Docker ———————————————————————————————————————————————————————————————————
build: g-hook
	USER_ID=$(shell id -u) GROUP_ID=$(shell id -g) docker-compose up --build -d

app: ## Enter in container php
	$(EXEC_DOCKER_PHP) bash

redis: ## Enter in container php
	$(EXEC_DOCKER_REDIS) sh

app-r: ## Enter in container php as root
	$(EXEC_DOCKER_PHP_ROOT) bash

up: ## start container
	docker-compose up -d

stop: ## stop container
	docker-compose stop

restart: ## restart containers
	docker-compose stop
	docker-compose up -d

down: ## destroy container
	docker-compose down

## —— Test, Code quality -T ————————————————————————————————————————————————————
test: ## Start tests and coverage. You can add the option filter to start specific test. Default all tests start : filter=testMyFunction
	$(SYMFONY) c:c --env=test
	$(MAKE) fixtures env=test
	$(eval filter ?= $(filter)) if [ -z "$(filter)" ] ; then $(EXEC_DOCKER_PHP) ./vendor/bin/phpunit --testdox --coverage-html coverage ; else $(EXEC_DOCKER_PHP) ./vendor/bin/phpunit --testdox --filter $(filter) --coverage-html coverage ; fi

test-unit: ## Start unit tests and coverage. You can add the option filter to start specific test. Default all tests start : filter=testMyFunction
	$(eval filter ?= $(filter)) if [ -z "$(filter)" ] ; then $(EXEC_DOCKER_PHP) ./vendor/bin/phpunit --testsuite='Unit' --testdox --coverage-html coverage ; else $(EXEC_DOCKER_PHP) ./vendor/bin/phpunit --testsuite='Unit' --testdox --coverage-html coverage --filter $(filter) ; fi

testall: ## Start all tests
	$(MAKE) fixtures env=test
	$(EXEC_DOCKER_PHP) ./vendor/bin/phpunit --testdox

phpmd: ## Start phpmd
	$(eval format := ansi)
	$(EXEC_DOCKER_PHP) ./vendor/bin/phpmd src $(format) phpmd.xml

phpstan: ## Start a PHP Static Analysis Tool
	$(EXEC_DOCKER_PHP) ./vendor/bin/phpstan analyse

php-cs-fixer: ## Start PHP Coding Standards Fixer"
	$(EXEC_DOCKER_PHP) php-cs-fixer fix --rules=no_unused_imports src

quality: php-cs-fixer phpmd phpstan test ## Start php-cs-fixer phpstan and tests 

## —— RabbitMQ/Messenger ——————————————————————————————————————————————————————————————————
worker: ## Display the supervisor workers status for rabbitMQ
	$(EXEC_DOCKER_PHP) supervisorctl status

r-notifier: ## Restart notifier supervisor worker for rabbitMQ
	$(EXEC_DOCKER_PHP) supervisorctl restart notification-symfony

notifier-err: ## Display log of cvcalculator supervisor worker for rabbitMQ
	$(EXEC_DOCKER_PHP) supervisorctl tail -f notification-symfony stderr

r-mailer: ## Restart mailer supervisor worker for rabbitMQ
	$(EXEC_DOCKER_PHP) supervisorctl restart mail-symfony

r-multicaster: ## Restart mailer supervisor worker for rabbitMQ
	$(EXEC_DOCKER_PHP) supervisorctl restart multicaster-symfony

r-cv: ## Restart mailer supervisor worker for rabbitMQ
	$(EXEC_DOCKER_PHP) supervisorctl restart cvcalculation-symfony

cv-err: ## Display log of cvcalculator supervisor worker for rabbitMQ
	$(EXEC_DOCKER_PHP) supervisorctl tail -f cvcalculation-symfony stderr

cv-out: ## Display log of cvcalculator supervisor worker for rabbitMQ
	$(EXEC_DOCKER_PHP) supervisorctl tail -f cvcalculation-symfony stdout

## —— Browser ——————————————————————————————————————————————————————————————————
open-coverage: ## Open coverage tool in browser
	@test -f ../../src/coverage/index.html || (echo -e "Unable to find code coverage report. You should generate it with \033[36mmake xdebug-on phpunit coverage=true\033[0m" ; exit 1)
	@xdg-open ../../src/coverage/index.html 2> /dev/null

open-adminer: ## Open adminer tool in browser
	@xdg-open "http://127.0.0.1:8080/?pgsql=183.67.130.2&username=skema&db=skema&ns=public" 2> /dev/null

open-app: ## Open project in browser
	@xdg-open http://localhost:8081 2> /dev/null

open-rabbit: ## Open project in browser
	@xdg-open http://localhost:15672 2> /dev/null