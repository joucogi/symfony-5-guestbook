SHELL := /bin/bash

.PHONY: tests
tests:
	$(call echo_title,Run tests)
	@symfony console doctrine:fixtures:load -n
	@symfony run bin/phpunit

start: start-docker start-server start-worker

stop: stop-server-docker stop-docker

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

stop-server-docker:
	$(call echo_title,Stop Workers and Web server)
	@symfony server:stop

message_failed_show:
	$(call echo_title,Inspect failed messages)
	@symfony console messenger:failed:show

message_failed_retry:
	$(call echo_title,Retry failed messages)
	@symfony console messenger:failed:retry

status:
	$(call echo_title,Show status)
	@symfony server:status

log:
	$(call echo_title,Show logs)
	@symfony server:log

##############
### FUNCTIONS
##############
define echo_title
	@echo ""
    @echo "--> ${1}"
    @echo ""
endef