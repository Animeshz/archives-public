pub mod opts;
// pub mod subcommand;
// pub mod size_fmt;
pub mod system;

use crate::opts::CliOpts;
use clap::Parser;

fn main() {
    let opt = CliOpts::parse();
    println!("{:#?}", opt);
}
