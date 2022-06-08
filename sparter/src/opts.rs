use std::{io, path::PathBuf, str::FromStr};

use bytesize::ByteSize;
use clap::{Parser, ValueHint};
use path_clean::PathClean;

/// A simple partitioner
#[derive(Parser, Debug)]
#[clap(name = "sparter", version = "0.1.0")]
pub struct CliOpts {
    /// Writes logs at the given file
    #[clap(short, long, value_hint = ValueHint::AnyPath, global = true)]
    log_file: Option<IoMethod>,

    #[clap(subcommand)]
    action: SubCommand,
}

#[derive(Parser, Debug)]
pub enum SubCommand {
    /// Lists all the partitions in the drive
    List {
        #[clap(value_hint = ValueHint::AnyPath)]
        from: IoMethod,
    },

    /// Partition backup subcommand
    #[clap(subcommand)]
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

#[derive(Parser, Debug)]
pub enum BackupCommand {
    /// Create a backup of paritition
    Create {
        #[clap(value_hint = ValueHint::AnyPath)]
        partition: IoMethod,

        #[clap(value_hint = ValueHint::AnyPath)]
        output: IoMethod,
    },

    /// Restore a partition from a backup
    Restore {
        #[clap(value_hint = ValueHint::AnyPath)]
        input: IoMethod,

        #[clap(value_hint = ValueHint::AnyPath)]
        partition: IoMethod,
    },

    /// Retrieves information of the partition
    Info {
        #[clap(value_hint = ValueHint::AnyPath)]
        input: IoMethod,
    },
}

#[derive(Parser, Debug)]
pub struct ResizeCommand {
    /// Size to be shrunk/extended (e.g. 500M, 300MB, 2.5G, 5GiB)
    #[clap(parse(try_from_str = parse_size))]
    size: u64,

    /// Anchor (`before` or `after`)
    #[clap(arg_enum)]
    anchor: Anchor,

    /// Target partition to be shrunk/extended
    #[clap(value_hint = ValueHint::AnyPath)]
    target: IoMethod,
}

#[derive(Parser, Debug)]
pub struct PositionCommand {
    position: u32,

    /// Size to be shrunk/extended (e.g. 500M, 300MB, 2.5G, 5GiB)
    #[clap(parse(try_from_str = parse_size))]
    size: u64,

    /// Anchor (`before` or `after`)
    #[clap(arg_enum)]
    anchor: Anchor,

    /// Target partition
    #[clap(value_hint = ValueHint::AnyPath)]
    target: IoMethod,
}

#[derive(Parser, Debug)]
pub struct DeleteCommand {
    /// Target partition
    #[clap(value_hint = ValueHint::AnyPath)]
    target: IoMethod,
}

#[derive(Parser, Debug)]
pub struct RenameCommand {
    /// Target partition
    #[clap(value_hint = ValueHint::AnyPath)]
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

#[derive(Debug, clap::ArgEnum, Clone)]
enum Anchor {
    Before,
    After,
}

fn parse_size(src: &str) -> Result<u64, <ByteSize as FromStr>::Err> {
    Ok(src.parse::<ByteSize>()?.0)
}
