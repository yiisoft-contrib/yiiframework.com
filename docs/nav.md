# Navigation and titles

## Information architecture

- Every public page must be discoverable from primary navigation or a page reachable through it.
- Group destinations by user intent, not implementation ownership.
- Keep frequent destinations at the top level; place related or less frequent ones in short, labeled groups.
- Avoid duplicate routes to the same information.
- Prefer framework routes for internal links.

## Navigation

- Clearly distinguish navigation, actions, and non-clickable group labels.
- Indicate the current destination and its parent without changing layout geometry.
- Open dropdowns directly below their trigger.
- Keep utility controls visually separate from content navigation.
- Preserve meaning, order, and accessibility across desktop and mobile layouts.

## Context selectors

- Use the context row only for controls that change the current content representation, such as version or language, and for relevant administrative actions.
- Keep selectors ordered newest to oldest and default to the newest available version.
- Preserve the current page or filter when switching representations where possible.
- Do not use context navigation as a decorative title bar.

## Page and document titles

- Use one semantic `<h1>` when it helps users identify the page.
- Avoid repeating the same title in navigation, section chrome, and content.
- Let hierarchy, spacing, and separators organize the page.
- Build the HTML title from specific to general: page, section when useful, then site.
- Keep title parts unique, concise, and meaningful outside the page context.
