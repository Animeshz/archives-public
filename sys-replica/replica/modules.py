from abc import ABC, abstractmethod

class Module(ABC):
    @abstractmethod
    def consumes():
        pass

    @abstractmethod
    def reduce(cfg):
        pass


_registered_modules = []


def register_module(mod: Module):
    _registered_modules.append(mod)


def register_inbuilt_modules():
    from .inbuilt_modules.vscode import VscodeModule
    register_module(VscodeModule)


def resolve_config(cfg: dict) -> dict:
    for mod in _registered_modules.sort(consumes are ascending):
        mod.reduce()


def read_file(file) -> bytes:
    with open(file, 'rb') as f:
        return f.read()
