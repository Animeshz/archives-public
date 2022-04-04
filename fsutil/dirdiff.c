#include <dirent.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <sys/stat.h>

int CONTENT_DIFF=0;
int SYMMETRIC_DIFF=0;

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

void dirdiff(char *dir_prefix1, char *dir_prefix2, DIR *d) {
    struct dirent *dir;
    while ((dir = readdir(d)) != NULL) {

        char *relative_fname1 = concatenate(dir_prefix1, "/", dir->d_name);
        char *relative_fname2 = concatenate(dir_prefix2, "/", dir->d_name);
        if (is_subdir(relative_fname1)) {
            DIR *subd = opendir(relative_fname1);
            dirdiff(relative_fname1, relative_fname2, subd);
            closedir(subd);
        } else if (is_file(relative_fname1) && !is_file(relative_fname2)) {
            printf("%s\n", relative_fname1);
        } else if (CONTENT_DIFF && is_file(relative_fname1) && is_file(relative_fname2)) {
            FILE *f1 = fopen(relative_fname1, "r"); if (!f1) { printf("Cannot read file: %s", relative_fname1); exit(1); }
            FILE *f2 = fopen(relative_fname2, "r"); if (!f2) { printf("Cannot read file: %s", relative_fname2); exit(1); }
            fseek(f1, 0, SEEK_END); long file_size1 = ftell(f1); fseek(f1, 0, SEEK_SET);
            fseek(f2, 0, SEEK_END); long file_size2 = ftell(f2); fseek(f2, 0, SEEK_SET);
            int same = file_size1 == file_size2;
            if (same) {
                char *file_contents1 = malloc(file_size1 + 1);
                char *file_contents2 = malloc(file_size2 + 1);
                fread(file_contents1, file_size1, 1, f1);
                fread(file_contents2, file_size2, 1, f2);
                same = strncmp(file_contents1, file_contents2, file_size1) == 0;
            }
            fclose(f1);
            fclose(f2);

            if (!same) {
                printf("%s\n", relative_fname1);
            }
        }
        free(relative_fname1);
        free(relative_fname2);

    }
}
void print_usage_and_exit(char *basename) {
    printf("Usage: %s [-s|-c] <dir1> <dir2>  -- lists file in dir1 and not in dir2\n", basename);
    printf("-s -> Performs symmetric difference (files uncommon in both dir)\n");
    printf("-c -> Also take in account the content differences in files (exclusive from -s)\n");
    exit(1);
}

int main(int argc, char *argv[]) {
    char *dirs[2]; int k = 0, positional = 0;
    for (int i = 1; i < argc; i++) {
        if (!positional && argv[i][0] == '-') {
            if (argv[i][1] == 's') SYMMETRIC_DIFF=1;
            else if (argv[i][1] == 'c') CONTENT_DIFF=1;
            else if (argv[i][1] == '-') { positional = 1; continue; }
            else { printf("Unknown option: %s\n", argv[i]); print_usage_and_exit(argv[0]); }
        } else {
            if (k == 2) { printf("Must pass only two directories.\n"); print_usage_and_exit(argv[0]); }
            dirs[k++] = argv[i];
        }
    }
    if (k < 2) {
        printf("Must pass two directories to take difference of.\n");
        print_usage_and_exit(argv[0]);
    }
    if (SYMMETRIC_DIFF == 1 && CONTENT_DIFF == 1) {
        printf("Options -s and -c are exclusive.\n");
        print_usage_and_exit(argv[0]);
    }

    DIR *d;
    d = opendir(dirs[0]);
    if (!d) { printf("ERROR: Cannot read dir1 directory\n"); return 1; }
    if (!is_dir(dirs[1])) { printf("ERROR: Cannot read dir2 directory\n"); return 1; }
    dirdiff(dirs[0], dirs[1], d);
    closedir(d);

    if (SYMMETRIC_DIFF) {
        d = opendir(dirs[1]);
        dirdiff(dirs[1], dirs[0], d);
        closedir(d);
    }

}
