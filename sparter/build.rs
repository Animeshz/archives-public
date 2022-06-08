extern crate clap;

include!("src/opts.rs");
use clap::Shell;

fn main() {
    let mut app = CliOpts::clap();
    app.gen_completions(env!("CARGO_PKG_NAME"), Shell::Bash, "target");
    app.gen_completions(env!("CARGO_PKG_NAME"), Shell::Fish, "target");
    app.gen_completions(env!("CARGO_PKG_NAME"), Shell::Zsh, "target");
}