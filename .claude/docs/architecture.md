# Architecture

Quatre classes indépendantes dans `src/` (namespace `Helper\`), aucune ne dépend d'une autre.

## PDOFactory

Constructeurs statiques de `PDO` : `mysql()`, `sqlite()` (défaut `:memory:`, lève `InvalidArgumentException` si le fichier n'existe pas), `oci()`, `pgsql()`. Tous passent par `configPdo()` : `ERRMODE_EXCEPTION`, fetch mode et case.

- Case et fetch mode viennent des **statiques globales** `PDOFactory::$case` (défaut `CASE_UPPER`) et `$fetchMode` (défaut `FETCH_OBJ`). Elles s'appliquent à toutes les connexions créées ensuite et persistent entre tests.
- `oci()` force `NLS_DATE_FORMAT` / `NLS_TIMESTAMP_FORMAT` et lève `RuntimeException` si impossible.

## DbQuickUse

CRUD rapide sur un `PDO` injecté : `select`, `selectOne`, `insertInto`, `update`, `delete`, `countElement`, `getLastPk`.

- `$where` : clé string → `col = ?` (valeur null → `col is null`) ; clé int → la valeur est un fragment SQL brut (string obligatoire, sinon `InvalidArgumentException`). Vide → `1 = 1`.
- `$fields` : clé string = alias (`['a' => 'col']` → `col as a`).
- Noms de table/colonne interpolés **sans échappement** : jamais de donnée utilisateur à ces endroits.
- `getLastPk()` fait un `max(pk)`, pas `lastInsertId()` ; lève `UnderflowException` si la table est vide.

## SqlRequest

Builder fluide de **SELECT uniquement** : `select`/`addSelect`, `from`, `where`/`addWhere`, `sql()`. `select()`/`addSelect()`/`addWhere()` cumulent ; `from()`/`where()` remplacent.

## PdoQueryable (trait)

S'utilise dans une classe « requête » ; API `protected` : `setPdo()`, `setReqSql()`, `setCharset()`, puis `execute()`, `fetchAll()`, `fetchOne()` (tableaux associatifs), `getRowsAffected()`, `getDbDriver()`. Seul `getLastReqInfo()` est public.

- Statements préparés mis en cache par sha256 du SQL.
- Paramètres string convertis vers `$charset` (défaut UTF-8), résultats reconvertis en UTF-8 via `mb_detect_encoding`.
- Sur `PDOException`, `debugDumpParams()` est capturé dans `$lastDebugDumpParams` avant de relancer.
