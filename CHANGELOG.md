# Changelog

## 3.0.0 (non publiée)

### Changements incompatibles

- PHP **8.2 à 8.5** requis (`>=8.2 <8.6`).
- `SqlRequest` supprimé : utiliser `DbQuickUse` ou écrire le SQL directement.
- `PDOFactory` :
  - statiques `PDOFactory::$case` et `PDOFactory::$fetchMode` supprimées ; passer les attributs PDO en dernier argument de `mysql()`, `sqlite()`, `oci()` ou `pgsql()` :
    ```php
    PDOFactory::sqlite(':memory:', [PDO::ATTR_CASE => PDO::CASE_UPPER]);
    ```
  - nouveaux défauts : `PDO::CASE_LOWER` (au lieu de `CASE_UPPER`) et `PDO::FETCH_ASSOC` (au lieu de `FETCH_OBJ`).
  - paramètres de `mysql()` typés nativement.
- `DbQuickUse` : une clause `$where` sans clé qui n'est pas une chaine lève `InvalidArgumentException`.
- `PdoQueryable` :
  - `getDbDriver()` lève `RuntimeException` si PDO n'est pas initialisé ;
  - si l'encodage d'une chaine est indétectable, elle est laissée telle quelle ; un échec de conversion lève `RuntimeException`.

### Outillage

- CI GitHub Actions sur PHP 8.2, 8.3, 8.4 et 8.5.
- PHP_CodeSniffer (PSR-2) remplacé par PHP-CS-Fixer (PSR-12).
- phpstan 2, niveau 9.
