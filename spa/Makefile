start-spa:
	$(call echo_title,Start SPA )
	@symfony server:start -d --passthru=index.html

stop-spa:
	$(call echo_title,Stop SPA)
	@symfony server:stop

compile:
	$(call echo_title,Compile SPA)
	@yarn encore dev

open:
	$(call echo_title,Open browser)
	@symfony open:local

##############
### FUNCTIONS
##############
define echo_title
	@echo ""
    @echo "--> ${1}"
    @echo ""
endef