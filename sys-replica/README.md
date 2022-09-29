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
just install
```


## QuickStart

A config file (`config.py`) at its simplest level:

```python
from replica.modules import resolve_repositories, resolve_config, inbuilt_modules
from replica.utils import read_file

REPOSITORIES = resolve_repositories(
    void="https://mirrors.dotsrc.org/voidlinux/current",
)

CONFIG = resolve_config(
    packages=REPOSITORIES.void("fd", "curl")
    files={
        '$HOME/.config/starship.toml': read_file('config/starship.toml'),
        '$HOME/.config/micro/settings.json': '{"colorscheme": "one-dark", "mkparents": true}'
    }
)
```

Run:

```bash
replica query <repo-url> <pkg>  # searches pkg in repository specified (by regex)
replica build       # builds configuration files and installation script in $CWD/build

./build/install     # Installs all the programs listed
./build/orphan      # Uninstalls any program unlisted after previous install run

./build/check       # Check conflicts with files & shows conflicts (if any) in git-diff format
./build/apply       # Applies non-conflicting files
./build/apply -f    # Force apply all files (overwriting)
```

> PROTIP: You can print CONFIG to see what does the resolved CONFIG looks like


## Modules

The config options are futher extended to provide simplistic configurables for complex things:

```python
CONFIG = resolve_config(
    modules=[*inbuilt_modules(), my_module_1, my_module_2]
    programs=dict(
         vscode=dict(
             enable=True,
             install=REPOSITORIES.void("vscode"),
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
def vscode_module(cfg):
    cfg['files']['$HOME/.config/...'] =
        lambda: cfg['programs']['vscode']['config'] if cfg['programs']['vscode']['enable'] and 'config' in cfg['programs']['vscode'] else None
    # ...
```

**Note:** Multiple modules can register to same key, the resultant value must be implementing `+`, for example you can add multiple shell alias where each alias must be therefore str (as str+str is allowed in python).
