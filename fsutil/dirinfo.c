#include <dirent.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <sys/stat.h>

#ifdef __MINGW32__
#define strdup _strdup
#endif

int EXTRA_PRINT=0;
int HEADER_PRINT=0;
int MAIN_PRINT=1;
int TOTAL_PRINT=0;
int IGNORE_DOTFILES=0;

int is_file(char *filename) {
    struct stat s;
    if (stat(filename, &s) == 0) return s.st_mode & S_IFREG;
    else return 0;
}

int is_subdir(char *filename) {
    if (strcmp(filename, ".") == 0 || strcmp(filename, "..") == 0) return 0;
    if (strncmp(filename + strlen(filename) - 2, "/.", 2) == 0 || strncmp(filename + strlen(filename) - 3, "/..", 3) == 0) return 0;

    struct stat s;
    if (stat(filename, &s) == 0) return s.st_mode & S_IFDIR;
    else return 0;
}

// Ref: https://stackoverflow.com/a/6134127/11377112 (how git determines)
int is_binary(char *filename) {
    char buffer[8000];
    FILE *f = fopen(filename, "r");
    int read = fread(buffer, 1, 8000, f);
    fclose(f);
    for (int i = 0; i < read; i++) {
        if (buffer[i] == '\0') return 1;
    }
    return 0;
}

char *concatenate(char *str1, char *sep, char *str2) {
    size_t size = snprintf(NULL, 0, "%s%s%s", str1, sep, str2);
    char *buffer = (char *) malloc(size + 1);
    snprintf(buffer, size+1, "%s%s%s", str1, sep, str2);
    return buffer;
}

char *extension(char *filename) {
    int len = strlen(filename), i = len;
    while (filename[--i] != '.') if (i == 0 || filename[i] == '/') return filename + len;

    return filename + i + 1;
}

typedef struct dirinfo_node {
    char *ext;
    int file_count;
    int line_count;
    int blank_line_count;
    int total_size;
    struct dirinfo_node *next;
} dirinfo_node;
struct extra_info {
    int text_files_processed;
    int files_ignored;
} extra_info_holder;

dirinfo_node *dirinfo_head_node = NULL;
dirinfo_node *ext_node(char *ext) {
    dirinfo_node *node = dirinfo_head_node;
    while (node != NULL) {
        if (strcmp(node->ext, ext) == 0) return node;
        else node = node->next;
    }
    return NULL;
}

void dirinfo(char *dir_prefix, DIR *d) {
    struct dirent *dir;
    while ((dir = readdir(d)) != NULL) {

        if (IGNORE_DOTFILES && strncmp(dir->d_name, ".", 1) == 0) {
            extra_info_holder.files_ignored++;
            continue;
        }

        char *relative_fname = strcmp(dir_prefix, "") == 0 ? strdup(dir->d_name) : concatenate(dir_prefix, "/", dir->d_name);
        if (is_subdir(relative_fname)) {
            DIR *d = opendir(relative_fname);
            dirinfo(relative_fname, d);
            closedir(d);
        } else if (is_file(relative_fname)) {
            if (is_binary(relative_fname)) extra_info_holder.files_ignored++;
            else {
                extra_info_holder.text_files_processed++;
                char *ext = strdup(extension(dir->d_name));
                dirinfo_node *node = ext_node(ext);
                if (node != NULL) {
                    free(ext);
                } else {
                    node = malloc(sizeof(dirinfo_node));
                    memset(node, 0, sizeof(dirinfo_node));
                    node->next = dirinfo_head_node; dirinfo_head_node = node;
                    node->ext = ext;
                }

                FILE *f = fopen(relative_fname, "r");
                if (!f) { printf("Cannot read file: %s", relative_fname); exit(1); }
                node->file_count++;

                fseek(f, 0, SEEK_END);
                long file_size = ftell(f);
                fseek(f, 0, SEEK_SET);
                char *file_contents = malloc(file_size + 1);
                fread(file_contents, file_size, 1, f);
                fclose(f);

                node->total_size += file_size;
                for (int i = 0; i < file_size; i++) {
                    if (file_contents[i] == '\n') {
                        node->line_count++;
                        if (i+1 < file_size && strncmp(file_contents+i+1, "\n", 1) == 0 || i+2 < file_size && strncmp(file_contents+i+1, "\r\n", 2) == 0) {
                            node->blank_line_count++;
                        }
                    }
                }
                free(file_contents);
            }
        } else {
            extra_info_holder.files_ignored++;
        }

        free(relative_fname);
    }
}


