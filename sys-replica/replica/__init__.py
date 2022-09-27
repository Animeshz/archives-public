import sys
import importlib


HELP = '''\
USAGE: replica [-h,--help] [SUBCOMMAND]

SUBCOMMAND:
  query <repo-url> <pkg-pattern>
                  - queries repository specified with given regex search pattern
  build           - builds configuration files and installation script in $CWD/build
'''


def query(repo_url, pkg_pattern):
    pass


def build():
    cfg = importlib.import_module('config')
    print(cfg.CONFIG)
    # build all the scripts


def main():
    if len(sys.argv) == 1 or '-h' in sys.argv or '--help' in sys.argv:
        print(HELP)
        exit(0)

    if sys.argv[1] != 'query' and sys.argv[1] != 'build':
        print("Unrecognized option: " + ' '.join(sys.argv[1:]))
        print(HELP)
        exit(1)

    globals()[sys.argv[1]](*sys.argv[2:])
