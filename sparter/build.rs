use clap::CommandFactory;
use clap_complete::{generate_to, shells};

include!("src/opts.rs");

fn main() -> std::io::Result<()> {
    let mut cmd = CliOpts::command();
    generate_to(shells::Bash, &mut cmd, env!("CARGO_PKG_NAME"), "target")?;
    generate_to(shells::Fish, &mut cmd, env!("CARGO_PKG_NAME"), "target")?;
    generate_to(shells::Zsh, &mut cmd, env!("CARGO_PKG_NAME"), "target")?;

    Ok(())
}
