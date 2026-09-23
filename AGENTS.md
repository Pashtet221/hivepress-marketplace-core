## Working theme

The active project theme is:

- `themes/listinghive/`

Codex may read and modify files inside this directory.

All project implementation changes must be made inside
`themes/listinghive/` unless the user explicitly instructs otherwise.

## Read-only dependencies

Codex may inspect, search and analyze:

- `plugins/hivepress/`
- `plugins/hivepress-*/`

These directories are third-party dependencies and MUST NOT be modified.

When implementing HivePress-related functionality:

1. Inspect the relevant HivePress source code.
2. Find existing hooks, filters, APIs and extension points.
3. Implement project-specific changes inside `themes/listinghive/`.
4. Never patch HivePress or HivePress extensions directly.

## Scope

Everything outside `themes/listinghive/` is read-only by default.