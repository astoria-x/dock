# Changelog

All notable changes to Dock. Newest first. Versions follow semantic versioning; the current number is `Version` in `style.css`.

## 1.1.0 (2026-11-23)
- Added: WooCommerce support (`inc/woocommerce.php`, loaded only when WooCommerce is active). Declares theme support and the product gallery zoom, lightbox and slider. Shop pages render inside Dock's layout (`.container.flow.dock-shop`, one `<main>`, no sidebar).
- Added: `assets/css/woocommerce.css` maps WooCommerce's color variables to Dock role tokens and styles buttons, prices, sale badges, notices and forms. It loads, with WooCommerce's own CSS, only on shop, cart, checkout and account pages and on pages that use a WooCommerce shortcode. Filter: `dock_woocommerce_load_styles`.
- Added: `--color-success`, `--color-error` and `--color-sale` role tokens (all pass WCAG AA).
- Changed: `single.php` shows the post date on blog posts only, so custom post types get a clean single view.

## 1.0.1 (2026-11-18)
- Fixed: content and menu links showed in the text color instead of `--color-link` (the reset's `a:not([class])` color override was removed).
- Changed: `base.css` uses role and size tokens only. Added `--color-input-border`, `--color-on-link`, `--border-width-thick` and `--underline-offset`. No visual change.
- Changed (2026-11-23, no code change): `screenshot.png` no longer shows a version number; README points to `style.css` and this changelog; release checklist added to `CLAUDE.md`.

## 1.0.0 (2026-11-17)
- First release: slim classic parent theme with templates, a rem-based token system on Andy Bell's reset, accessibility basics, per-block CSS and front-end cleanup.
