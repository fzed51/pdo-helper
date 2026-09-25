# Changelog

Format inspiré de [Keep a Changelog](https://keepachangelog.com/fr/1.1.0/) ; le projet suit le [versionnage sémantique](https://semver.org/lang/fr/).

## [3.0.0] - 2026-09-26

### Changements incompatibles

- PHP **8.2 à 8.5** requis (`>=8.2 <8.6`).
- Extension `mbstring` désormais requise (`ext-mbstring`, utilisée par `PdoQueryable`).
- `SqlRequest` supprimé : utiliser `DbQuickUse` ou écrire le SQL directement.
- `PDOFactory` :
  - statiques `PDOFactory::$case` et `PDOFactory::$fetchMode` supprimées ; passer les attributs PDO en dernier argument de `mysql()`, `sqlite()`, `oci()` ou `pgsql()` :
    ```php
    PDOFactory::sqlite(':memory:', [PDO::ATTR_CASE => PDO::CASE_UPPER]);
    ```
  - casse par défaut : `PDO::CASE_LOWER` (au lieu de `CASE_UPPER`) ; le fetch mode par défaut reste `PDO::FETCH_OBJ`.
  - paramètres de `mysql()` typés nativement.
- `DbQuickUse` : une clause `$where` sans clé qui n'est pas une chaine lève `InvalidArgumentException`.
- `PdoQueryable` :
  - `getDbDriver()` lève `RuntimeException` si PDO n'est pas initialisé ;
  - si l'encodage d'une chaine est indétectable, elle est laissée telle quelle ; un échec de conversion lève `RuntimeException`.

### Outillage

- CI GitHub Actions sur PHP 8.2, 8.3, 8.4 et 8.5.
- PHP_CodeSniffer (PSR-2) remplacé par PHP-CS-Fixer (PSR-12).
- phpstan 2, niveau 9.
- PHPUnit 9 → 11.

### Corrections

- `PdoQueryable::getLastReqInfo()` retournait toujours `null` : la dernière requête exécutée n'était jamais mémorisée.
- `DbQuickUse` fonctionne avec une connexion `PDOFactory` sans réglage préalable : l'ancien défaut `CASE_UPPER` rendait les clés `['nom']` introuvables.

## Versions antérieures

Voir les [tags du dépôt](https://github.com/fzed51/pdo-helper/tags) (jusqu'à `v2.1.0`).

[3.0.0]: https://github.com/fzed51/pdo-helper/compare/v2.1.0...v3.0.0
