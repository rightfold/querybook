all: dependencies

.PHONY: test
test: dependencies
	vendor/bin/phpunit --bootstrap bootstrap.php test

.PHONY: dependencies
dependencies:
	composer update
