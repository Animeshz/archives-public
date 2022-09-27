from ..modules import Module, register_module

class VscodeModule(Module):
    def consumes():
        return ['programs.vscode']

    # TODO: Produce files and program list
    def reduce(cfg):
        return {...}