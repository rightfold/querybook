all: server

.PHONY: server
server:
	${MAKE} -C server
