# 🛠️ Commandes Utiles - Home Harmony

Ce document regroupe les commandes essentielles pour le développement et la maintenance du projet.

## 🐘 PHP & Composer

### Installation des dépendances
```bash
composer install
```

### Mise à jour de l'autoload (si ajout de classes)
```bash
composer dump-autoload
```

## 🔍 Qualité du Code & Analyse

### Analyse statique (PHPStan)
Vérifie les erreurs de typage et la logique du code (Niveau 7 actuel).
```bash
composer phpstan
```

### Détection de "Code Sale" (PHPMD)
Identifie les structures trop complexes, les variables mal nommées ou le code inutilisé (Configuré via `phpmd.xml`).
```bash
composer phpmd
```

### Tests unitaires (PHPUnit)
Vérifie le bon fonctionnement des modèles et de la logique métier.
```bash
composer test
```

### Formatage du code (PHP-CS-Fixer)
Applique les standards PSR-12 automatiquement.
```bash
# Appliquer les corrections
composer format

# Vérifier sans modifier
composer format-check
```

## 🎨 CSS & Design (Tailwind CSS v4)

### Compilation unique
```bash
npm run build-css
```

### Mode "Watch" (développement en temps réel)
```bash
npm run watch-css
```

## 🗄️ Base de données (SQLite)

La base de données se trouve dans `data/harmony.sq3`.
Pour l'explorer, vous pouvez utiliser **SQLite Browser** ou l'extension VS Code **SQLite Viewer**.

---
*Note : Assurez-vous d'avoir PHP 8.3+ et Node.js installés sur votre machine.*
