from collections.abc import MutableMapping


def read_file(file) -> bytes:
    with open(file, 'rb') as f:
        return f.read()

