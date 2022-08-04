use std::ffi::CStr;
use std::io::Result;
use std::{mem, str};

#[cfg(target_os = "linux")]
pub fn kernel_version() -> Result<String> {
    unsafe {
        let mut uname = mem::zeroed();
        if libc::uname(&mut uname) == 0 {
            Ok(CStr::from_ptr(uname.release.as_ptr())
                .to_string_lossy()
                .into_owned())
        } else {
            Err(std::io::Error::last_os_error())
        }
    }
}

#[cfg(test)]
mod test {
    use udev::*;

    #[test]
    fn udev() {
        let mut er = Enumerator::new().unwrap();
        er.match_subsystem("block").unwrap();
        for dev in er.scan_devices().unwrap() {
            println!("{:#?}", dev);
            println!("{:#?} {:#?}", dev.devtype(), dev.driver());

            for property in dev.properties() {
                println!("{:?} = {:?}", property.name(), property.value());
            }
            println!("attrib");
            for attribute in dev.attributes() {
                println!("{:?} = {:?}", attribute.name(), attribute.value());
            }
        }
    }
}
