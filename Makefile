all: help

# This will output the help for each task. thanks to https://marmelab.com/blog/2016/02/29/auto-documented-makefile.html
help: ## Show this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)


composer-install: ## Install composer dependencies
	@docker run --rm --interactive --tty --volume $(PWD):/app --user $(id -u):$(id -g) composer install

php-cs-fix: ## Run CS fixer and fix
	@docker run --rm -it --volume $(PWD):/app -w /app --user $(id -u):$(id -g) miroff/php-cs-fixer:3.11.0 php-cs-fixer fix -v


PHPUNIT=docker run --rm -it --volume $(shell pwd):/app  -w /app miroff/phpunit:9.5.26 php -dxdebug.mode=coverage /usr/local/composer/vendor/bin/phpunit

tests-run: ## Run unit tests
	$(PHPUNIT) --testdox

tests-coverage: ## Run test coverage
	$(PHPUNIT) --coverage-html coverage


pre-commit: php-cs-fix tests-run ## run all pre-commit commands
