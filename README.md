# Home Harmony

Une application web simple et élégante pour gérer les tâches ménagères quotidiennes. Organisez vos corvées par pièce, fréquence et profil utilisateur, avec un suivi historique pour motiver la famille.

## Fonctionnalités

- **Gestion des profils** : Créez des profils pour chaque membre de la famille avec des couleurs et emojis personnalisés.
- **Pièces et tâches** : Associez des tâches à des pièces spécifiques (salon, cuisine, jardin, etc.).
- **Fréquences personnalisables** : Quotidien, hebdomadaire, mensuel ou saisonnier.
- **Validation en temps réel** : Marquez les tâches comme faites avec un profil sélectionné.
- **Annulation** : Possibilité d'annuler une tâche validée par erreur.
- **Filtrage** : Filtrez les tâches par catégorie (Maison, Jardin, Voiture).
- **Historique** : Suivi automatique des tâches réalisées avec dates et profils.
- **Interface responsive** : Design moderne avec mode "Lux" pour une expérience fluide.

## Technologies utilisées

- **Backend** : PHP (sans base de données, stockage JSON)
- **Frontend** : HTML5, CSS3 (Tailwind CSS), JavaScript
- **Serveur local** : Compatible avec Laragon ou XAMPP
- **Déploiement** : Hébergement web standard avec PHP

## Installation

1. **Clonez le repository** :
   ```bash
   git clone https://github.com/ethan2106/home-harmony.git
   cd home-harmony
   ```

2. **Configurez un serveur local** :
   - Utilisez Laragon, XAMPP ou WAMP.
   - Placez le dossier dans le répertoire web (ex: `www/` pour Laragon).
   - Assurez-vous que PHP est activé.

3. **Accédez à l'application** :
   - Ouvrez votre navigateur à `http://localhost/home-harmony` (ajustez selon votre setup).

4. **Première utilisation** :
   - Ajoutez des profils via `add_profile.php`.
   - Créez des pièces via `add_room.php`.
   - Ajoutez des tâches via `add_task.php`.

## Utilisation

- **Sélectionnez un profil** : Cliquez sur un profil dans le sélecteur pour activer les validations.
- **Validez une tâche** : Cliquez sur une tâche pour la marquer comme faite (nécessite un profil sélectionné).
- **Annulez une tâche** : Utilisez la fonction undo si une erreur a été commise.
- **Filtrez** : Utilisez les boutons de filtre pour afficher seulement certaines catégories.
- **Admin** : Accédez à `admin.php` pour gérer les données JSON.

## Structure du projet

```
home-harmony/
├── index.php              # Page principale
├── add_profile.php        # Ajout de profils
├── add_room.php           # Ajout de pièces
├── add_task.php           # Ajout de tâches
├── update_task.php        # Mise à jour des tâches (AJAX)
├── admin.php              # Interface d'administration
├── assets/
│   ├── css/style.css      # Styles CSS
│   └── js/
│       ├── app.js         # Logique frontend
│       └── admin.js       # Scripts admin
├── includes/
│   ├── bootstrap.php      # Initialisation et logique métier
│   ├── functions.php      # Fonctions utilitaires
│   ├── header.php         # En-tête HTML
│   ├── navbar.php         # Barre de navigation
│   ├── sidebar.php        # Barre latérale
│   └── footer.php         # Pied de page
├── *.json                 # Fichiers de données (tâches, profils, pièces, historique)
└── README.md              # Ce fichier
```

## Contribution

Les contributions sont les bienvenues ! Forkez le repo, créez une branche pour vos modifications, et soumettez une pull request.

## Licence

Ce projet est sous licence MIT. Voir le fichier LICENSE pour plus de détails.