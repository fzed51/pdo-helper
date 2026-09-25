# Tests

- Namespace `Test\`, fichiers `test/*Test.php`, un fichier par classe de `src/`.
- Tout tourne sur SQLite `:memory:` : aucune base externe, seul `PDOFactoryTest` crée puis supprime un `./test.db` temporaire.
- Les tests qui passent par `PDOFactory` fixent eux-mêmes `PDOFactory::$case` / `$fetchMode`, car ces statiques persistent d'un test à l'autre. Ex. `DbQuickUseTest::setUp()` force `CASE_LOWER`.

## Tester le trait PdoQueryable

Ses méthodes sont `protected` : les tests utilisent une classe anonyme qui `use` le trait avec des alias et ré-expose des méthodes publiques. Le constructeur y crée la base SQLite et assigne `$this->pdo` directement.

```php
return new class {
    use PdoQueryable {
        fetchAll as TfetchAll;
    }
    public function fetchAll(array $param = []): array
    {
        return $this->TfetchAll($param);
    }
};
```

Modèle complet : `getInstancePdoQueryable()` dans `test/PdoQueryableTest.php` ; y ajouter l'alias de toute nouvelle méthode testée.
