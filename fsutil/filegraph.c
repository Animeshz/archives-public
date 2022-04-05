#include <dirent.h>
#include <stdio.h>
#include <stdlib.h>
#include <sys/stat.h>
#include <string.h>

int is_subdir(char *filename) {
    if (strcmp(filename, ".") == 0 || strcmp(filename, "..") == 0) return 0;
    if (strncmp(filename + strlen(filename) - 2, "/.", 2) == 0 || strncmp(filename + strlen(filename) - 3, "/..", 3) == 0) return 0;

    struct stat s;
    if (stat(filename, &s) == 0) return s.st_mode & S_IFDIR;
    else return 0;
}

char *concatenate(char *str1, char *sep, char *str2) {
    size_t size = snprintf(NULL, 0, "%s%s%s", str1, sep, str2);
    char *buffer = (char *) malloc(size + 1);
    snprintf(buffer, size+1, "%s%s%s", str1, sep, str2);
    return buffer;
}

// Symbols: https://en.wikipedia.org/wiki/Box-drawing_character
void print_file_graph(char *dir_prefix, char *print_prefix, DIR *d) {
    struct dirent *dir; int index = 0;
    while ((dir = readdir(d)) != NULL) {
        if (dir->d_name[0] == '.') continue;
        char *pointer = !index ? "┌── " : "├── ";
        char *relative_fname = strcmp(dir_prefix, "") == 0 ? strdup(dir->d_name) : concatenate(dir_prefix, "/", dir->d_name);
        if (is_subdir(relative_fname)) {
            DIR *subd = opendir(relative_fname);
            char *new_prefix = concatenate(print_prefix, "", !index ? "    " : "│   ");
            print_file_graph(relative_fname, new_prefix, subd);
            printf("%s%s%s\n", print_prefix, pointer, dir->d_name);
            free(new_prefix);
            closedir(subd);
        } else {
            printf("%s%s%s\n", print_prefix, pointer, dir->d_name);
        }
        index++;
    }
}

int main() {
    DIR *d = opendir(".");
    if (!d) {
        printf("ERROR: Cannot read current directory\n");
        return 1;
    }

    print_file_graph("", "", d);
    printf("$PWD\n");
    closedir(d);
}
