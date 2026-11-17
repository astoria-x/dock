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
| `functions.php` | Theme supports, menus, enqueue, block CSS on demand, cleanup. |
| `header.php` / `footer.php` | Document shell, skip link, site header/footer, menus. |
| `index.php` | Fallback template and blog index. |
| `page.php`, `single.php`, `archive.php`, `search.php`, `404.php` | Standard templates. |
| `assets/css/variables.css` | Design tokens. |
| `assets/css/reset.css` | Andy Bell's modern reset + 62.5% root. |
| `assets/css/base.css` | Element defaults and reusable classes. |

CSS load order: `variables.css` > `reset.css` > `base.css` (set by enqueue dependencies).

## Versioning
- Semantic versioning. Bump `Version` in `style.css` **and** `DOCK_VERSION` in `functions.php` together (it busts the CSS cache).
- One topic per branch and pull request. `main` is always installable.

## Before every merge
- `php -l` on every PHP file.
- PHPCS with the `WordPress` ruleset passes.
- `grep -rnE '[0-9]px' assets/css` returns comments only.
- Test with Dock active and no plugins: no PHP notices, no console errors, no unexpected CSS or JS in the page source.

## Open decisions (do not decide in code)
- Hybrid theme (block template parts). Michael chose 2026-11-17: classic for v1, decide in v1.1.
