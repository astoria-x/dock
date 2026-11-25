# CLAUDE.md: Dock

Rules for anyone (human or AI) working in this repository. Read before changing anything.

## What Dock is
Dock is the Astoria.X parent theme by Big Idea Media. Every Big Idea Media child theme and plugin plugs into it. It is a slim, builder-free **classic PHP theme**. Gutenberg is used only to edit content.

## Hard rules
1. **No page builders, build tools, frameworks, or jQuery.** Plain PHP, plain CSS. Dock ships no JavaScript. Do not dequeue jQuery either; plugins may need it.
2. **REM only for typography and spacing. No pixels.** `html` is 62.5%, so `1rem = 10px`; body text is `1.6rem`. Line heights are unitless. Media queries in rem use the browser default (16px), not 10px; write `48rem` for 768px.
3. **Variables first.** Every color, size and space comes from `assets/css/variables.css`. Components use role tokens (`--color-text`, `--color-link`), not palette tokens.
4. **Reusable classes only, never one-off CSS.** If you are about to style a single element, make a class or a token instead. Current utilities: `.container`, `.flow`, `.cluster`, `.screen-reader-text`.
5. **Accessibility is not optional.** Keep the skip link, `.screen-reader-text`, visible `:focus-visible` styles and the semantic landmarks (`header`, `nav[aria-label]`, `main#content`, `footer`). Text colors must pass WCAG AA.
6. **Security.** Escape all output late (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`). Sanitize all input early. Every form and state-changing request uses a nonce plus a capability check. Follow the WordPress Coding Standards.
7. **Performance.** No emoji scripts. Block CSS loads per block, only when a page uses that block. Flag any new asset, request or query that loads on every page before adding it.
8. **Scope.** Do not add features, templates or plugin dependencies that are not in the approved version scope. Propose them for the next version instead.
9. **Child-theme friendly.** Use `get_template_directory*()` for Dock's own files. Wrap replaceable functions in `function_exists`. Prefer hooks over hard-coded output.

## File map
| File | Purpose |
| --- | --- |
| `style.css` | Theme header only (name, version, author). No styles. |
| `functions.php` | Theme supports, menus, enqueue, block editor for every post type, editor root size, block CSS on demand, cleanup. |
| `header.php` / `footer.php` | Document shell, skip link, site header/footer, menus. |
| `index.php` | Fallback template and blog index. |
| `page.php`, `single.php`, `archive.php`, `search.php`, `404.php` | Standard templates. |
| `assets/css/variables.css` | Design tokens. |
| `assets/css/reset.css` | Andy Bell's modern reset + 62.5% root. |
| `assets/css/base.css` | Element defaults and reusable classes. |
| `inc/woocommerce.php` | WooCommerce support, shop layout wrapper, shop-only CSS loading. Loaded only when WooCommerce is active. |
| `assets/css/woocommerce.css` | WooCommerce colors mapped to role tokens, shop components. Shop pages only. |

CSS load order: `variables.css` > `reset.css` > `base.css` (set by enqueue dependencies). On shop pages: WooCommerce's CSS > `woocommerce.css`.

## Post types and WooCommerce
- Blog posts and any custom post type use `single.php` and `archive.php`. A child adds `single-{type}.php` or `archive-{type}.php` only when a post type needs its own layout.
- WooCommerce: style the store through tokens first. To change a WooCommerce template, copy it into the child theme's `woocommerce/` folder. Never load shop CSS on every page.

## Versioning
- Semantic versioning. Any change that ships in the theme folder (code, CSS, screenshot) is at least a patch bump.
- The version number lives in exactly two places: `Version` in `style.css` and `DOCK_VERSION` in `functions.php` (it busts the CSS cache). Nothing else carries the number: not the README, not `screenshot.png`, not comments. The README points to `style.css` and `CHANGELOG.md`.
- One topic per branch and pull request. `main` is always installable.

## Release checklist (every version bump, all in the same pull request)
1. `style.css` `Version` and `functions.php` `DOCK_VERSION` set to the new number.
2. `CHANGELOG.md`: a new entry at the top with the version, the date and what changed.
3. `screenshot.png` (1200 × 900) is current and shows no version number.
4. Version check: `grep -rnE '[0-9]+\.[0-9]+\.[0-9]+' --include='*.php' --include='*.css' --include='*.md' .` shows the old number nowhere except `@since` tags (they record when code was added and never change), older `CHANGELOG.md` entries and "Requires" lines.
5. After merge: tag the merge commit `vX.Y.Z`, build `dock-X.Y.Z.zip` from that tag (folder `dock/` at the zip root, no `.git`), put it in the project files, and delete the previous zip there.
6. Update the theme on the test site and any live sites, then confirm the Themes screen shows the new version.
7. Run the new-site install check below on the test site.

## New-site install check (every new site and every Dock update)
- Open a post, a page and (on WooCommerce sites) a product in wp-admin. All three open in the block editor, never the classic editor.
- Editor text is the same size as on the front end.
- The front end shows no PHP notices and no unexpected CSS or JS.

## Before every merge
- `php -l` on every PHP file.
- PHPCS with the `WordPress` ruleset passes.
- `grep -rnE '[0-9]px' assets/css` returns comments only.
- Test with Dock active and no plugins: no PHP notices, no console errors, no unexpected CSS or JS in the page source.

## Open decisions (do not decide in code)
- Hybrid theme (block template parts). Michael chose 2026-11-17: classic for v1, decide in v1.1.
