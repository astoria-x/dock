# Changelog

All notable changes to Dock. Newest first. Versions follow semantic versioning; the current number is `Version` in `style.css`.

## 1.0.1 (2026-11-18)
- Fixed: content and menu links showed in the text color instead of `--color-link` (the reset's `a:not([class])` color override was removed).
- Changed: `base.css` uses role and size tokens only. Added `--color-input-border`, `--color-on-link`, `--border-width-thick` and `--underline-offset`. No visual change.
- Changed (2026-11-23, no code change): `screenshot.png` no longer shows a version number; README points to `style.css` and this changelog; release checklist added to `CLAUDE.md`.

## 1.0.0 (2026-11-17)
- First release: slim classic parent theme with templates, a rem-based token system on Andy Bell's reset, accessibility basics, per-block CSS and front-end cleanup.
