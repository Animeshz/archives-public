use std::fmt::Debug;
use std::io::{Write, Result};
use std::ops::Index;

use crossterm::terminal;

use crate::draw::Cell;

/// Represents rows & cols in a 2d plane
#[derive(Copy, Clone, Debug)]
pub struct Dimension {
    pub rows: usize,
    pub cols: usize,
}

/// Represents a 2d plane
#[derive(Clone, Debug)]
pub struct Matrix<T: Copy + Debug + Default> {
    /// Dimension of the matrix
    pub dim: Dimension,
    /// 2D data of the matrix in row-major order
    pub data: Vec<T>,
}

impl<T: Copy + Debug + Default> Matrix<T> {
    /// Constructs a new Matrix
    ///
    /// # Example
    /// ```rust
    /// let matrix = Matrix::new(Dimension { rows: 10, cols: 15 });
    /// ```
    pub fn new(dim: Dimension) -> Self {
        Self {
            dim,
            data: vec![T::default(); dim.rows * dim.cols],
        }
    }

    /// Resizes the internal data to match the given `dim`.
    pub fn resize(&mut self, dim: Dimension) {
        self.dim = dim;
        self.data.resize(dim.rows * dim.cols, T::default());
    }

    /// Resets the internal data to defaults.
    pub fn reset(&mut self) {
        let default = T::default();
        for item in &mut self.data {
            *item = default;
        }
    }
}

impl<T: Copy + Debug + Default> Index<usize> for Matrix<T> {
    type Output = [T];

    /// Allows indexing of the [`Matrix::data`] as if it was a 2d array.
    ///
    /// # Example:
    /// ```rust
    /// let matrix = Matrix::new(Dimension { x: 10, y: 10 })
    /// matrix[2][3] = 5;
    /// ```
    fn index(&self, index: usize) -> &Self::Output {
        &self.data[index * usize::from(self.dim.cols)..(index + 1) * usize::from(self.dim.cols)]
    }
}

/// Rendering Context is a context where buffer lies and unicode [`draw::Cell`](crate::draw::Cell)s
/// can be drawn on (usually by [`layout::Layout`](crate::layout::Layout)s).
pub struct RenderingCtx {
    mode: RenderingMode,
    buffer: [Matrix<Cell>; 2],
    buffer_index: usize,
}

pub enum RenderingMode {
    Cli,
    Tui,
}

impl RenderingCtx {
    /// Constructs a new RenderingCtx.
    pub fn new(mode: RenderingMode) -> Result<Self> {
        let zero = Dimension { rows: 0, cols: 0 };
        Ok(Self {
            mode,
            buffer: [Matrix::new(zero), Matrix::new(zero)],
            buffer_index: 0,
        }.resize()?)
    }

    /// Resizes this RenderingCtx to the correct terminal size.
    pub fn resize(mut self) -> Result<Self> {
        let (rows, cols) = terminal::size()?;
        let dim = Dimension { rows: rows.into(), cols: cols.into() };

        self.buffer[0].resize(dim);

        if matches!(self.mode, RenderingMode::Tui) {
            self.buffer[1].resize(dim);
        }
        Ok(self)
    }

    /// Flushes the buffer to the `write`.
    pub fn flush<T: Write>(&mut self, write: T) {
        // TODO: Flush to the write

        if matches!(self.mode, RenderingMode::Tui) {
            self.buffer_index = 1 - self.buffer_index;
        }
        self.buffer[self.buffer_index].reset();
    }
}
