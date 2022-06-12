use super::Point;

pub type Anchor = Point;
pub type Edge = (Anchor, Anchor);

pub enum AnchorDirection {
    Top,
    Bottom,
    Left,
    Right,
}

pub struct Shape {}

pub mod predefined {}
