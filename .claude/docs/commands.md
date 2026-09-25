# Commandes

## Tests (phpunit 9)

```bash
composer test                                   # toute la suite (timeout Composer désactivé)
vendor/bin/phpunit test/PdoQueryableTest.php    # un fichier
vendor/bin/phpunit --filter testFetchAll        # un test
```

Config `phpunit.xml` : bootstrap `vendor/autoload.php`, suite = `test/*Test.php`, mode strict (sortie pendant un test = test « risky »).

## Qualité

```bash
composer check          # phpcs puis phpstan
vendor/bin/phpcs        # PSR-2 sur src/ et test/
vendor/bin/phpcbf       # correction auto du style
vendor/bin/phpstan analyse   # niveau 6 sur src/ et test/
```

Dans `test/`, phpcs tolère les méthodes non camelCase et plusieurs classes par fichier.

## Environnement local (PHP 8.5)

Les dépendances verrouillées datent de 2023 : chaque commande affiche des dizaines de `Deprecated` et des traces Xdebug. Pour une sortie lisible :

```bash
php -d error_reporting="E_ALL & ~E_DEPRECATED" -d xdebug.mode=off vendor/bin/phpunit
```

phpstan 1.10.3 plante sous PHP 8.5 : son résultat est inexploitable tant que `composer.lock` n'est pas mis à jour. Ne pas conclure qu'il n'y a pas d'erreur de typage.
