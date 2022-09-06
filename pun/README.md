# PUN - Packages UNified

A just works tool which provides a basic layer to use and control multiple package-managers without doing *something stupid* to your system.

Traditional linux package management includes 2-step process:
* Getting the package content from the repositories.
* Making the package available to the end-user.

We'd not touch the first one. The second one, which can be dangerous if handed over to the package manager itself, e.g. a version mismatch on libc that will completely break the system, we'll isolate the changes to the system (while hiding most details user won't care while doing so).

Effectively providing a simple, minimal & flexible interface which is further described in [How does it work?](#how-does-it-work) if you're up for knowing the inner-details.


## Installation & QuickStart

Dependencies: `pkg-config fuse3 python3 python3-pip just rust`

Build and Install:

```bash
git clone https://github.com/Animeshz/pun && cd pun
just install
```

Enable the `pund` service, and add yourself to pun group (`sudo usermod -aG pun $(whoami)`).

Run `pun --help` to know more. Basic usage is given below:

```bash
# Add package-manager
pun add xbps
pun add pacman

# Use package-manager as you do
xbps-install -S btop
pacman -Sy neofetch

# To know which pkg-manager the file comes from
pun list /usr/bin/btop                  # Priority-wise listing of all the pkg-man providing the file
pun at apk btop                         # Trigger btop coming from apk
pun at pacman --restricted makepkg -si  # Only stuffs coming from pacman visible to the command
pun at apt --unrestricted neofetch      # Force unrestricted, detect all pkgs from different pkg-managers

# Translate between different pkg managers
pun translate --to=pacman xbps-install -S tar          # pacman -Sy tar
eval $(pun translate --to=pacman xbps-install -S tar)  # run that one
```

The daemon configuration file is located at `/etc/pun.py`. Set the following variables to alter the behavior:

```python
import pun

CONFIG = dict(
    pkg_preferences = ['xbps', 'pacman', 'apk', 'apt']
    restricted_bins = ['xbps-src', 'makepkg']  # These binaries only see their own system (enabled by default for pkg-managers)
    
    # TODO: Rethink over this
    # file_overrides = pun.pkgs.list_files('xbps:python3') + ['/pun/apt/usr/bin/btop']
)
```


## How does it work?

This is highly inspired by how Nix/Guix and BedrockLinux manages their system.

Our design choice ensures ease-of-use while not giving up the flexibility and accessibility:

* Delegate all the work for getting the packages to the package-managers themselves.
* Create a seperate directory for each isolation layer `/pun/<pkg-manager>` managed by user-group `pun`.
* Use a FUSE filesystem `punfs` to make binaries, libraries and header files in them look as they're in the root filesystem (`/`).
* Prioritize the filesystem access to the system from which the tool has came from to elliminate version-conflicts, also provide `RESTRICTED_BINS` daemon config or inline `--restricted` flag for restricting its access completely to its own system only (useful for pkg managers for not getting confused).
* Provide `PKG_PREFERENCES` (safe) & `FILE_OVERRIDES` (unsafe) in daemon config to make discovery of pkgs preferential & files getting preferred the highest (version-mismatch may occur, so beware) respectively.

<sub>*BONUS: You can also mix the musl with glibc as packages prefer to use their own rootfs containing the package-manager they came from, although this is not throughly tested to work.*</sub>

