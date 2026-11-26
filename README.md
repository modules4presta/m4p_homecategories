# Home Catalogue for PrestaShop 8 & 9

**The catalogue departments on the home page – every department with a thumbnail and its subcategories, so a visitor reaches the right shelf in one click.**

> **Meta description (155 chars):** PrestaShop home page catalogue: departments with thumbnails, subcategory links and product counts on phones. Free, MIT.

---

## Why the catalogue on the home page matters for your store

A shop with a wide range loses visitors who cannot see at once what it sells. A menu hides the
departments behind a click, and a few hand-picked tiles on the home page get out of date with every
change in the catalogue.

A section built from the category tree itself always shows the current departments and their
subcategories, without editing the home page.

- **Always current** – the departments come from the category tree, in its order
- **One click to the shelf** – each department lists its subcategories as links
- **Made for phones** – a compact list with the product count of each department

## What the module does

The module adds a section to `displayHome` that lists every active subcategory of one parent
category as a department: its image as a thumbnail, its name and its active subcategories as links.
It reads the catalogue and does not change it.

### Key features

- **Departments** – the active subcategories of the chosen parent, in their catalogue order, in three columns (two on tablets)
- **Thumbnail** – the category thumbnail, or the category cover without one, in the smallest square category image type of the shop that is at least 160 px wide; WebP when the shop generates WebP thumbnails, with the JPEG as the fallback. A department without an image shows no thumbnail (an empty frame on phones)
- **Phones** – a disclosure list with the product count of each department ("3 products") and an "Everything in this department" link; the first department is open
- **Base layout** – `views/css/front.css`, loaded on the home page only

### Empty categories, uninstalling

A department with no active subcategories shows its name and thumbnail only. Uninstalling the
module deletes its one setting; the catalogue stays as it was.

## Compatibility

| | |
|---|---|
| PrestaShop | 8.0 – 9.x |
| PHP | 7.4+ |
| Requirements | none |
| Multistore | The setting is saved in the shop context of the back office |
| Themes | Works with any theme – a template and a base layout ship in the module; style the `.m4p-hc__*` classes to match the theme |

The module performs no core overrides and adds no database tables; its setting is one row in
`configuration` (`M4P_HC_PARENT`).

## Installation

1. Upload and install the module from **Modules → Module Manager**.
2. Open **Configure** and choose the parent category.
3. In **Design → Positions → displayHome** place the module where the section belongs.

After installation the parent category is the home category of the shop, so its top-level
categories become the departments.

## Configuration options

| Setting | Description |
|---|---|
| **Parent category** | Its subcategories become the departments. Usually the home category of the shop. |

## Frequently asked questions

**Do I have to update the section when the catalogue changes?**
No. The departments and their subcategories are read from the category tree on every page view.

**Where does the thumbnail come from?**
From the category thumbnail set in **Catalog → Categories**, or the category cover image when there is no thumbnail.

**Can I undo the installation?**
Yes. Uninstalling deletes the module setting; categories and images are not touched.

---

**Keywords:** PrestaShop home page categories, category tiles, catalogue departments, subcategory links, PrestaShop home page module

## License

MIT – see [LICENSE](LICENSE). Free to use commercially, fork and modify; keep the copyright notice.

## Contributing

Bug reports and pull requests are welcome – see [CONTRIBUTING.md](CONTRIBUTING.md). For security
issues, follow [SECURITY.md](SECURITY.md) instead of opening a public issue.

---

Built by [Nice Code](https://nice-code.com/pl/produkty/sklep-b2b-prestashop) – we build and maintain PrestaShop stores.

© Nice Code sp. z o.o. (Modules4Presta) – released under the MIT license.
