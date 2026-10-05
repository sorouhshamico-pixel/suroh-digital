# Codex continuation prompt

Continue development of the Sorouh Digital project as a production-grade application.

First:
1. Read `AGENTS.md`.
2. Read `docs/AI_HANDOFF.md` completely.
3. Inspect the full repository, README, git status/log, routes, database schema, authentication, public assets, tracking and SEO implementation.
4. Do not assume the documentation is perfectly current; verify against the code.

Then:
- Identify incomplete P0 launch blockers from `docs/AI_HANDOFF.md`.
- Implement them in priority order without breaking existing functionality.
- Preserve the approved Sorouh Digital RTL black/gold brand, first-party tracking, UTM attribution, admin CMS/CRM, and technical SEO.
- Add or improve tests/checks where practical.
- Run PHP syntax checks and route smoke tests.
- Keep secrets out of Git.
- Use focused commits with clear messages.
- Update `docs/AI_HANDOFF.md` when verified architecture, routes, schema or deployment requirements change.

Do not stop at cosmetic work. The target is a deployable, secure, fast, SEO-ready production platform for `digital.suroohalshami.com`.
