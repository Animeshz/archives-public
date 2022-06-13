use super::*;

/// Represents a generic shape
pub struct Shape {
    pub anchors: Vec<Anchor>,
    pub edges: ShapeEdges,
}

impl Shape {
    // fn constraint();
}

pub struct ShapeEdges {
    left: Vec<Edge>,
    bottom: Vec<Edge>,
    top: Vec<Edge>,
    right: Vec<Edge>,
}

pub mod predefined {
    use super::*;

    // TODO: Get access to the rendering ctx while creating the shape in order to resolve the dimensions
    // Maybe have an abstract shape on which call to `.resolve(&rendering_ctx)` resolves dimensions
    // and form the `Shape`?
    pub fn rectangle(width: Measurement, height: Measurement) -> Shape {
        todo!()
    }

    pub fn right_triangle(
        width: Measurement,
        height: Measurement,
        right_angle: AnchorDirection,
    ) -> Shape {
        todo!()
    }
}
