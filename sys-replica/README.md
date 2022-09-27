# SysReplica

Allows you to write your system source in a file.


## Installation

Install from pip:

```bash
pip install sys-replica
```

Build from source and Install:

```bash
git clone https://github.com/Animeshz/sys-replica && cd sys-replica
poetry build
pip install dist/sys_replica-*.whl
```


## QuickStart

A config file (`replica.py`) at its simplest level:

```python
from replica import resolve_repositories, read_file

REPOSITORIES = resolve_repositories(
    void="https://mirrors.dotsrc.org/voidlinux/current",
)

CONFIG = dict(
    packages=REPOSITORIES.void("fd", "curl")
    files={
        '$HOME/.config/starship.toml': read_file('config/starship.toml'),
        '$HOME/.config/micro/settings.json': '{"colorscheme": "one-dark", "mkparents": true}'
    }
)
```

Run:

```bash
replica reduce      # resolves all repositories and config into its simplest form
replica build       # builds configuration files and installation script in $CWD/build
replica query <repo-url> <pkg>  # searches pkg in a repository

./build/install     # Installs all the programs listed
./build/orphan      # Uninstalls any program unlisted after previous install run

./build/check       # Check conflicts with files & shows conflicts (if any) in git-diff format
./build/apply       # Applies non-conflicting files
./build/apply -f    # Force apply all files (overwriting)
```


## Modules

The config options are futher extended to provide simplistic configurables for complex things:

```python
CONFIG = dict(
    programs=dict(
         vscode=dict(
             enable=True,
             source=REPOSITORIES.void,
             extensions=[
                 "asvetliakov.vscode-neovim",
                 "Equinusocio.vsc-material-theme",
                 "ms-python.python",
                 "ms-vscode.cpptools",
                 "rust-lang.rust-analyzer",
             ],
             config=read_file("config/vscode/settings.json"),
         ),
    )
)
```

Where a module defined as below will listen to the `programs.vscode` key:

```python
from replica import Module, register_module

class VscodeModule(Module):
    def consumes():
        return ['programs.vscode']

    def reduce(cfg):
        return {...}  # read /src/inbuilt_modules/vscode.py

# somewhere
register_module(VscodeModule)
```
