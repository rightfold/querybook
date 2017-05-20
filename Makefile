all: server

test: test-server

.PHONY: server
server:
	${MAKE} -C server all

.PHONY: test-server
test-server:
	${MAKE} -C server test
