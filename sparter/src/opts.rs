use structopt::StructOpt;

/// A simple partitioner
#[derive(StructOpt, Debug)]
#[structopt(name = "sparter")]
pub struct CliOpts {
    /// Writes logs at the given file
    #[structopt(short, long, global = true)]
    log_file: Option<bool>,

    #[structopt(subcommand)]
    sub_command: SubCommand,
}

#[derive(StructOpt, Debug)]
enum SubCommand {

}
