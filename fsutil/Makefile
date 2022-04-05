.PHONY: all clean

CFLAGS += -O3 -std=gnu11 -pthread -Wall -Werror -Wextra -Wno-sign-compare -Wno-unused-parameter -Wno-unused-variable -Wshadow

SRC := $(wildcard *.c)
DIST := $(SRC:%.c=%)

all: $(DIST)

$(DIST): %: %.c
	$(CC) $(CFLAGS) -o $@ $<

clean:
	@$(RM) -v $(DIST) $(DIST:%=%.exe)
