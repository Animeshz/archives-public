use std::str::FromStr;

#[derive(Debug)]
pub enum SizeUnit {
    Byte,
    KiloByte,
    KibiByte,
    MegaByte,
    MebiByte,
    GigaByte,
    GibiByte,
    TeraByte,
    TebiByte,
    PetaByte,
    PebiByte,
    ExaByte,
    ExbiByte,
}

impl SizeUnit {
    pub const fn to_bytes(&self) -> u64 {
        match self {
            Self::Byte => 1,
            Self::KiloByte => 1_000,
            Self::KibiByte => 1_024,
            Self::MegaByte => 1_000_000,
            Self::MebiByte => 1_048_576,
            Self::GigaByte => 1_000_000_000,
            Self::GibiByte => 1_073_741_824,
            Self::TeraByte => 1_000_000_000_000,
            Self::TebiByte => 1_099_511_627_776,
            Self::PetaByte => 1_000_000_000_000_000,
            Self::PebiByte => 1_125_899_906_842_624,
            Self::ExaByte => 1_000_000_000_000_000_000,
            Self::ExbiByte => 1_152_921_504_606_846_976,
        }
    }
}

impl std::str::FromStr for SizeUnit {
    type Err = String;

    fn from_str(unit: &str) -> Result<Self, Self::Err> {
        match unit.to_lowercase().as_str() {
            "b" => Ok(Self::Byte),
            // power of tens
            "k" | "kb" => Ok(Self::KiloByte),
            "m" | "mb" => Ok(Self::MegaByte),
            "g" | "gb" => Ok(Self::GigaByte),
            "t" | "tb" => Ok(Self::TeraByte),
            "p" | "pb" => Ok(Self::PetaByte),
            "e" | "eb" => Ok(Self::ExaByte),
            // power of twos
            "ki" | "kib" => Ok(Self::KibiByte),
            "mi" | "mib" => Ok(Self::MebiByte),
            "gi" | "gib" => Ok(Self::GibiByte),
            "ti" | "tib" => Ok(Self::TebiByte),
            "pi" | "pib" => Ok(Self::PebiByte),
            "ei" | "eib" => Ok(Self::ExbiByte),
            _ => Err(format!("couldn't parse unit of {:?}", unit)),
        }
    }
}

#[derive(Debug)]
pub struct Size(pub u64);

impl Size {
    pub const LN_KB: f64 = 6.90775527898213;    // ln 1000
    pub const LN_KIB: f64 = 6.93147180559945;   // ln 1024

    /// Resolves to the appropriate significant SizeUnit
    pub fn significant_unit(&self, kibi_family: bool) -> SizeUnit {
        let base = if kibi_family { Self::LN_KIB } else { Self::LN_KB };
        let exp = (self.0 as f64).ln() / base;
        let exp = exp as u8;

        if kibi_family {
            match exp {
                0 => SizeUnit::Byte,
                1 => SizeUnit::KibiByte,
                2 => SizeUnit::MebiByte,
                3 => SizeUnit::GibiByte,
                4 => SizeUnit::TebiByte,
                5 => SizeUnit::PebiByte,
                6 => SizeUnit::ExbiByte,
                _ => unreachable!(),
            }
        } else {
            match exp {
                0 => SizeUnit::Byte,
                1 => SizeUnit::KiloByte,
                2 => SizeUnit::MegaByte,
                3 => SizeUnit::GigaByte,
                4 => SizeUnit::TeraByte,
                5 => SizeUnit::PetaByte,
                6 => SizeUnit::ExaByte,
                _ => unreachable!(),
            }
        }
    }

    fn encode(&self, unit: &SizeUnit) -> String {
        format!("{} {:?}", self.0 / unit.to_bytes(), unit)
    }
}

impl FromStr for Size {
    type Err = String;

    fn from_str(value: &str) -> Result<Self, Self::Err> {
        if let Ok(v) = value.parse::<u64>() {
            return Ok(Self(v));
        }
        let number: String = value
            .chars()
            .take_while(|c| c.is_digit(10) || c == &'.')
            .collect();
        match number.parse::<f64>() {
            Ok(v) => {
                let suffix: String = value
                    .chars()
                    .skip_while(|c| c.is_whitespace() || c.is_digit(10) || c == &'.')
                    .collect();
                match suffix.parse::<SizeUnit>() {
                    Ok(u) => Ok(Self(v * u.to_bytes())),
                    Err(error) => Err(format!(
                        "couldn't parse {:?} into a known SI unit, {}",
                        suffix, error
                    )),
                }
            }
            Err(error) => Err(format!(
                "couldn't parse {:?} into a ByteSize, {}",
                value, error
            )),
        }
    }
}
