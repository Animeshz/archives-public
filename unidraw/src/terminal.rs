use crossterm;
use std::io;

/// Delegates to [crossterm::terminal::size]
///
/// Returns the terminal size (columns, rows).
pub fn size() -> io::Result<(u16, u16)> {
    crossterm::terminal::size()
}

pub struct TerminalEdge;
impl TerminalEdge {
    pub const left: () = ();
    pub const bottom: () = ();
    pub const top: () = ();
    pub const right: () = ();
}
