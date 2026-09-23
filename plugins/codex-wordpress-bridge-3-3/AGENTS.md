# Codex WordPress Bridge

## Purpose

This plugin exposes a narrow authenticated REST API for WordPress content management.

## Security rules

- Never add arbitrary SQL execution.
- Never expose user passwords, application passwords, salts or API keys.
- Never add endpoints for plugin/theme editing.
- Never add permanent deletion without an explicit separate design.
- All write routes require the `use_codex_bridge` capability.
- Continue using WordPress and ACF APIs instead of direct postmeta manipulation.
- New content remains draft unless publishing is explicitly enabled by filter.

## Compatibility

- WordPress 6.0+
- PHP 7.4+
- ACF is optional; ACF endpoints return 501 when unavailable.
