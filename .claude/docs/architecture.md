# Architecture

Trois briques indépendantes dans `src/` (namespace `Helper\`), aucune ne dépend d'une autre.

## PDOFactory

Constructeurs statiques de `PDO` : `mysql()`, `sqlite()` (défaut `:memory:`, lève `InvalidArgumentException` si le fichier n'existe pas), `oci()`, `pgsql()`. Tous passent par `configPdo()`.

- Défauts dans la constante `DEFAULT_ATTRIBUTES` : `ERRMODE_EXCEPTION`, `FETCH_OBJ`, `CASE_LOWER` (clés identiques sur tous les SGBD, ce qu'attend `DbQuickUse`). Chaque constructeur prend en dernier argument `array $attributes`, fusionné par-dessus avec `+` (pas `array_merge`, qui renumérote les clés int).
- `oci()` force `NLS_DATE_FORMAT` / `NLS_TIMESTAMP_FORMAT` et lève `RuntimeException` si impossible.

## DbQuickUse

CRUD rapide sur un `PDO` injecté : `select`, `selectOne`, `insertInto`, `update`, `delete`, `countElement`, `getLastPk`.

- `$where` : clé string → `col = ?` (valeur null → `col is null`) ; clé int → la valeur est un fragment SQL brut (string obligatoire, sinon `InvalidArgumentException`). Vide → `1 = 1`.
- `$fields` : clé string = alias (`['a' => 'col']` → `col as a`).
- Noms de table/colonne interpolés **sans échappement** : jamais de donnée utilisateur à ces endroits.
- `getLastPk()` fait un `max(pk)`, pas `lastInsertId()` ; lève `UnderflowException` si la table est vide.

## PdoQueryable (trait)

S'utilise dans une classe « requête » ; API `protected` : `setPdo()`, `setReqSql()`, `setCharset()`, puis `execute()`, `fetchAll()`, `fetchOne()` (tableaux associatifs), `getRowsAffected()`, `getDbDriver()`. Seul `getLastReqInfo()` est public.

- Statements préparés mis en cache par sha256 du SQL.
- Paramètres string convertis vers `$charset` (défaut UTF-8), résultats reconvertis en UTF-8 via `mb_detect_encoding`.
- Sur `PDOException`, `debugDumpParams()` est capturé dans `$lastDebugDumpParams` avant de relancer.
