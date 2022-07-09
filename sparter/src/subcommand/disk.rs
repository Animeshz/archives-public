use std::path::PathBuf;
use block_utils::{self, BlockUtilsError, Device, MediaType};

fn get_blocks() -> Result<Vec<Device>, BlockUtilsError> {
    Ok(block_utils::get_all_device_info_iter(block_utils::get_block_devices()?)?
        .filter_map(|x| x.ok())
        .filter(|x| x.capacity != 0)
        .filter(|x| x.media_type != MediaType::Ram)
        .collect())
}

fn get_corresponding_partitions(block_device_path: &Vec<String>) -> Vec<Vec<PathBuf>> {
    block_device_path.iter()
        .map(|x| block_utils::get_children_devpaths_from_path(x).unwrap_or_else(|_| vec![]))
        .collect()
}

#[cfg(test)]
mod tests {
    use super::*;
    use std::fs::File;
    use block_utils::{self, BlockUtilsError};
    use gptman::GPT;

    #[test]
    fn print_disks() -> Result<(), BlockUtilsError> {
        let block_devices = get_blocks()?;
        let block_paths = block_devices.iter().map(|x| format!("/dev/{}", x.name)).collect::<Vec<String>>();
        let part = get_corresponding_partitions(&block_paths);

        let blockp1 = &block_paths[0];

        let mut f = File::open(blockp1).unwrap();
        let gpt = GPT::find_from(&mut f).unwrap();

        // Use VFS to read generic fs, write into tar + metadata in a file

        println!("{block_devices:#?}");
        println!("{part:#?}");
        println!("{block_paths:#?}");
        println!("{gpt:#?}");

        Ok(())
    }
}
