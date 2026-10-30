# Sponsor artwork

Artwork sources verified on 30 October 2026. These marks belong to their owners
and acknowledge the matching public sponsors. Source geometry is preserved;
CSS presents the page's artwork in monochrome in both themes, including
Skillshare, without changing the original files. JetBrains retains its neutral
white-on-black presentation.

## Official SVG artwork

- Skillshare light/dark artwork comes from its [brand guidelines](https://partners.skillshare.com/design),
  using the supplied `SK_Primary_logo_TM_blk_grn.svg` and `SK_Primary_logo_TM_white_grn.svg` files unchanged.
- Craft CMS light/dark artwork comes from its [brand resources](https://craftcms.com/brand-resources),
  using black/white SVGs from the [official logo archive](https://craftcms.com/assets/craft-cms-logo-assets.zip) unchanged.
- The HumHub symbol is served by its [official documentation](https://docs.humhub.org/img/logo.svg), unchanged.
- The Betteridge monogram is its [official website's pinned-tab artwork](https://www.betteridge.com/_ui/responsive/betteridge/favicon/safari-pinned-tab.svg), unchanged.
- The Icons8 symbol is the unchanged inline SVG in the `i8-header-logo` link on
  its [official developer portal](https://developers.icons8.com/).
- `github.svg` is the unchanged `GitHub Logos/SVG/GitHub_Lockup_Black.svg`
  in GitHub's [official archive](https://brand.github.com/GitHub_Logos.zip),
  linked from its [logo guidelines](https://brand.github.com/foundations/logo).
- `openai.svg` comes from `OpenAI-logos(new)/SVGs/OpenAI-black-wordmark.svg`
  in OpenAI's [official archive](https://cdn.openai.com/brand/OpenAI-Logos-2025.zip),
  associated with its [brand guidelines](https://openai.com/brand/).
  Only the root dimensions/viewBox crop outer whitespace; paths are unchanged.
- `anthropic.svg` is the wordmark SVG (`logoWordmark`) in the
  [official Anthropic homepage](https://www.anthropic.com/).
  `sentry.svg` is the `0 0 200 44` logo SVG in the
  [official Sentry branding page](https://sentry.io/branding/).
  Both are standalone extractions with page-only class/ARIA removed and
  unchanged paths. The default SVG `currentColor` renders black.

## Faithful vector traces

The human explicitly requested matching SVGs where original SVGs could not be
found. These files are traces of verified artwork, not original brand SVGs.
No replacement fonts or generic symbols are used.

- `dmstr.svg` traces the shield and asterisk in the
  [diemeisterei Open Collective profile](https://opencollective.com/diemeisterei.json)
  [avatar](https://opencollective-production.s3.us-west-1.amazonaws.com/6f1fe3c0-66b7-11e9-8421-95bfcf15aa5b.png).
  The identical mark belongs to the [official dmstr GitHub organization](https://github.com/dmstr).
  The old website redirects to Herzog Kommunikation, so its different logo is
  deliberately not used. The shield is traced in black with transparent
  negative space, with empty avatar margins removed.
- `dudesquare.svg` traces the two-line lettering in the
  [official DudeSquare website header PNG](https://www.dudesquare.nl/images/dudesquare.png).
  The [company's own disclaimer](https://www.dudesquare.nl/disclaimer/) identifies
  this website as DudeSquare B.V. The trace preserves letter shapes and layout,
  with empty margins removed.
- Both raster contours use Potrace 1.16, threshold 128, `alphamax=0` for
  dmstr’s asterisk and `alphamax=1` for its curved shield and for DudeSquare’s
  smooth lettering,
  `opttolerance=0.2`, and two pixels of transparent padding.
- `onetwist.svg` manually traces the prominent wire-sphere vertices and edges in
  the [official Onetwist touch icon](https://www.onetwist.com/apple-touch-icon.png).
  The same sphere appears in the
  [Onetwist Software Open Collective avatar](https://opencollective.com/onetwist-software.json).
  The trace omits the gradient/stars background and faint rear mesh for a clear
  transparent monochrome mark; it retains the distinctive front mesh and outline,
  with a thicker stroke for legibility at its 44-pixel display size.
  The unrelated commented-out preloader SVG on its homepage is not used.

Toni Schreiber is featured by contribution rank and retains a text label: the
verified public Open Collective profile provides no website or artwork.
Sponsor order, public identity labels, contribution dates and grouping are
independent of artwork availability.
