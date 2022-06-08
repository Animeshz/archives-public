use std::{io, path::PathBuf, str::FromStr};

use bytesize::ByteSize;
use path_clean::PathClean;
use structopt::StructOpt;

/// A simple partitioner
#[derive(StructOpt, Debug)]
#[structopt(name = "sparter", version = "0.0.1")]
pub struct CliOpts {
    /// Writes logs at the given file
    #[structopt(short, long, global = true)]
    log_file: Option<bool>,

    #[structopt(subcommand)]
    action: SubCommand,
}

#[derive(StructOpt, Debug)]
pub enum SubCommand {
    /// Lists all the partitions in the drive
    List { from: IoMethod },

    /// Partition backup subcommand
    Backup(BackupCommand),

    /// Partition shrink subcommand
    Shrink(ResizeCommand),

    /// Unallocated space move subcommand
    Move(PositionCommand),

    /// Partition extend subcommand
    Extend(ResizeCommand),

    /// Partition create subcommand
    Create(PositionCommand),

    /// Partition delete subcommand
    Delete(DeleteCommand),

    /// Partition rename subcommand
    Rename(RenameCommand),
}

#[derive(StructOpt, Debug)]
pub enum BackupCommand {
    /// Create a backup of paritition
    Create {
        partition: IoMethod,
        output: IoMethod,
    },

    /// Restore a partition from a backup
    Restore {
        input: IoMethod,
        partition: IoMethod,
    },

    /// Retrieves information of the partition
    Info { input: IoMethod },
}

#[derive(StructOpt, Debug)]
pub struct ResizeCommand {
    /// Size to be shrunk/extended (e.g. 500M, 300MB, 2.5G, 5GiB)
    #[structopt(parse(try_from_str = parse_size))]
    size: u64,

    /// Anchor (`before` or `after`)
    #[structopt(possible_values = &Anchor::variants())]
    anchor: Anchor,

    /// Target partition to be shrunk/extended
    target: IoMethod,
}

#[derive(StructOpt, Debug)]
pub struct PositionCommand {
    position: u32,

    /// Size to be shrunk/extended (e.g. 500M, 300MB, 2.5G, 5GiB)
    #[structopt(parse(try_from_str = parse_size))]
    size: u64,

    /// Anchor (`before` or `after`)
    #[structopt(possible_values = &Anchor::variants())]
    anchor: Anchor,

    /// Target partition
    target: IoMethod,
}

#[derive(StructOpt, Debug)]
pub struct DeleteCommand {
    /// Target partition
    target: IoMethod,
}

#[derive(StructOpt, Debug)]
pub struct RenameCommand {
    /// Target partition
    target: IoMethod,

    /// New name
    new_name: String,
}

#[derive(Debug)]
pub enum IoMethod {
    StdIo,
    File(PathBuf),
}

impl FromStr for IoMethod {
    type Err = io::Error;

    fn from_str(src: &str) -> io::Result<Self> {
        match src {
            "-" => Ok(IoMethod::StdIo),
            _ => {
                let path = PathBuf::from(src);
                let path = match path.is_absolute() {
                    true => path,
                    false => std::env::current_dir()?.join(path),
                }
                .clean();

                if path.exists() || path.parent().map(|v| v.exists()) == Some(true) {
                    Ok(IoMethod::File(path))
                } else {
                    Err(io::Error::new(
                        io::ErrorKind::NotFound,
                        format!("Path {:?} does not exist", path),
                    ))
                }
            }
        }
    }
}

#[derive(Debug)]
enum Anchor {
    Before,
    After,
}
impl Anchor {
    fn variants() -> [&'static str; 2] {
        ["before", "after"]
    }
}
impl FromStr for Anchor {
    type Err = core::convert::Infallible;

    fn from_str(src: &str) -> Result<Self, Self::Err> {
        match src {
            "before" => Ok(Anchor::Before),
            "after" => Ok(Anchor::After),
            _ => unreachable!(),
        }
    }
}

fn parse_size(src: &str) -> Result<u64, <ByteSize as FromStr>::Err> {
    Ok(src.parse::<ByteSize>()?.0)
}
