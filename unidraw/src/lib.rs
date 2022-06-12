#![feature(associated_type_defaults)]

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
//!   - [`draw::shape::predefined`] containing a few of predefined shapes
//!   - Constraint based relative-layout system (similar to Android), allowing you to constraint shapes using virtual springs<br>
//!     Each shape has a `Vec<Edge>` for every [`Edge`](draw::Edge) it has on the left, bottom, top and right sides<br>
//!   - Two types of terminal [`terminal::CliTerminal`] (for print & exit) or [`terminal::TuiTerminal`] (for print & loop over events)
//!
//! - Layer 2: Consisting of
//!   - [`draw::path::Path`] in order to connect any `Edge` to any another `Edge` (including on the same Shape itself),
//!   with 2 [`draw::path::PathAlgorithm`]s: `MinDist` and `NearestBorder`
// //!   (you can have yours)
// //!   - [`draw::span::Span`]
//!
//!   <br><sub>MiscInfo: Shapes are represented by [`draw::Anchor`]s along with their [`draw::AnchorDirection`], rendered
//!     into a buffer of size equivalent to the terminal size, which is then efficiently rendered.</sub>
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
pub mod render;
pub mod symbol;
pub mod terminal;
