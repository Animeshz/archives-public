use libc;
use std::ffi::CStr;
use std::io::Result;
use std::str;

use crate::system;

use libparted::{Device, DeviceType, DiskPartIter, Partition};
use walkdir::WalkDir;

const MOUNT_PATH: &str = "/var/run/sparter";

fn get_devices() -> Vec<Device<'static>> {
    Device::devices(true)
        .filter(|x| x.length() != 0)
        .filter(|x| x.type_() != DeviceType::PED_DEVICE_RAM)
        .collect()
}

fn get_supported_fs() -> Vec<String> {
    let kern_fsmod_path = format!(
        "/lib/modules/{}/kernel/fs",
        system::kernel_version().unwrap()
    );

    WalkDir::new(kern_fsmod_path)
        .into_iter()
        .filter_map(|e| e.ok())
        .filter(|e| {
            e.file_type().is_file()
                && e.file_name()
                    .to_str()
                    .map(|n| n.ends_with(".ko.gz"))
                    .unwrap_or(false)
        })
        .filter_map(|e| {
            e.path()
                .file_name()
                .map(|n| n.to_str())
                .flatten()
                .map(|s| s.chars().take_while(|c| c != &'.').collect())
        })
        .collect()
}

fn backup<'a, I>(parts: I) -> Result<()>
where
    I: Iterator<Item = Partition<'a>>,
{
    std::fs::create_dir_all(MOUNT_PATH)?;

    for part in parts {
        if let Some(path) = part.get_path() {
            // mount each partition selected one by one
            // mount(path, MOUNT_PATH);

            // tar each of them, one by one
        }
    }

    todo!("Exit")
}

fn restore() {
    todo!()
}

// fn format_disk_information(disk: &Disk) -> String {
//     let mut output = String::new();
//     let device = unsafe { disk.get_device() };
//     write!(
//         &mut output,
//         "Disk {:?} {:?} ({:?}) [{}]",
//         device.path(),
//         ByteSize::b(device.length() * device.sector_size()),
//         disk.get_disk_type_name().unwrap(),
//         device.model()
//     );
//     // Disk "/dev/nvme0n1" 500.1 GB ("gpt") [Samsung SSD 970 EVO Plus 500GB]
//
//     output
// }

fn mount() {}

#[cfg(test)]
mod tests {
    use super::*;
    use sys_mount::*;

    // #[test]
    // fn print_disks() -> Result<(), BlockUtilsError> {
    //     let block_devices = get_blocks()?;
    //     let block_paths = block_devices.iter().map(|x| format!("/dev/{}", x.name)).collect::<Vec<String>>();
    //     let part = get_corresponding_partitions(&block_paths);

    //     let blockp1 = &block_paths[0];

    //     let mut f = File::open(blockp1).unwrap();
    //     let gpt = GPT::find_from(&mut f).unwrap();

    //     // Use VFS to read generic fs, write into tar + metadata in a file
    //     //
    //     // mkdir /var/run/appname
    //     // mount /dev/block /var/run/appname

    //     println!("{block_devices:#?}");
    //     println!("{part:#?}");
    //     println!("{block_paths:#?}");
    //     println!("{gpt:#?}");

    //     Ok(())
    // }

    #[test]
    fn libparted() -> std::io::Result<()> {
        let mut devices = get_devices();
        for mut dev in devices.iter_mut() {
            println!(
                "{} ({:?} = {:?}): {} ({}b | {}b)",
                dev.model(),
                dev.path(),
                dev.type_(),
                dev.length(),
                dev.sector_size(),
                dev.phys_sector_size()
            );

            let mut disk = libparted::Disk::new(&mut dev)?;
            // println!("{}", format_disk_information(&disk));

            let mut done = false;
            // let mut part = (0, 0);
            for p in disk.parts() {
                if p.geom_start() < 2048 {
                    continue;
                }
                // NAME, MODEL, MAJ:MIN, TRAN, FSTYPE, RM, SIZE, RO, LABEL, MOUNTPOINTS
                println!(
                    "{:?} {:?} {:?} {:?} {:?}",
                    p.fs_type_name(),
                    p.get_path(),
                    p.num(),
                    p.geom_length(),
                    p.type_get_name()
                );
                if !done && p.type_get_name() == "free" {
                    // part = (p.geom_start(), p.geom_end());
                    done = true;
                }
            }

            // let mut partition = Partition::new(&disk, PartitionType::PED_PARTITION_NORMAL, None, part.0, part.1)?;
            // let geom = partition.get_geom();
            // let constraint = geom.exact().unwrap();
            // println!("{} {}", geom.start(), geom.end());

            // disk.add_partition(&mut partition, &constraint);

            println!();
        }
        Ok(())
    }

    #[test]
    fn backup_test() -> Result<()> {
        let mut devices = get_devices();
        for dev in devices.iter_mut() {
            let disk = libparted::Disk::new(dev)?;
            backup(disk.parts()).unwrap();
        }

        Ok(())
    }

    #[test]
    fn print_fs() {
        let fs = get_supported_fs();
        let fs: Vec<&str> = fs.iter().map(|s| s.as_ref()).collect();
        let fs = FilesystemType::Set(&fs[..]);
        // udevadm info /dev/nvme0n1p1    # perfect fstype
        // C: 		if ((data = udev_device_get_property_value(dev, "ID_FS_TYPE"))) prop->fstype = xstrdup(data);

        println!("{:?}", fs);

        let mount = Mount::new("/dev/loop0p1", MOUNT_PATH, fs, MountFlags::empty(), None).unwrap();
        // sudo losetup -Pf disk1.img
        // /dev/loop0p1 /run/sparter ocfs2_dlmfs rw,relatime 0 0
    }
}
