#include <emscripten/val.h>
#include <js-bind/bind.hpp>
#include <iostream>
#include "headers.h"

using namespace std::placeholders;
using emscripten::val;

extern "C" {

void onUpdated() {
    val chrome = val::global("chrome");
    val tabs = chrome["tabs"];
    auto fn = [] (val tId, val chInfo, val tab) {
        if (chInfo["url"] != val::undefined())
            std::cout << chInfo["url"].as<std::string>() << std::endl;
    };
    tabs["onUpdated"].call<void>("addListener", js::bind(fn, _1, _2, _3));
}
}
