pub mod opts;
pub mod subcommand;

use crate::opts::CliOpts;
use clap::Parser;

fn main() {
    let opt = CliOpts::parse();
    println!("{:#?}", opt);
}
