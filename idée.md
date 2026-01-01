**Carnet d'Entretien Maison & Jardin**

Ce document détaille le plan de création d'une application simple pour gérer les tâches domestiques et extérieures.

## 1. Concept et Fonctionnalités
L'objectif est d'avoir une liste de tâches récurrentes ou ponctuelles organisée par zone (Maison / Jardin / Voiture) et par fréquence.

### Fonctionnalités clés :
- **Tableau de bord :** Vue d'ensemble des tâches du jour.
- **Gestion des profils :** Choix rapide du membre de la famille (Lucie, Jim, Ethan, etc.) avec une **couleur personnalisée** par profil.
- **Gestion des tâches :** Ajouter, modifier, supprimer des tâches.
- **Catégorisation :** Séparation entre "Maison", "Jardin" et "Voiture".
- **Récurrence :** Définir si une tâche est quotidienne, hebdomadaire, mensuelle ou saisonnière.
- **Statut :** Marquer une tâche comme "Fait" avec enregistrement de qui l'a réalisée (affichage de la couleur du profil).

## 2. Structure des Données (Modèle)
Pour rester simple, nous pouvons utiliser un fichier JSON ou une petite base de données SQLite.

**Profils (config.json) :**
- `nom` : Lucie, Jim, etc.
- `couleur` : Code hexadécimal ou classe CSS (ex: #FF5733).

**Champs d'une tâche (tasks.json) :**
- `id` : Identifiant unique.
- `titre` : Nom de la tâche (ex: Balayer le salon, Aspirer la voiture).
- `categorie` : Maison, Jardin ou Voiture.
- `frequence` : Quotidien, Hebdo, etc.
- `dernier_fait` : Date de la dernière exécution.
- `fait_par` : Nom du membre ayant validé la tâche en dernier.
- `jour_prevu` : (Optionnel) Jour spécifique de la semaine.

## 3. Interface Utilisateur (UI)
Une interface épurée optimisée pour **PC** :
- **Header :** Titre, date du jour et **Sélecteur de Profil** (boutons avec la couleur de chaque membre).
- **Filtres :** Boutons pour basculer entre "Tout", "Maison", "Jardin", "Voiture".
- **Liste de tâches :** Cartes simples avec une case à cocher. Une tâche validée affiche un badge ou une bordure de la **couleur du profil** qui l'a faite.
- **Bouton "+" :** Pour ajouter une nouvelle tâche rapidement.

## 4. Stack Technique (Simple)
- **Frontend :** HTML5, **Tailwind CSS** (via CDN pour la simplicité ou CLI), JavaScript (Vanilla).
- **Backend :** PHP (pour la logique simple et la persistance).
- **Stockage :** Fichiers JSON (`tasks.json` et `profiles.json`).

## 5. Validation des Tâches (Interaction)
Pour une expérience fluide, nous utiliserons **JavaScript (Fetch API)** :
1. **Action :** L'utilisateur choisit son profil (ex: Lucie) puis coche une case.
2. **JS :** Envoie une requête "en arrière-plan" (AJAX) à un script PHP (ex: `update_task.php`) avec l'ID de la tâche et le nom du profil.
3. **PHP :** Met à jour le statut (`dernier_fait`) et le champ `fait_par` dans le fichier JSON.
4. **UI :** La tâche affiche "Fait par Lucie" et change d'état visuel sans recharger la page.

## 6. Étapes de Développement
1. **Phase 1 :** Maquette HTML/CSS statique de la liste de tâches.
2. **Phase 2 :** Création du fichier de stockage (JSON) et lecture en PHP.
3. **Phase 3 :** Implémentation de l'ajout et du marquage "Fait".
4. **Phase 4 :** Ajout des filtres par catégorie.
5. **Phase 5 :** Peaufinage du design (PC) et ergonomie.
