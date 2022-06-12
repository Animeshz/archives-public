pub mod path;
pub mod shape;

/// Measurement is used to define dimensions of [`shape::Shape`]
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

pub enum DiagonalDirection {
    TopLeft,
    TopRight,
    BottomLeft,
    BottomRight,
}

pub enum StraightDirection {
    Left,
    Bottom,
    Top,
    Right,
}
impl StraightDirection {
    /// Returns the opposite StraightDirection.
    fn opposite(&self) -> EdgeDirection {
        match self {
            EdgeDirection::Left => EdgeDirection::Right,
            EdgeDirection::Bottom => EdgeDirection::Top,
            EdgeDirection::Top => EdgeDirection::Bottom,
            EdgeDirection::Right => EdgeDirection::Left,
        }
    }
}

/// `Anchor` refers to a vertex of a shape.
pub type Anchor = Point;

/// `AnchorDirection` refers to direction of anchor towards the [`shape::Shape`].
pub type AnchorDirection = DiagonalDirection;

/// `EdgeDirection` refers to direction of Edge outwards the [`shape::Shape`].
pub type EdgeDirection = StraightDirection;

/// `Edge` refers to any of straight line formed between any two `Anchor`s of the `Shape`, or `AreaBoundary` of the terminal.
#[derive(Copy, Clone, Debug)]
pub enum Edge {
    AreaBoundary(EdgeDirection),
    Shape {
        from: Anchor,
        to: Anchor,
        direction: EdgeDirection,
    },
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
    fn with_margin(&mut self, width: u16) -> Self {
        self
    }
}
