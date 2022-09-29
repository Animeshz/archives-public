from abc import ABC, abstractmethod
from collections import defaultdict
from collections.abc import MutableMapping, Callable
from functools import reduce
import operator
import types
from typing import Concatenate


class ModuleResolver(MutableMapping):
    def __init__(self, initial: dict = {}, _level=0):
        self._source = {}
        self._being_calculated = set()
        self._level = _level

        if _level == 0:
            self._register_modules(initial)
            self._fill(initial)

    def _fill(self, source):
        for key in source:
            if isinstance(source[key], dict):
                self[key]._fill(source[key])
            else:
                self[key] = lambda _: source[key]

    def _register_modules(self, source):
        if 'modules' not in source:
            return

        for mod in source.pop('modules', []):
            mod(self)

    def __contains__(self, key) -> bool:
        return key in self._source

    def __getitem__(self, key):
        if key in self._being_calculated:
            raise RecursionError(f"Circular References detected at key={key}")

        if key not in self._source:
            self._source[key] = ModuleResolver(_level=self._level + 1)
        elif isinstance(self._source[key], list) and all(isinstance(x, types.FunctionType) for x in self._source[key]):
            self._being_calculated.add(key)
            results = filter(lambda x: x is not None, [x() for x in self._source[key]])
            self._source[key] = None if (first := next(results, None)) is None else reduce(operator.add, results, first)
            self._being_calculated.remove(key)

        return self._source[key]

    def __setitem__(self, key, val):
        if not isinstance(val, types.FunctionType):
            raise TypeError("val must be a function")

        if key not in self._source:
            self._source[key] = [val]
        elif isinstance(self._source[key], list) and all(isinstance(x, types.FunctionType) for x in self._source[key]):
            self._source[key].append(val)
        else:
            raise TypeError(
                f"Cannot add resolver to item that is either another resolver (dict-like) or accessed (calculated) value; key={key}; value={self._source[key]};")

    def __delitem__(self, key):
        raise NotImplementedError

    def __iter__(self):
        return iter(self._source)

    def __len__(self):
        return len(self._source)


def inbuilt_modules():
    from .inbuilt_modules.vscode import vscode_module
    return [vscode_module]


def resolve_config(**kwargs) -> ModuleResolver:
    return ModuleResolver(initial=kwargs)
