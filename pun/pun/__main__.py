import rich_click as click


click.rich_click.SHOW_ARGUMENTS = True
click.rich_click.APPEND_METAVARS_HELP = True
click.rich_click.USE_MARKDOWN = True


@click.group()
def completions():
    pass
@completions.command
def bash():
    pass
@completions.command
def zsh():
    pass
@completions.command
def fish():
    pass

@click.group()
def pman():
    pass
@pman.command
def add():
    pass
@pman.command
def rem():
    pass
@pman.command
def list():
    pass

@click.command()
def at():
    pass
@click.command()
def conflicts():
    pass
 

@click.group()
def translate():
    print('Not implemented')
    exit(0)

# @click.command()
# @click.option(
#     "--input",
#     type=click.Path(),
#     help="Input **file**. _[default: a custom default]_",
# )
# @click.option(
#     "--type",
#     default="files",
#     show_default=True,
#     help="Type of file to sync",
# )
# @click.option("--all", is_flag=True, help="Sync\n 1. all\n 2. the\n 3. things?")
# @click.option(
#     "--debug/--no-debug",
#     "-d/-n",
#     default=False,
#     help="# Enable `debug mode`",
# )
# def cli(input, type, all, debug):
#     """
#     My amazing tool does _**all the things**_.
#
#     This is a `minimal example` based on documentation from the [_click_ package](https://click.palletsprojects.com/).
#
#     > Remember:
#     >  - You can try using --help at the top level
#     >  - Also for specific group subcommands.
#
#     """
#     print(f"Debug mode is {'on' if debug else 'off'}")


# pman [add|del|list] <pman>
# pman chroot <pman>
# pman completions <shell>

# def argument_parser():
#     parser = argparse.ArgumentParser('pun', description='Manage your system with multiple package-managers')
#     subcommands = parser.add_subparsers(title='subcommands')
#
#     pman = subcommands.add_parser('pman', help='Package manager subcommands')
#     pman.add_argument('list', help='lists all pkg managers installed')
#     pman.add_argument('add', help='add a pkg manager')
#     pman.add_argument('del', help='deletes a pkg manager')
#
#     return parser


if __name__ == '__main__':
    # args = argument_parser().parse_args()
    args = cli()
    print(args)
    pass
