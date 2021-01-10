SHELL := /bin/bash

.PHONY: tests
tests:
	$(call echo_title,Run tests)
	@symfony console doctrine:fixtures:load -n
	@symfony run bin/phpunit

start: start-docker start-server start-worker

stop: stop-server-worker stop-docker

restart: stop start

start-docker:
	$(call echo_title,Start Docker containers)
	@docker-compose up -d

stop-docker:
	$(call echo_title,Stop Docker containers)
	@docker-compose stop

start-server:
	$(call echo_title,Start Web server)
	@symfony server:start -d

start-worker:
	$(call echo_title,Start Workers to consume messages)
	@sleep 2
	@symfony run -d --watch=config,src,templates,vendor symfony console messenger:consume async

stop-server-worker:
	$(call echo_title,Stop Workers and Web server)
	@symfony server:stop

message-failed-show:
	$(call echo_title,Inspect failed messages)
	@symfony console messenger:failed:show

message-failed-retry:
	$(call echo_title,Retry failed messages)
	@symfony console messenger:failed:retry

workflow-dump:
	$(call echo_title,"Dump workflow - paste the code in https://dreampuf.github.io/GraphvizOnline to show the graph")
	@symfony console workflow:dump comment

load-fixtures:
	$(call echo_title,Load fixtures to database)
	@symfony console doctrine:fixtures:load

connect-db:
	$(call echo_title,Connect to database)
	@docker exec -it guestbook_database_1 psql -U main -W main

status:
	$(call echo_title,Show status)
	@symfony server:status

log:
	$(call echo_title,Show logs)
	@symfony server:log

purging-cache:
	$(call echo_title,Purging HTTP Cache)
	@rm -rf var/cache/dev/http_cache

##############
### FUNCTIONS
##############
define echo_title
	@echo ""
    @echo "--> ${1}"
    @echo ""
endef