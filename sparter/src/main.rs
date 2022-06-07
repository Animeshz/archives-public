pub mod opts;

use opts::CliOpts;
use structopt::StructOpt;

fn main() {
    let opt = CliOpts::from_args();
    println!("{:#?}", opt);
}
