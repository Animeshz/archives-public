#include <emscripten.h>
#include <emscripten/fetch.h>

#ifdef __cplusplus
extern "C" {
#endif

struct download_queue;

//download(char *link, callback);
void save_option(char *key, char *value);
char *retrieve_option(char *key, char *default_value);
//char *retrieve_option(char *key, char *default_value, callback);

#ifdef __cplusplus
}
#endif
