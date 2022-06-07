use std::{
    fmt::Display,
    path::{Path, PathBuf},
    str::FromStr,
};

use bytesize::ByteSize;
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
    Info {
        input: IoMethod,
    },
}

#[derive(StructOpt, Debug)]
pub struct ResizeCommand {
    /// Size to be shrunk/extended (e.g. 500M, 300MB, 2.5G, 5GiB)
    #[structopt(parse(try_from_str = parse_size))]
    size: u64,

    /// Anchor (`before` or `after`)
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
    type Err = String;

    fn from_str(src: &str) -> Result<Self, Self::Err> {
        match src {
            "-" => Ok(IoMethod::StdIo),
            _ => {
                let path = PathBuf::from(src);
                if path.exists() || path.parent().unwrap_or_else(|| &Path::new("/")).exists() {
                    Ok(IoMethod::File(path))
                } else {
                    Err(format!("Path {} does not exists", src))
                }
            }
        }
    }
}

#[derive(StructOpt, Debug)]
enum Anchor {
    Before,
    After,
}

impl FromStr for Anchor {
    type Err = ParseAnchorError;

    fn from_str(src: &str) -> Result<Self, Self::Err> {
        match src {
            "before" => Ok(Anchor::Before),
            "after" => Ok(Anchor::After),
            _ => Err(ParseAnchorError),
        }
    }
}

#[derive(Debug)]
struct ParseAnchorError;
impl Display for ParseAnchorError {
    fn fmt(&self, f: &mut std::fmt::Formatter<'_>) -> std::fmt::Result {
        write!(f, "provided string was not `before` or `after`")
    }
}

fn parse_size(src: &str) -> Result<u64, <ByteSize as FromStr>::Err> {
    Ok(src.parse::<ByteSize>()?.0)
}

