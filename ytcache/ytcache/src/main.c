#include <stdio.h>
#include "headers.h"
#include <emscripten.h>


void prechecks() {
    EM_ASM({
        ;
    });
}

void prechecksB(int B) {
    printf("Oka\n");
}

typedef void (*int_taking)(int);
//EM_JS(void, onUpdated, (int_taking pc, int d), {
//    chrome.tabs.onUpdated.addListener((a, b, c) => console.log(typeof(pc), pc, d));
//});



int main() {
    printf("Hello World\n");
    //EM_ASM({
    //    chrome.tabs.onUpdated.addListener(function (tabId, changeInfo, tab) {
    //        console.log(changeInfo.url);
    //    });
    //});
    //onUpdated(prechecksB, 55);
    onUpdated();
    return 0;
}