void print_usage_and_exit(char *basename) {
    printf("Usage: %s [-t|-to|-h|-ho|-e|-i]  -- Gives file count, line count, and blank lines per file-extension in current directory\n", basename);
    printf("-t  -> print total\n");
    printf("-to -> print total only (exclusive option)\n");
    printf("-h -> print info header\n");
    printf("-ho -> print info header only (exclusive option)\n");
    printf("-e -> print extra information (traversal related)\n");
    printf("-i -> ignore dotfiles\n");
    exit(1);
}

int main(int argc, char *argv[]) {
    if (argc >= 2) {
        for (int i = 1; i < argc; i++) {
            if (strcmp(argv[i], "-to") == 0) {
                if (argc != 2) { printf("-to is exclusive and cannot be used with another option\n"); print_usage_and_exit(argv[0]); }
                TOTAL_PRINT=1;
                MAIN_PRINT=0;
                break;
            }
            if (strcmp(argv[i], "-ho") == 0) {
                if (argc != 2) { printf("-ho is exclusive and cannot be used with another option\n"); print_usage_and_exit(argv[0]); }
                HEADER_PRINT=1;
                MAIN_PRINT=0;
                break;
            }
            if (strcmp(argv[i], "-t") == 0) TOTAL_PRINT=1;
            else if (strcmp(argv[i], "-h") == 0) HEADER_PRINT=1;
            else if (strcmp(argv[i], "-e") == 0) EXTRA_PRINT=1;
            else if (strcmp(argv[i], "-i") == 0) IGNORE_DOTFILES=1;
            else { printf("Unknown option: %s\n", argv[i]); print_usage_and_exit(argv[0]); }
        }
    }


    DIR *d = opendir(".");
    if (!d) {
        printf("ERROR: Cannot read current directory\n");
        return 1;
    }

    dirinfo("", d);
    closedir(d);

    if (EXTRA_PRINT) {
        printf("%d text files processed\n%d files ignored\n\n", extra_info_holder.text_files_processed, extra_info_holder.files_ignored);
    }

    if (HEADER_PRINT) {
        printf("extension file_count line_count blank_line_count total_size_of_files\n");
    }

    dirinfo_node *node;

    // Print dirinfo collected information
    if (MAIN_PRINT) {
        node = dirinfo_head_node;
        while (node != NULL) {
            if (strcmp(node->ext, "") == 0)
                printf("unknown %d %d %d %d\n", node->file_count, node->line_count, node->blank_line_count, node->total_size);
            else
                printf("%s %d %d %d %d\n", node->ext, node->file_count, node->line_count, node->blank_line_count, node->total_size);
            node = node->next;
        }
    }
    if (TOTAL_PRINT) {
        node = dirinfo_head_node;
        int total_file_count = 0, total_line_count = 0, total_blank_line_count = 0, total_size;
        while (node != NULL) {
            total_file_count += node->file_count, total_line_count += node->line_count, total_blank_line_count += node->blank_line_count, total_size += node->total_size;
            node = node->next;
        }
        printf("total %d %d %d %d\n", total_file_count, total_line_count, total_blank_line_count, total_size);
    }

    // cleanup
    node = dirinfo_head_node;
    while (node != NULL) {
        free(node->ext);
        dirinfo_head_node = node->next;
        free(node); node = dirinfo_head_node;
    }
}
