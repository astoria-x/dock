# Dock

The Astoria.X parent theme by Big Idea Media. Slim, builder-free, classic PHP. Gutenberg edits content; PHP templates handle layout.

**Version:** see `Version` in [style.css](style.css) and [CHANGELOG.md](CHANGELOG.md) · **Requires:** WordPress 6.5+, PHP 8.1+ · **License:** GPL-2.0-or-later

## What's inside
- Classic templates: `index`, `page`, `single`, `archive`, `search`, `404`, plus `header` and `footer`.
- A variable-based CSS system in rem (`1rem = 10px`), built on Andy Bell's modern CSS reset.
- Accessibility basics: skip link, `.screen-reader-text`, visible focus styles, semantic landmarks.
- Lean front end: no JavaScript, no emoji scripts, core block CSS only for blocks on the page.
- Block editor everywhere: products and every custom post type edit in the block editor, never the classic editor.
- Works with any post type: blog posts and custom post types use the Single and Archive templates.
- WooCommerce ready: shop pages use Dock's layout and tokens; shop CSS loads only on shop pages.
- Theme supports: title tag, featured images, HTML5 markup, primary and footer menus, editor styles, responsive embeds.

## What's not inside (on purpose)
No page builder, build step, framework, jQuery, widgets, customizer options or bundled plugins.

## Install
1. Download the repo as a zip (or `git clone` into `wp-content/themes/dock`).
2. In WordPress, go to **Appearance > Themes > Add New > Upload Theme**, upload the zip, and activate.
3. Assign menus under **Appearance > Menus** to *Primary menu* and *Footer menu*.
4. Check: open a post, a page and (on store sites) a product. All three should open in the block editor.

## Updates
Dock updates itself like any other theme. New versions on GitHub show on **Dashboard > Updates** and **Appearance > Themes**; click Update, or turn on automatic updates for Dock on the Themes screen.

Client sites should run a child theme of Dock rather than Dock itself, so updates to Dock never overwrite client work.

## Customizing
Override the tokens in `assets/css/variables.css` from a child theme stylesheet, for example:

```css
:root {
	--color-link: #0b6e4f;
	--font-body: "Inter", system-ui, sans-serif;
}
```

## Contributing
Read [CLAUDE.md](CLAUDE.md) first. It holds the rules for this repo.
