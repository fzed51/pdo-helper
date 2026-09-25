# Tests

- Namespace `Test\`, fichiers `test/*Test.php`, un fichier par classe de `src/`.
- Tout tourne sur SQLite `:memory:` : aucune base externe, seul `PDOFactoryTest` crée puis supprime un `./test.db` temporaire.
- Les tests qui passent par `PDOFactory` fixent eux-mêmes `PDOFactory::$case` / `$fetchMode`, car ces statiques persistent d'un test à l'autre. Ex. `DbQuickUseTest::setUp()` force `CASE_LOWER`.

## Tester le trait PdoQueryable

Ses méthodes sont `protected` : `test/Stub/PdoQueryableStub.php` (`Test\Stub\PdoQueryableStub`) `use` le trait avec des alias (`fetchAll as TfetchAll`) et ré-expose des méthodes publiques typées. Son constructeur crée la base SQLite `test` (2 lignes). Pour tester une nouvelle méthode du trait, y ajouter l'alias et la méthode publique correspondante.
