#[cfg(test)]
mod tests {
    use block_utils::{self, BlockUtilsError};

    #[test]
    fn print_disks() -> Result<(), BlockUtilsError> {
        let p = block_utils::get_block_devices()?;
        println!("{p:?}");

        Ok(())
    }
}