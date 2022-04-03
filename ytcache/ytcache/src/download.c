#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <pthread.h>
#include "headers.h"

typedef void (*work_fun)(void *arg);

typedef struct download_arg {
    char *link;
    int chunk_size;
    int downloaded_bytes;
} download_arg;

typedef struct pool_work {
    work_fun         func;
    void             *arg;
    struct pool_work *next;
} pool_work;

typedef struct download_queue {
    pool_work       *work_first;
    pool_work       *work_last;
    pthread_mutex_t  work_mutex;
    pthread_cond_t   work_cond;
    pthread_cond_t   working_cond;
    int              working_cnt;
    int              thread_cnt;
    pthread_t       *pool;
    int              stop;
} download_queue;

pool_work *get_work(download_queue *q)
{
    pool_work *work;

    if (q == NULL)
        return NULL;

    work = q->work_first;
    if (work == NULL)
        return NULL;

    if (work->next == NULL) {
        q->work_first = NULL;
        q->work_last = NULL;
    } else {
        q->work_first = work->next;
    }

    return work;
}

static pool_work *create_work(work_fun func, void *arg)
{
    if (func == NULL)
        return NULL;

    pool_work *work = malloc(sizeof(*work));
    work->func = func;
    work->arg = arg;
    work->next = NULL;
    return work;
}

void clear_work(pool_work *wrk) {
    if (wrk != NULL) free(wrk);
}

void *queue_worker(void *qarg) {
    download_queue *q = qarg;
    pool_work *work;

    while (1) {
        pthread_mutex_lock(&(q->work_mutex));

        while (q->work_first == NULL && !q->stop)
            pthread_cond_wait(&(q->work_cond), &(q->work_mutex));

        if (q->stop)
            break;

        work = get_work(q);
        q->working_cnt++;
        pthread_mutex_unlock(&(q->work_mutex));

        if (work != NULL) {
            work->func(work->arg);
            clear_work(work);
        }

        pthread_mutex_lock(&(q->work_mutex));
        q->working_cnt--;
        if (!q->stop && q->working_cnt == 0 && q->work_first == NULL)
            pthread_cond_signal(&(q->working_cond));
        pthread_mutex_unlock(&(q->work_mutex));
    }

    q->thread_cnt--;
    pthread_cond_signal(&(q->working_cond));
    pthread_mutex_unlock(&(q->work_mutex));
    return NULL;
}

download_queue *create_queue(int num_threads) {
    download_queue *q = malloc(sizeof(download_queue));
    pthread_t thread;
    q->thread_cnt = num_threads;

    pthread_mutex_init(&(q->work_mutex), 0);
    pthread_cond_init(&(q->work_cond), 0);
    pthread_cond_init(&(q->working_cond), 0);
    q->work_first = 0;
    q->work_last = 0;
    for (int i = 0; i < num_threads; i++) {
        pthread_create(&thread, NULL, queue_worker, q);
        pthread_detach(thread);
    }

    return q;
}

void downloadProgress(emscripten_fetch_t *fetch) {
  if (fetch->totalBytes) {
    printf("Downloading %s.. %.2f%% complete.\n", fetch->url, fetch->dataOffset * 100.0 / fetch->totalBytes);
  } else {
    printf("Downloading %s.. %lld bytes complete.\n", fetch->url, fetch->dataOffset + fetch->numBytes);
  }
}

void download_chunk(void *arg) {
    download_arg *a = (download_arg *) arg;
    emscripten_fetch_attr_t attr;
    emscripten_fetch_attr_init(&attr);
    strcpy(attr.requestMethod, "GET");
    const char* headers[] = {"Range", "bytes=0-5000", 0}; // chunk_size
    attr.requestHeaders = headers;
    attr.attributes = EMSCRIPTEN_FETCH_PERSIST_FILE | EMSCRIPTEN_FETCH_SYNCHRONOUS;
    attr.onprogress = downloadProgress;
    attr.timeoutMSecs = 2*60;
    attr.userData = (void *)0;
    emscripten_fetch_t *fetch = emscripten_fetch(&attr, a->link);

    if (fetch->status == 200) {
        printf("Finished downloading %llu bytes from URL %s.\n", fetch->numBytes, fetch->url);
        // The data is now available at fetch->data[0] through fetch->data[fetch->numBytes-1];
    } else {
        printf("Downloading %s failed, HTTP failure status code: %d.\n", fetch->url, fetch->status);
    }
    emscripten_fetch_close(fetch);

}

void queue_download(download_queue *q, char *link) {
    download_arg *arg = malloc(sizeof(download_arg));
    arg->link = link;
    arg->chunk_size = 10000;
    arg->downloaded_bytes = 0;
    pool_work* work = create_work(download_chunk, arg);
}
