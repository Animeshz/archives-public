#include <dirent.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <sys/stat.h>

int PRINT_SIZE=0;
int IGNORE_DOTFILES=0;

int is_file(char *filename) {
    struct stat s;
    if (stat(filename, &s) == 0) return s.st_mode & S_IFREG;
    else return 0;
}

int is_dir(char *filename) {
    struct stat s;
    if (stat(filename, &s) == 0) return s.st_mode & S_IFDIR;
    else return 0;
}

int is_subdir(char *filename) {
    if (strcmp(filename, ".") == 0 || strcmp(filename, "..") == 0) return 0;
    if (strncmp(filename + strlen(filename) - 2, "/.", 2) == 0 || strncmp(filename + strlen(filename) - 3, "/..", 3) == 0) return 0;

    return is_dir(filename);
}

char *concatenate(char *str1, char *sep, char *str2) {
    size_t size = snprintf(NULL, 0, "%s%s%s", str1, sep, str2);
    char *buffer = (char *) malloc(size + 1);
    snprintf(buffer, size+1, "%s%s%s", str1, sep, str2);
    return buffer;
}

typedef struct sizesort_node {
    char *relative_file;
    long size;
    struct sizesort_node *next;
} sizesort_node;

struct sizesort_extrainfo {
    long largest_size;
} extra_info;

sizesort_node *sizesort_head_node = NULL;
void insert_node(char *relative_fname, long file_size) {
    sizesort_node *new_node = malloc(sizeof(sizesort_node));
    new_node->relative_file = relative_fname; new_node->size = file_size; new_node->next = NULL;
    if (file_size > extra_info.largest_size) extra_info.largest_size = file_size;

    sizesort_node *node = sizesort_head_node;
    if (node == NULL || node->size > new_node->size || (node->size == new_node->size && strcmp(new_node->relative_file, node->relative_file) < 0)) {
        new_node->next = sizesort_head_node;
        sizesort_head_node = new_node;
    } else {
        // Comparision: ascending order of size, but if equal then lexicographically sort on name
        //  * ---> * ---> * ---> * ---> * ---> *
        //  ^      ^
        //  |     compared with new_node
        // node
        while (node->next != NULL && (node->next->size < new_node->size || (node->next->size == new_node->size && strcmp(node->next->relative_file, new_node->relative_file) < 0)))
            node = node->next;
        new_node->next = node->next;
        node->next = new_node;
    }
}

void sizesort(char *dir_prefix, DIR *d) {
    struct dirent *dir;
    while ((dir = readdir(d)) != NULL) {

        if (IGNORE_DOTFILES && strncmp(dir->d_name, ".", 1) == 0) continue;
        char *relative_fname = strcmp(dir_prefix, "") == 0 ? strdup(dir->d_name) : concatenate(dir_prefix, "/", dir->d_name);
        if (is_subdir(relative_fname)) {
            DIR *subd = opendir(relative_fname);
            sizesort(relative_fname, subd);
            closedir(subd);
        } else if (is_file(relative_fname)) {
            FILE *f = fopen(relative_fname, "r"); if (!f) { printf("Cannot read file: %s", relative_fname); exit(1); }
            fseek(f, 0, SEEK_END); long file_size = ftell(f); fseek(f, 0, SEEK_SET);
            fclose(f);

            insert_node(strdup(relative_fname), file_size);
        }
        free(relative_fname);

    }
}
void print_usage_and_exit(char *basename) {
    printf("Usage: %s [-s|-i] -- Sorts files with relative path from $PWD in descending order of their size\n", basename);
    printf("-s -> Prints size in the front\n");
    printf("-i -> Ignore dotfiles\n");
    exit(1);
}

int main(int argc, char *argv[]) {
    for (int i = 1; i < argc; i++) {
        if (strcmp(argv[i], "-s") == 0) PRINT_SIZE=1;
        else if (strcmp(argv[i], "-i") == 0) IGNORE_DOTFILES=1;
        else { printf("Unknown option: %s\n", argv[i]); print_usage_and_exit(argv[0]); }
    }

    DIR *d = opendir(".");
    if (!d) { printf("ERROR: Cannot read dir1 directory\n"); return 1; }
    sizesort("", d);
    closedir(d);

    int pad = snprintf(NULL, 0, "%ld", extra_info.largest_size);
    sizesort_node *node = sizesort_head_node;
    while (node != NULL) {
        if (PRINT_SIZE)
            printf("%*ld %s\n", pad, node->size, node->relative_file);
        else
            printf("%s\n", node->relative_file);
        free(node->relative_file);
        sizesort_head_node = node->next;
        free(node); node = sizesort_head_node;
    }
}
