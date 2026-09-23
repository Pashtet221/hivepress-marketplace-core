# Codex access rules — HivePress Marketplace

## Writable project code
Codex may read and modify files ONLY in:

- `themes/site_test/`

This is the custom project theme and is the only location where implementation changes should be made unless the user explicitly authorizes another path.

## Read-only reference code
Codex may inspect, search, and analyze the following paths, but MUST NOT modify them:

- `themes/listinghive/`
- `plugins/hivepress/`
- `plugins/hivepress-*/`

These directories are third-party HivePress/ListingHive dependencies and are included only so Codex can understand models, hooks, filters, templates, APIs, and integration behavior.

## Forbidden
All other `wp-content` directories and files are outside the project scope. Do not read, edit, generate patches for, or rely on them unless the user explicitly grants access.

## Implementation policy
When a requested change depends on HivePress or ListingHive:

1. Inspect the relevant read-only dependency code.
2. Identify supported hooks, filters, template overrides, APIs, models, or extension points.
3. Implement the change in `themes/site_test/`.
4. Never patch HivePress plugins or ListingHive directly.
5. If the requested behavior cannot be implemented safely from the custom theme, explain the limitation before changing any read-only dependency.
