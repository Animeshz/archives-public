from pathlib import Path
import os
from platform import system_alias
import subprocess
import sys
from rich import print


PUN_ROOT = '/pun'
PUN_PKG_ROOT = f'{PUN_ROOT}/pkgroot'
PUN_CFG_FILE = f'{PUN_ROOT}/cfg.py'

PKG_ROOT = Path(__file__).parent
TRIGGERS = PKG_ROOT / 'vendor/triggers'


def setup_pun_precheck():
    if os.path.exists(PUN_ROOT):
        return

    print()
    print('[cyan]=== Running initial setup ===[/cyan]')

    if os.geteuid() != 0:
        print('[red]ERROR: Couldn\'t proceed with initial setup. Please rerun with root privileges.[/red]', file=sys.stderr)
        exit(13)

    print('>> Creating [green]pun[/green] [blue]user[/blue] & [blue]group[/blue]')
    subprocess.run([str(TRIGGERS/'system-accounts'), 'run', 'post-install'],
                   env={'system_accounts': 'pun'}, cwd='/', check=True)

    print('>> Creating [green]pun[/green] [blue]root[/blue] at {}'.format(PUN_ROOT))
    subprocess.run([str(TRIGGERS/'mkdirs'), 'run', 'post-install'],
                   env={'make_dirs': f'{PUN_ROOT} 0755 pun pun'}, cwd='/', check=True)
    subprocess.run([str(TRIGGERS/'mkdirs'), 'run', 'post-install'],
                   env={'make_dirs': f'{PUN_PKG_ROOT} 0755 pun pun'}, cwd='/', check=True)
    print('>> Copying sample [green]cfg.py[/green] file at {}'.format(PUN_CFG_FILE))
    subprocess.run(['install', '-m', '0755', str(PKG_ROOT / 'cfg.py.sample'), PUN_CFG_FILE],
                   cwd='/', check=True)

    print('[cyan]=== Finished initial setup ===[/cyan]')
    print()
    print('[blue]SUGGESTION:[/blue] Add yourself to pun group ["sudo usermod -aG pun $(whoami)"] to manage any operations at {} without need of sudo'.format(PUN_ROOT))
    print()


if __name__ == '__main__':
    setup_pun_precheck()
