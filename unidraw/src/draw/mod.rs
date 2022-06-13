pub mod path;
pub mod shape;

use std::convert::From;

/// Measurement is used to define dimensions of [`shape::Shape`]
#[derive(Copy, Clone, Debug)]
pub enum Measurement {
    Exact(u16),
    Percentage(u8),
    Weight(u8),
}

/// Represents a point in the cartesian plane (of the terminal ofc).
#[derive(Copy, Clone, Debug)]
pub struct Point {
    pub x: i16,
    pub y: i16,
}

/// Represents a cell at a point in the cartesian plane (of the terminal ofc).
#[derive(Copy, Clone, Debug, Default)]
pub struct Cell {
    pub c: char,
    // modifier: Modifier,  // if modifier is just a bitmask then probably a good idea to have Copy trait
}

#[derive(Copy, Clone, Debug)]
pub enum DiagonalDirection {
    TopLeft,
    TopRight,
    BottomLeft,
    BottomRight,
}

#[derive(Copy, Clone, Debug)]
pub enum StraightDirection {
    Left,
    Bottom,
    Top,
    Right,
}

/// `AnchorDirection` refers to direction of anchor towards the [`shape::Shape`].
pub type AnchorDirection = DiagonalDirection;

/// `EdgeDirection` refers to direction of Edge outwards the [`shape::Shape`].
pub type EdgeDirection = StraightDirection;

/// `Anchor` refers to a vertex of a shape.
#[derive(Copy, Clone, Debug)]
pub struct Anchor(Point, AnchorDirection);

/// `Edge` refers to any of straight line formed between any two [`Anchor`]s of the [`shape::Shape`] or the Terminal.
#[derive(Copy, Clone, Debug)]
pub struct Edge {
    from: Anchor,
    to: Anchor,
    direction: EdgeDirection,
}

impl From<(Anchor, Anchor)> for Edge {
    fn from(anchors: (Anchor, Anchor)) -> Self {
        todo!()
    }
}

// Make constraint resolving at the time of rendering.
// Try to locate an object by the key, iterate over edges and select an edge.
// Once all the edges are constrained of an structure

/// Its synonymous to a Spring, connects any two opposite facing edges.
///
/// Any edge can only have one constraint attached to it.
pub struct EdgeConstraint {
    pub from: Edge,
    pub to: Edge,
    // constraint: Constraint,
    // modifiers: Modifiers,
}
impl EdgeConstraint {
    pub fn with_margin(mut self, width: u16) -> Self {
        todo!()
    }
}
