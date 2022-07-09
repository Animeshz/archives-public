use std::{io, path::PathBuf, str::FromStr};

use bytesize::ByteSize;
use clap::{AppSettings, Parser, ValueHint};
use path_clean::PathClean;

const DISK_EXAMPLES: &str = "\
Examples:
    sparter disk list
    sparter disk backup /dev/sdx backup.img
    sparter disk backup /dev/sdx backup.img --partitions /dev/sdxx /dev/sdxy  # non-interactive
    sparter disk restore backup.img /dev/sdx
    sparter disk info <backup.img|/dev/sdx>
";
const PARTITION_EXAMPLES: &str = "\
Examples:
    sparter partition shrink 500M <before|after> /dev/sdxx
    sparter partition extend 500M <before|after> /dev/sdxx
    sparter partition create 1GiB <before|after> /dev/sdxx [--offset 2G]
    sparter partition delete /dev/sdxx
    sparter partition rename /dev/sdxx /dev/sdxx [--backup original-partition-table.save]
";
const SPACE_EXAMPLES: &str = "\
Examples:
    sparter space move 10G <before|after> /dev/sdxx <before|after> /dev/sdxx
";

/// A simple partitioning utility for easily [ resize | move | create | delete | backup | restore ] partition(s)
#[derive(Parser, Debug)]
#[clap(name = "sparter", version = "0.1.0", propagate_version = true)]
#[clap(global_setting = AppSettings::DeriveDisplayOrder, disable_help_subcommand = true)]
pub struct CliOpts {
    /// Writes logs at the given file
    #[clap(short, long, value_hint = ValueHint::AnyPath, global = true)]
    log_file: Option<IoMethod>,

    #[clap(subcommand)]
    action: SubCommand,
}

#[derive(Parser, Debug)]
pub enum SubCommand {
    /// Operations related to disk (list|backup|restore)
    #[clap(subcommand, after_help = DISK_EXAMPLES)]
    Disk(DiskCommand),

    /// Operations related to indivisual partitions (shrink|extend|create|delete)
    #[clap(subcommand, after_help = PARTITION_EXAMPLES)]
    Partition(PartitionCommand),

    /// Operations related to unallocated space (move)
    #[clap(subcommand, after_help = SPACE_EXAMPLES)]
    Space(SpaceCommand),
}

#[derive(Debug)]
pub enum IoMethod {
    StdIo,
    File(PathBuf),
}

#[derive(Debug, clap::ArgEnum, Clone)]
pub enum Anchor {
    Before,
    After,
}

#[derive(Parser, Debug)]
pub enum DiskCommand {
    /// Disk listing subcommand
    List,

    /// Disk backup subcommand
    Backup {
        /// Disk to backup
        #[clap(value_hint = ValueHint::AnyPath)]
        disk: IoMethod,

        /// File to be written to
        #[clap(value_hint = ValueHint::AnyPath)]
        output: IoMethod,

        /// Partitions to backup (non-interactive)
        #[clap(short, long, value_hint = ValueHint::AnyPath, multiple_values = true)]
        partitions: Vec<IoMethod>,
    },

    /// Disk restore subcommand
    Restore {
        /// Backup image file to read from
        #[clap(value_hint = ValueHint::AnyPath)]
        input: IoMethod,

        /// Destination disk to restore into
        #[clap(value_hint = ValueHint::AnyPath)]
        disk: IoMethod,
    },

    /// Disk/Image info subcommand
    Info {
        /// Disk or Image File from which to retrieve information
        #[clap(value_hint = ValueHint::AnyPath)]
        from: IoMethod,
    },
}

#[derive(Parser, Debug)]
pub enum PartitionCommand {
    /// Partition shrink subcommand
    Shrink {
        /// Size to be shrunk/extended [e.g. 500M, 300MB, 2.5G, 5GiB]
        #[clap(parse(try_from_str = parse_size))]
        size: u64,

        /// Anchor
        #[clap(arg_enum)]
        anchor: Anchor,

        /// Target partition to be shrunk/extended
        #[clap(value_hint = ValueHint::AnyPath)]
        target: IoMethod,
    },

    /// Partition extend subcommand
    Extend {
        /// Size to be shrunk/extended [e.g. 500M, 300MB, 2.5G, 5GiB]
        #[clap(parse(try_from_str = parse_size))]
        size: u64,

        /// Anchor
        #[clap(arg_enum)]
        anchor: Anchor,

        /// Target partition to be shrunk/extended
        #[clap(value_hint = ValueHint::AnyPath)]
        target: IoMethod,
    },

    /// Partition create subcommand
    Create {
        /// Size to be shrunk/extended [e.g. 500M, 300MB, 2.5G, 5GiB]
        #[clap(parse(try_from_str = parse_size))]
        size: u64,

        /// Anchor
        #[clap(arg_enum)]
        anchor: Anchor,

        /// Target partition
        #[clap(value_hint = ValueHint::AnyPath)]
        from: IoMethod,

        /// Offset relative to given anchor
        #[clap(parse(try_from_str = parse_size))]
        offset: Option<u64>,
    },

    /// Partition delete subcommand
    Delete {
        /// Target partition
        #[clap(value_hint = ValueHint::AnyPath)]
        target: IoMethod,
    },

    /// Partition rename subcommand
    Rename {
        /// Target partition
        #[clap(value_hint = ValueHint::AnyPath)]
        target: IoMethod,

        /// New name
        new_name: String,
    },
}

#[derive(Parser, Debug)]
pub enum SpaceCommand {
    /// Unallocated space move subcommand
    Move {
        /// Size to be shrunk/extended [e.g. 500M, 300MB, 2.5G, 5GiB]
        #[clap(parse(try_from_str = parse_size))]
        size: u64,

        /// Anchor
        #[clap(arg_enum)]
        from_anchor: Anchor,

        /// Target partition
        #[clap(value_hint = ValueHint::AnyPath)]
        from: IoMethod,

        /// Anchor
        #[clap(arg_enum)]
        target_anchor: Anchor,

        /// Target partition
        #[clap(value_hint = ValueHint::AnyPath)]
        target: IoMethod,
    },
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

fn parse_size(src: &str) -> Result<u64, <ByteSize as FromStr>::Err> {
    Ok(src.parse::<ByteSize>()?.0)
}
