# PDO-HELPER

Librairie PHP d'aide à l'utilisation de PDO et au requêtage des bases de données.

> Vous migrez depuis la v2 ? Consultez le [CHANGELOG](CHANGELOG.md) : la v3 contient des changements incompatibles.

## Prérequis

- PHP **8.2 à 8.5**
- extensions `pdo` (et le driver de votre SGBD : `pdo_mysql`, `pdo_pgsql`, `pdo_sqlite`, `pdo_oci`) et `mbstring`

## Installation

```bash
composer require fzed51/pdo-helper
```

## Utilisation

### PDOFactory

Fabrique statique pour créer des connexions PDO préconfigurées (mode exception, lignes en objets, colonnes en minuscules ; tout est surchargeable).

```php
use Helper\PDOFactory;

// MySQL
$pdo = PDOFactory::mysql('localhost', 'ma_base', 'user', 'password');
// avec port et charset personnalisés
$pdo = PDOFactory::mysql('localhost', 'ma_base', 'user', 'password', 3307, 'utf8mb4');

// SQLite (fichier ou mémoire)
$pdo = PDOFactory::sqlite(':memory:');
$pdo = PDOFactory::sqlite('/chemin/vers/base.db');

// PostgreSQL
$pdo = PDOFactory::pgsql('ma_base', 'localhost', 'user', 'password');

// Oracle (normalise automatiquement NLS_DATE_FORMAT et NLS_TIMESTAMP_FORMAT)
$pdo = PDOFactory::oci('MON_SID', 'user', 'password');
```

**Attributs par défaut** : `ERRMODE_EXCEPTION`, `FETCH_OBJ`, `CASE_LOWER` (noms de colonnes en minuscules quel que soit le SGBD, y compris Oracle).
Chaque constructeur accepte en dernier argument un tableau d'attributs PDO qui surcharge ces défauts :

```php
$pdo = PDOFactory::sqlite(':memory:', [
    PDO::ATTR_CASE               => PDO::CASE_NATURAL,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
```

---

### DbQuickUse

Classe utilitaire pour les opérations CRUD courantes. S'instancie avec un objet PDO.

```php
use Helper\DbQuickUse;

$db = new DbQuickUse($pdo);
```

**Lecture**

```php
// Plusieurs enregistrements
$users = $db->select(['id', 'nom', 'email'], 'users', ['actif' => 1]);

// Avec alias : ['alias' => 'champ']
$users = $db->select(['identifiant' => 'id', 'nom'], 'users');

// Limite (appliquée côté PHP après la requête, pas en SQL)
$users = $db->select(['id', 'nom'], 'users', [], 10);

// Un seul enregistrement (retourne null si absent)
$user = $db->selectOne(['id', 'nom', 'email'], 'users', ['id' => 42]);

// Comptage
$nb = $db->countElement('users', ['actif' => 1]);
```

**Écriture**

```php
// Insertion
$db->insertInto('users', ['nom' => 'Dupont', 'email' => 'dupont@example.com']);

// Mise à jour
$db->update('users', ['email' => 'nouveau@example.com'], ['id' => 42]);

// Suppression
$db->delete('users', ['id' => 42]);

// Dernier PK (MAX sur la colonne ; UnderflowException si la table est vide)
$lastId = $db->getLastPk('users', 'id');
```

**Format du tableau `$where`**

| Écriture | SQL généré |
|---|---|
| `['champ' => 'valeur']` | `champ = ?` |
| `['champ' => null]` | `champ IS NULL` |
| `['age > 18']` (clé entière) | clause brute verbatim |

Sans clause WHERE, la condition `1 = 1` est utilisée. Une clause sans clé doit être une chaine, sinon une `InvalidArgumentException` est levée.

> ⚠️ **Sécurité** : seules les **valeurs** sont passées en paramètres liés. Les noms de tables et de colonnes, ainsi que les clauses brutes (clé entière), sont insérés tels quels dans le SQL : n'y placez jamais de données provenant d'un utilisateur.

---

### PdoQueryable

Trait à inclure dans vos classes repository/DAO pour bénéficier d'un accès PDO avec cache de requêtes et gestion automatique de l'encodage.

```php
use Helper\PdoQueryable;

class UserRepository
{
    use PdoQueryable;

    public function __construct(\PDO $pdo)
    {
        $this->setPdo($pdo);
    }

    public function findAll(): array
    {
        $this->setReqSql('SELECT * FROM users');
        return $this->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $this->setReqSql('SELECT * FROM users WHERE id = ?');
        return $this->fetchOne([$id]);
    }

    public function create(string $nom, string $email): void
    {
        $this->setReqSql('INSERT INTO users (nom, email) VALUES (?, ?)');
        $this->execute([$nom, $email]);
        echo $this->getRowsAffected(); // 1
    }
}
```

**Comportements clés**

- Les `PDOStatement` sont mis en cache par hash SHA-256 du SQL (évite les `prepare()` répétés).
- L'encodage des paramètres en entrée et des données en sortie est converti automatiquement. Les charsets supportés sont : `UTF-8`, `CP1252`, `ISO-8859-15`, `ISO-8859-1`, `ASCII`. La sortie est toujours ramenée en `UTF-8` ; l'encodage cible en entrée se configure via `setCharset()`.
- Les méthodes du trait sont `protected` (à utiliser dans votre classe), sauf `getLastReqInfo()` qui est publique et retourne `['request' => string, 'params' => array]` (ou `null`) pour le débogage.
- `getRowsAffected()` retourne le nombre de lignes affectées par le dernier `execute()`.
