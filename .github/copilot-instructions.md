# GitHub Copilot repository instructions

This is the Sorouh Digital production application.

Always use `docs/AI_HANDOFF.md` as the canonical project specification and handoff document.

When generating or modifying code:
- Match the existing PHP/MVC-style architecture and coding conventions.
- Preserve Arabic RTL UI and the approved black/gold Sorouh Digital design language.
- Preserve UTM attribution, analytics events and lead tracking.
- Use PDO prepared statements and server-side validation.
- Never place secrets, passwords, API keys or production data in source.
- Maintain SEO metadata, canonicals, schema, sitemap and clean semantic HTML.
- Keep JS/CSS lightweight and mobile-first.
- Respect `prefers-reduced-motion`.
- Avoid framework migrations unless explicitly requested.
- Never fabricate testimonials, clients, metrics or business claims.
- Validate syntax and existing routes after edits.

If implementation and documentation conflict, inspect the actual repository, preserve working behavior, and update `docs/AI_HANDOFF.md` after verified changes.
