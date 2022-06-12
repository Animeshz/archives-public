use yansi::{Color, Paint};

/// A central struct to reference commonly used symbols
pub struct Symbol {
    pub arrow: char,
}

impl Symbol {
    /// Symbol table containing unicode characters
    pub const fn unicode() -> &'static Self {
        &Self { arrow: '➜' }
    }

    /// Symbol table containing ascii characters
    pub const fn ascii() -> &'static Self {
        &Self { arrow: '>' }
    }
}

/// A trait used to add formatting attributes to displayable items.
pub trait Fmt: Sized {
    /// Give this value the specified foreground colour
    ///
    /// # Example
    /// ```rust
    /// use yansi::Color;
    /// println!("{}", "Hello".fg(Color::Red))
    /// ```
    fn fg(self, color: Color) -> Paint<Self> {
        Paint::new(self).fg(color)
    }

    /// Give this value the specified background colour
    ///
    /// # Example
    /// ```rust
    /// use yansi::Color;
    /// println!("{}", "Hello".bg(Color::Green))
    /// ```
    fn bg(self, color: Color) -> Paint<Self> {
        Paint::new(self).bg(color)
    }
}
impl<T: std::fmt::Display> Fmt for T {}

#[cfg(test)]
mod tests {
    use super::*;
    use std::io::{Cursor, Write};

    #[test]
    fn unicode_ascii_write_works_together() {
        let arrow_unicode = Symbol::unicode().arrow;
        let arrow_ascii = Symbol::ascii().arrow;

        let mut c = Cursor::new(Vec::new());
        write!(c, "{}{}", arrow_unicode, arrow_ascii).expect("Cannot write to Cursor");

        let read = String::from_utf8(c.into_inner()).expect("Not UTF-8");
        assert_eq!(read, "➜>");
    }

    #[test]
    fn paint_on_unicode() {
        let arrow = Symbol::unicode().arrow;
        let arrow_fg = arrow.fg(Color::Blue);
        let arrow_bg = arrow.bg(Color::Green);
        let arrow_fg_bg = arrow.fg(Color::Blue).bg(Color::Green);
        let arrow_bg_fg = arrow.bg(Color::Green).fg(Color::Blue);

        assert_eq!(arrow_fg.to_string(), format!("[34m{}[0m", arrow));
        assert_eq!(arrow_bg.to_string(), format!("[42m{}[0m", arrow));
        assert_eq!(arrow_fg_bg.to_string(), format!("[42;34m{}[0m", arrow));
        assert_eq!(arrow_fg_bg, arrow_bg_fg);
    }
}
