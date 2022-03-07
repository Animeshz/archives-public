.PHONY: all clean

CFLAGS += -O3 -std=c11 -Wall -Werror -Wextra -Wno-sign-compare -Wno-unused-parameter -Wno-unused-variable -Wshadow
CXXFLAGS += -O3 -std=c++11 -Wall -Werror -Wextra -Wno-sign-compare -Wno-unused-parameter -Wno-unused-variable -Wshadow

SRC_DIR := src
OBJ_DIR := build
DIST_DIR := dist
SRC_STATIC_DIR := src/static
DIST_STATIC_DIR := dist/static

SRC := $(wildcard $(SRC_DIR)/*.c)
OBJ := $(SRC:$(SRC_DIR)/%.c=$(OBJ_DIR)/%.o)
DIST := $(DIST_DIR)/main.wasm $(DIST_DIR)/main.js
SRC_STATIC := $(wildcard $(SRC_STATIC_DIR)/*)
DIST_STATIC := $(SRC_STATIC:$(SRC_STATIC_DIR)/%=$(DIST_STATIC_DIR)/%)


all: $(DIST) $(DIST_STATIC) $(DIST_DIR)/manifest.json

$(DIST): $(OBJ) | $(DIST_DIR)
	$(CC) $(CFLAGS) $^ -o $@

$(OBJ_DIR)/%.o: $(SRC_DIR)/%.c | $(OBJ_DIR)
	$(CC) $(CFLAGS) -c $< -o $@

$(DIST_STATIC_DIR)/%: $(SRC_STATIC_DIR)/% | $(DIST_STATIC_DIR)
	cp $< $@

$(DIST_DIR)/manifest.json: $(SRC_DIR)/manifest.json | $(DIST_DIR)
	cp $< $@

$(DIST_DIR) $(DIST_STATIC_DIR) $(OBJ_DIR):
	mkdir -p $@

clean:
	@$(RM) -rv $(DIST_DIR) $(OBJ_DIR)
