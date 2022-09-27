from .modules import register_module
from .inbuilt_modules.vscode import VscodeModule

def main():
    print('hello')

    register_module(VscodeModule)
