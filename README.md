# PDO-HELPER

Librairie PHP 8.1+ d'aide à l'utilisation de PDO et au requêtage des bases de données.

## Installation

```bash
composer require fzed51/pdo-helper
```

## Utilisation

### PDOFactory

Fabrique statique pour créer des connexions PDO préconfigurées (mode exception activé, casse et mode de fetch configurables).

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

**Configuration globale** (à définir avant la création des connexions) :

```php
PDOFactory::$case      = PDO::CASE_LOWER;   // défaut : PDO::CASE_UPPER
PDOFactory::$fetchMode = PDO::FETCH_ASSOC;  // défaut : PDO::FETCH_OBJ
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

// Limite
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

// Dernier PK (MAX sur la colonne)
$lastId = $db->getLastPk('users', 'id');
```

**Format du tableau `$where`**

| Écriture | SQL généré |
|---|---|
| `['champ' => 'valeur']` | `champ = ?` |
| `['champ' => null]` | `champ IS NULL` |
| `['age > 18']` (clé entière) | clause brute verbatim |

Sans clause WHERE, la condition `1 = 1` est utilisée.

---

### SqlRequest

Générateur de requêtes SELECT avec une interface fluide (chaînable).

```php
use Helper\SqlRequest;

$sql = (new SqlRequest())
    ->select(['id', 'email' => 'users.email', 'nom'])
    ->from('users')
    ->where(['actif = 1', 'age > 18'])
    ->sql();
// select id, users.email as email, nom from users where actif = 1 and age > 18

// Ajout progressif
$req = (new SqlRequest())
    ->select('id')
    ->addSelect('users.email', 'email')
    ->from('users LEFT JOIN roles ON users.role_id = roles.id')
    ->where('actif = 1')
    ->addWhere('roles.name = "admin"');

$sql = $req->sql();
```

`select()` et `from()` acceptent une chaîne ou un tableau. `where()` remplace les clauses existantes ; `addWhere()` en ajoute une.

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
- `getLastReqInfo()` retourne `['request' => string, 'params' => array]` pour le débogage.
- `getRowsAffected()` retourne le nombre de lignes affectées par le dernier `execute()`.
