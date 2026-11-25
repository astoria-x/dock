# Changelog

All notable changes to Dock. Newest first. Versions follow semantic versioning; the current number is `Version` in `style.css`.

## 1.2.0 (2026-11-25)
- Added: automatic theme updates from GitHub (`inc/updater.php`). WordPress's own update check reads the version on Dock's `main` branch; newer versions show on Dashboard > Updates and Appearance > Themes, and the automatic-updates toggle works. Dock is kept out of WordPress.org's update check so a same-named theme there can never replace it. Source filter: `dock_update_source`. No requests on front-end page views.
- Added: `Update URI` header in `style.css`.

## 1.1.1 (2026-11-25)
Editor fixes found on the ORX site (WordPress 7.1.5, WooCommerce 11.3.1). No front-end change.
- Fixed: WooCommerce products opened in the classic editor. Every post type with `show_in_rest` and editor support now uses the block editor; WooCommerce's Product data box still shows as a panel. A child can opt a post type out with the `dock_use_block_editor` filter.
- Fixed: text was 1.6 times too big in the classic editor, which loads Dock's rem CSS with a 16px root. The classic editor now gets the 10px root on its own (`tiny_mce_before_init`), without touching the block editor.
- Added: new-site install check in CLAUDE.md and README (a post, a page and a product all open in the block editor).

## 1.1.0 (2026-11-23)
- Added: WooCommerce support (`inc/woocommerce.php`, loaded only when WooCommerce is active). Declares theme support and the product gallery zoom, lightbox and slider. Shop pages render inside Dock's layout (`.container.flow.dock-shop`, one `<main>`, no sidebar).
- Added: `assets/css/woocommerce.css` maps WooCommerce's color variables to Dock role tokens and styles buttons, prices, sale badges, notices and forms. It loads, with WooCommerce's own CSS, only on shop, cart, checkout and account pages and on pages that use a WooCommerce shortcode. Filter: `dock_woocommerce_load_styles`.
- Added: WooCommerce's cart scripts (and the jQuery they pull in) stay off non-shop pages. Order attribution keeps running. Filter: `dock_woocommerce_shop_scripts`.
- Added: `--color-success`, `--color-error` and `--color-sale` role tokens (all pass WCAG AA).
- Changed: `single.php` shows the post date on blog posts only, so custom post types get a clean single view.

## 1.0.1 (2026-11-18)
- Fixed: content and menu links showed in the text color instead of `--color-link` (the reset's `a:not([class])` color override was removed).
- Changed: `base.css` uses role and size tokens only. Added `--color-input-border`, `--color-on-link`, `--border-width-thick` and `--underline-offset`. No visual change.
- Changed (2026-11-23, no code change): `screenshot.png` no longer shows a version number; README points to `style.css` and this changelog; release checklist added to `CLAUDE.md`.

## 1.0.0 (2026-11-17)
- First release: slim classic parent theme with templates, a rem-based token system on Andy Bell's reset, accessibility basics, per-block CSS and front-end cleanup.
