SHELL := /bin/bash

.PHONY: tests
tests:
	symfony console doctrine:fixtures:load -n
	symfony run bin/phpunit