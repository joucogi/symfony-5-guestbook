start-spa:
	@symfony server:start -d --passthru=index.html

stop-spa:
	@symfony server:stop

compile:
	@API_ENDPOINT=`symfony var:export SYMFONY_DEFAULT_ROUTE_URL --dir=..` yarn encore dev

open:
	@symfony open:local

status:
	@symfony server:status