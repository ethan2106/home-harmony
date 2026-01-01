# ✨ Harmony - Gestionnaire de Tâches Ménagères

**Harmony** est une application web moderne conçue pour transformer les corvées ménagères en une expérience ludique et organisée. Initialement développée en PHP procédural, l'application a subi une refonte complète vers une architecture **MVC (Modèle-Vue-Contrôleur)** robuste et performante.

---

## 🚀 Le Gros du Travail : La Révolution MVC

Le projet a été entièrement restructuré pour passer d'une multitude de fichiers isolés à une architecture professionnelle :

- **Point d'entrée unique** : Toutes les requêtes passent par `public/index.php`, garantissant une sécurité et un contrôle total.
- **Routage Centralisé** : Un système de routage intelligent (`app/Core/Router.php`) dirige les URLs vers les bons contrôleurs.
- **Séparation des Responsabilités** :
    - **Models** : Gestion de la base de données SQLite (`app/Models/`).
    - **Views** : Templates propres et réutilisables (`app/Views/`).
    - **Controllers** : Logique métier et traitement des requêtes (`app/Controllers/`).
- **Base de données SQLite** : Migration vers un système de fichier unique (`data/harmony.sq3`), léger et sans configuration serveur complexe.

---

## 🎨 Stack Technique Moderne

### Frontend : Tailwind CSS v4 ⚡
L'application utilise la toute dernière version de **Tailwind CSS (v4)** avec une approche **"CSS-first"** :
- **Performance** : Build ultra-rapide via le nouveau CLI en Rust.
- **Safelist Dynamique** : Gestion intelligente des couleurs de profils générées en PHP directement dans le CSS.
- **Design "Lux"** : Interface épurée, animations fluides et composants "Glassmorphism".

### Backend : PHP 8.x & MVC
- **Architecture Custom** : Un framework MVC léger conçu sur mesure pour le projet.
- **Autoloading** : Gestion propre des classes via Composer (PSR-4).
- **API REST-like** : Communications fluides entre le frontend (JS) et le backend via des endpoints API dédiés.

---

## 🛠️ Structure du Projet

```text
project-x/
├── app/
│   ├── Controllers/    # Logique (Dashboard, Admin, API)
│   ├── Core/           # Moteur de l'application (Router)
│   ├── Models/         # Interactions Base de données
│   └── Views/          # Templates HTML/PHP
├── data/               # Base de données SQLite & Logs
├── includes/           # Composants d'interface (Navbar, Sidebar)
├── public/             # Fichiers exposés (Index, CSS, JS)
│   └── assets/         # Ressources statiques
└── vendor/             # Dépendances Composer
```

---

## 🌟 Fonctionnalités Clés

- **Gamification** : Système de points par tâche et historique des actions par profil.
- **Gestion Intelligente** : Tâches récurrentes (Quotidien, Hebdo, Mensuel, Saisonnier).
- **Multi-Profils** : Sélection rapide de l'utilisateur avec mémorisation par Cookie.
- **Administration Complète** : Interface dédiée pour gérer les pièces, les tâches et les utilisateurs.
- **Reset Quotidien** : Système automatique de réinitialisation des tâches chaque matin.

---

## ⚙️ Installation (Laragon)

1. **Cloner le projet** dans votre dossier `www/`.
2. **Configuration Nginx** : Pointer le `root` vers le dossier `/public`.
3. **PHP** : Assurez-vous que PHP-CGI écoute sur le port `9003` (ou ajustez la config Nginx).
4. **Dépendances** :
   ```bash
   composer install
   npm install
   ```
5. **Build CSS** :
   ```bash
   npx @tailwindcss/cli -i ./public/assets/css/tailwind.css -o ./public/assets/css/style.css
   ```

---

## 📝 Notes de Développement
Le projet a été optimisé pour la rapidité et la simplicité de maintenance. L'utilisation de Tailwind v4 permet de se passer de fichiers de configuration JS complexes, tout en offrant une personnalisation totale via le fichier CSS principal.
