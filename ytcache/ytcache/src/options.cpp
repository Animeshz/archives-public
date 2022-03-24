#include <emscripten/val.h>
#include <js-bind/bind.hpp>
#include <iostream>
#include "headers.h"

using namespace std::placeholders;
using emscripten::val;

extern "C" {

void save_option(char *key, char *value) {
    val chrome = val::global("chrome");
    val storage = chrome["storage"]["local"];
    val parameter = val::object();
    parameter.set(key, std::string(value));
    storage.call<void>("set", parameter);
}

char *retrieve_option(char *key, char *default_value) {
    val chrome = val::global("chrome");
    val storage = chrome["storage"]["local"];
    val parameter = val::object();
    parameter.set(key, std::string(default_value));
    //storage.call<void>("get", parameter, js::bind([key] (val values) { std::cout << values[key].as<std::string>() << std::endl; }, std::placeholders::_1));
    auto fn = [] (val values) {};
    storage.call<void>("get", parameter, js::bind(fn, _1));
    return NULL;
}

}
