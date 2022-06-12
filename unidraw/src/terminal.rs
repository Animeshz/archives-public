use crossterm;
use std::io;

use crate::render::{Matrix, RenderingCtx};

/// Delegates to [crossterm::terminal::size]
///
/// Returns the terminal size (columns, rows).
pub fn size() -> io::Result<(u16, u16)> {
    crossterm::terminal::size()
}

/// Constructs a new [`CliTerminal`] locked onto the given `write`
pub fn cli<T: io::Write>(write: T) -> CliTerminal<T> {
    CliTerminal {
        write,
        buffer: Matrix {},
    }
}

// Constructs a new [`TuiTerminal`] locked onto the given `write`
// pub fn tui<T: io::Write>(write: T) -> TuiTerminal<T> {
//     todo!()
// }

/// Cli (print & exit) interface to the terminal
///
/// # Example:
/// ```rust
/// use unidraw::terminal;
///
/// let term = terminal::cli();
/// ```
pub struct CliTerminal<T: io::Write> {
    write: T,
    /// Holds result of current draw() call
    buffer: Matrix,
}

/// Tui (print & loop over events) interface to the terminal
///
/// # Example:
/// ```rust
/// use unidraw::terminal;
///
/// let term = terminal::tui();
/// ```
pub struct TuiTerminal<T: io::Write> {
    write: T,
    /// Holds result of current and previous draw() calls
    buffer: [Matrix; 2],
    /// Index of current buffer matrix
    buffer_index: usize,
}

impl<T: io::Write> CliTerminal<T> {
    /// Returns a rendering ctx, through which shapes can be made and rendered.
    pub fn draw(&mut self) -> &mut RenderingCtx {
        todo!()
    }
}

// impl<T: io::Write> TuiTerminal<T> {
//    /// Calls the closure every time re-rendering is required,
//    /// with rendering ctx through which shapes can be made and rendered.
//    pub fn draw(&mut self) -> &mut dyn RenderingCtx {
//     pub fn draw<F>(&mut self, callback: F) -> io::Result<()>
//     where
//         F: FnOnce(&mut RenderingCtx),
//     {
//         todo!()
//     }
// }
