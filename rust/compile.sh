# Compile the rust workspace
cargo build --manifest-path="rust/Cargo.toml" --release

# Copy the compiled rust libraries to the ffi-libs storage directory.
# The .so files are called by Laravel.
cp rust/target/release/lib*_ffi.so storage/ffi-libs