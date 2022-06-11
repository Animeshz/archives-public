use crossterm;
use std::io;

/// Delegates to [crossterm::terminal::size]
///
/// Returns the terminal size (columns, rows).
pub fn size() -> io::Result<(u16, u16)> {
    crossterm::terminal::size()
}
