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
composer check               # php-cs-fixer check puis phpstan
composer fix                 # applique le style PSR-12 (php-cs-fixer fix)
vendor/bin/phpstan analyse   # niveau défini dans phpstan.neon sur src/ et test/
```

Style : PHP-CS-Fixer, règles `@PSR12`, config `.php-cs-fixer.dist.php` (src/ et test/).

## Compatibilité PHP 8.2 → 8.5

- Contrainte `"php": ">=8.2 <8.6"` ; `config.platform.php = 8.2.0` : le `composer.lock` est résolu pour 8.2, donc installable sur toutes les versions supportées. Ne pas utiliser de syntaxe ou de fonction postérieure à 8.2.
- CI : `.github/workflows/ci.yml` lance php-cs-fixer, phpstan et phpunit sur 8.2, 8.3, 8.4 et 8.5 (push sur `main`/`develop`, PR).
- En local (PHP 8.5 + Xdebug), préfixer par `XDEBUG_MODE=off` pour accélérer les commandes.
