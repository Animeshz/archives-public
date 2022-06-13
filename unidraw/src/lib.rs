//! # UniDraw
//!
//! UniDraw aims to provide 3 basic abstraction layers over the raw terminal access (using crossterm backend), in order
//! to print or draw complex [`Shape`](shape/struct.Shape.html)s and connect them via flexible arrows (called [`Path`](draw::path::Path)).
//!
//! - Layer 0: Consisting of
//!   - [`symbol::Symbol`] allowing you to print commonly used unicode symbols
//!   - [`symbol::Fmt`] allowing you to colorize fg/bg of any printable
//!   - [`draw::shape::Shape`] representing any generic shape
//!   - [`draw::shape::predefined`] containing a few of predefined shapes
//!   - [`render::RenderingCtx`] with [`RenderingMode::Cli`](render::RenderingMode::Cli) (for print & exit)
//!     or [`RenderingMode::Tui`](render::RenderingMode::Tui) (for print & loop over events).
//!
//! - Layer 1: Consisting of
//!   - Once a Shape is defined it can be organized into [`render::Matrix`] of terminal dimensions using the [`layout::Layout`]s.
//!   - Layout expects a [`layout::LayoutMixer`] for handling the layout, in case any overlapping happens.
//!   - Various types of layouts are defined in the [`layout::predefined`], if it doesn't meet your needs you are free to define your own layout.
//!
//! - Layer 2: Consisting of
//!   - [`draw::path::Path`] in order to connect any `Edge` to any another `Edge` (including on the same Shape itself),
//!   with 2 [`draw::path::PathAlgorithm`]s: `MinDist` and `NearestBorder`
// //!   (you can have yours)
// //!   - [`draw::span::Span`]
//!
//!   <br><sub>MiscInfo: Shapes are represented by [`draw::Anchor`]s along with their [`draw::AnchorDirection`], initially organized into
//!     into a buffer of size equivalent to the terminal size in the `RenderingCtx`, which is then efficiently rendered.</sub>
//!
//! ## Example:
//! ```rust
//! use std::io;
//! use unidraw::terminal;
//!
//! fn main() {
//!     let stdout = io::stdout();
//!     let term = terminal::cli(stdout);
//! }
//! ```

pub mod draw;
pub mod layout;
pub mod render;
pub mod symbol;
