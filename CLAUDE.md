# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

Lib PHP ^8.1 (`Helper\` → `src/`, `Test\` → `test/`), commentaires et messages en français.

Au besoin, lire :
- `.claude/docs/commands.md` — avant de lancer tests, phpcs ou phpstan : commandes (suite, fichier, test unique), et contournements indispensables sous PHP 8.5 (sortie bruitée, phpstan qui plante).
- `.claude/docs/architecture.md` — avant de modifier ou d'utiliser une classe de `src/` : rôle de `PDOFactory`, `DbQuickUse`, `SqlRequest`, `PdoQueryable`, conventions (`$where`, alias) et pièges (statiques globales, SQL non échappé, cache, encodage).
- `.claude/docs/tests.md` — avant d'écrire un test : environnement SQLite, isolation des statiques, modèle de classe anonyme pour tester le trait.
