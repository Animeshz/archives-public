# ytcache
[INCOMPLETE] A chrome extension for caching youtube video if dropped to a certain playlist with desired quality.

## Build Instructions

Install emscripten using the recommended [emsdk method](https://emscripten.org/docs/getting_started/downloads.html#installation-instructions-using-the-emsdk-recommended).

```bash
# If haven't sourced the emsdk envs
source /path/to/emsdk/emsdk_env.sh

cd ytcache
emmake make
```

The object files should be present in the `build` directory and final optimized build output should be present in the `dist` directory.
