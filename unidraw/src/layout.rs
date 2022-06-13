/// Represents a generic layout system, see the impl for more detail.
pub trait Layout {}
pub enum LayoutMixer {}

pub mod predefined {
    use super::*;

    ///
    /// 1 Example (Cli):
    /// ```rust
    /// use render::{RenderingCtx, RenderingMode};
    /// use layout::MeasurementLayout;
    ///
    /// let mut rctx = RenderingCtx::new(RenderingMode::Cli);
    ///
    /// let mut ml = MeasurementLayout::new(rctx);   // ownership transferred
    /// ml.draw(shape);
    ///
    /// rctx = ml.into_inner();                 // regain the ownership
    /// rctx.flush();
    /// ```
    ///
    /// 2 Example (Tui):
    /// ```rust
    /// use std::{thread::sleep, time::Duration};
    /// use render::{RenderingCtx, RenderingMode};
    /// use layout::MeasurementLayout;
    ///
    /// let mut rctx = RenderingCtx::new(RenderingMode::Tui);
    ///
    /// loop {
    ///     let mut ml = MeasurementLayout::new(rctx.resize()?); // ownership transferred
    ///     ml.draw(shape);
    ///
    ///     rctx = ml.into_inner();                         // regain the ownership
    ///     rctx.flush();
    ///
    ///     sleep(Duration::from_millis(200));
    /// }
    /// ```
    pub struct MeasurementLayout {}
    impl Layout for MeasurementLayout {}

    // //!   - Constraint based relative-layout system (similar to Android), allowing you to constraint shapes using virtual springs<br>
    // //!     Each shape has a `Vec<Edge>` for every [`Edge`](draw::Edge) it has on the left, bottom, top and right sides<br>
    // pub struct ConstraintLayout {}
}
