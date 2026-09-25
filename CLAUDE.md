# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

Lib PHP 8.2 → 8.5 (`Helper\` → `src/`, `Test\` → `test/`), commentaires et messages en français.

Au besoin, lire :
- `.claude/docs/commands.md` — avant de lancer tests, php-cs-fixer ou phpstan : commandes (suite, fichier, test unique), compatibilité PHP 8.2 → 8.5 et CI.
- `.claude/docs/architecture.md` — avant de modifier ou d'utiliser une classe de `src/` : rôle de `PDOFactory`, `DbQuickUse`, `SqlRequest`, `PdoQueryable`, conventions (`$where`, alias) et pièges (statiques globales, SQL non échappé, cache, encodage).
- `.claude/docs/tests.md` — avant d'écrire un test : environnement SQLite, isolation des statiques, stub pour tester le trait.
