#include <dirent.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <errno.h>
#include <sys/stat.h>

#ifdef __MINGW32__
#include <direct.h>
#else
#include <sys/types.h>
#endif

void mkdir_if_required(char *dir) {
    int ret;
    errno = 0;
#ifdef __MINGW32__
    ret = _mkdir(dir);
#else
    ret = mkdir(dir, 0775);
#endif
    if (ret != 0 && errno != EEXIST) {
        printf("ERROR: Something went wrong creating subdirectory: %s, errno: %d\n", dir, errno);
        exit(1);
    }
}

int is_file(char *filename) {
    struct stat s;
    if (stat(filename, &s) == 0) return s.st_mode & S_IFREG;
    else return 0;
}

char *extension(char *filename) {
    int len = strlen(filename);
    while (filename[--len] != '.') if (len == 0 || filename[len] == '/') return NULL;

    char *ext = filename + len + 1;
    return strcmp(ext, "") == 0 ? NULL : ext;
}

char *concatenate(char *str1, char *sep, char *str2) {
    size_t size = snprintf(NULL, 0, "%s%s%s", str1, sep, str2);
    char *buffer = (char *) malloc(size + 1);
    snprintf(buffer, size+1, "%s%s%s", str1, sep, str2);
    return buffer;
}


int main() {
    DIR *d = opendir(".");
    if (!d) {
        printf("ERROR: Cannot read current directory\n");
        return 1;
    }

    struct dirent *dir;
    while ((dir = readdir(d)) != NULL) {
        char *ext = extension(dir->d_name);
        if (ext == NULL || !is_file(dir->d_name)) continue;
        mkdir_if_required(ext);

        char *move_name = concatenate(ext, "/", dir->d_name);
        rename(dir->d_name, move_name);
        free(move_name);
    }
    closedir(d);
}
