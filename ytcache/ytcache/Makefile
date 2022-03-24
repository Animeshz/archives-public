.PHONY: all clean

CFLAGS += -O3 -std=gnu11 -pthread -Wall -Werror -Wextra -Wno-sign-compare -Wno-unused-parameter -Wno-unused-variable -Wshadow
CXXFLAGS += -O3 -std=c++14 -pthread -Wall -Werror -Wextra -Wno-sign-compare -Wno-unused-parameter -Wno-unused-variable -Wshadow
LDFLAGS += -lembind -pthread -s PTHREAD_POOL_SIZE=10

SRC_DIR := src
OBJ_DIR := build
DIST_DIR := dist
SRC_STATIC_DIR := src/static
DIST_STATIC_DIR := dist/static

SRC_C := $(wildcard $(SRC_DIR)/*.c)
SRC_CXX := $(wildcard $(SRC_DIR)/*.cpp)
OBJ_C := $(SRC_C:$(SRC_DIR)/%.c=$(OBJ_DIR)/%.o)
OBJ_CXX := $(SRC_CXX:$(SRC_DIR)/%.cpp=$(OBJ_DIR)/%.o)
DIST := $(DIST_DIR)/main.js $(DIST_DIR)/main.wasm
SRC_STATIC := $(wildcard $(SRC_STATIC_DIR)/*)
DIST_STATIC := $(SRC_STATIC:$(SRC_STATIC_DIR)/%=$(DIST_STATIC_DIR)/%)


all: $(DIST) $(DIST_STATIC) $(DIST_DIR)/manifest.json

$(DIST): $(OBJ_C) $(OBJ_CXX) | $(DIST_DIR)
	$(CC) $(LDFLAGS) $^ -o $@

$(OBJ_DIR)/%.o: $(SRC_DIR)/%.c | $(OBJ_DIR)
	$(CC) $(CFLAGS) -Iincludes -c $< -o $@
$(OBJ_DIR)/%.o: $(SRC_DIR)/%.cpp | $(OBJ_DIR)
	$(CXX) $(CXXFLAGS) -Iincludes -c $< -o $@

$(DIST_STATIC_DIR)/%: $(SRC_STATIC_DIR)/% | $(DIST_STATIC_DIR)
	cp -r $< $@

$(DIST_DIR)/manifest.json: $(SRC_DIR)/manifest.json | $(DIST_DIR)
	cp $< $@

$(DIST_DIR) $(DIST_STATIC_DIR) $(OBJ_DIR):
	mkdir -p $@

clean:
	@$(RM) -rv $(DIST_DIR) $(OBJ_DIR)
