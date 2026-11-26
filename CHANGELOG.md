# Changelog

All notable changes to this project are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the project uses
[semantic versioning](https://semver.org/).

## [2.0.3] - 2026-11-26

### Added

- Home page section on `displayHome` with the catalogue departments: every active subcategory of one parent
  category (setting "Parent category", by default the shop's home category), with its active subcategories
  as links, in three columns (two on tablets).
- Department image: the category thumbnail, or the category cover without one, in the smallest square
  category image type of the shop at least 160 px wide, so it stays sharp at 88 px on 2x screens. When the
  shop generates WebP thumbnails, the image comes as WebP with the JPEG as the fallback.
- On phones the departments are a disclosure list with the product count of each department and an
  "Everything in this department" link; the first one is open.
- Base layout in `views/css/front.css`, loaded on the home page only; a theme restyles the `.m4p-hc__*` classes.
- English and Polish catalogues.
