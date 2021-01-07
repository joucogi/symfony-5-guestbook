SHELL := /bin/bash

.PHONY: tests
tests:
	symfony console doctrine:fixtures:load -n
	symfony run bin/phpunit

start-worker:
	symfony run -d --watch=config,src,templates,vendor symfony console messenger:consume async
