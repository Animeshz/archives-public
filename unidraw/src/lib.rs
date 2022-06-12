//! # UniDraw
//!
//! UniDraw aims to provide 3 basic abstraction layers over the raw terminal access (using crossterm backend), in order
//! to print or draw complex [`Shape`](shape/struct.Shape.html)s and connect them via flexible arrows (called Path).
//!
//! - Layer 0: Consisting of
//!   - [`symbol::Symbol`] allowing you to print commonly used unicode symbols
//!   - [`symbol::Fmt`] allowing you to colorize fg/bg of any printable
//!   - [`terminal::size`] for getting dimensions of Terminal at the moment
//!
//! - Layer 1: Consisting of
//!   - [`draw::shape::Shape`] representing any generic shape
//!   - [`draw::shape::predefined`] contains a few of predefined shapes
// //!   - [`terminal::Terminal::set_mode`]: Allowing to set terminal to [`Cli`](`terminal::Terminal::Mode::Cli`) mode
// //!     (print & exit) or to [`Tui`](`terminal::Terminal::Mode::Tui`) mode (print & loop over events)
//!   - [`ConstraintLayout`](layout::ConstraintLayout) System (similar to Android), allowing you to constraint shapes
//!       using virtual springs.<br>
//!     Each shape has a `Vec<Edge>` for every [`Edge`](draw::shape::Edge) it has on the left, bottom, top and right
//!       sides.<br>
//!     You also have [`terminal::TerminalEdge`] representing the 4 edges of the terminal.
//!
//! - Layer 2: Consisting of
//!   - [`draw::path::Path`] in order to connect any `Edge` to any another `Edge` (including on the same Shape itself),
//!   with 2 [`draw::path::PathAlgorithm`]s: `MinDist` and `NearestBorder`
// //!   (you can have yours)
// //!   - [`::Span`]
//!
//!   <br><sub>MiscInfo: Shapes are represented by [`draw::shape::Anchor`]s along with their [`draw::shape::AnchorDirection`], rendered
//!     into a buffer of size equivalent to the terminal size, which is then efficiently rendered.</sub>
//!
//! ## Example:
//! ```rust
//! fn main() {
//!     let layout = ConstraintLayout::new(CrossTerm::new(stderr().lock()));
//! }
//! ```

pub mod draw;
pub mod render;
pub mod symbol;
pub mod terminal;
