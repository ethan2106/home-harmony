<?php
/**
 * Vue Admin - Interface d'administration
 */
require_once __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>

<div class="container mx-auto py-8 px-4">
    <header class="flex justify-between items-center mb-10 bg-white p-6 rounded-xl shadow-sm">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Configuration</h1>
            <p class="text-gray-500">Gérez vos pièces et votre catalogue de tâches.</p>
        </div>
        <div class="flex gap-4">
            <button onclick="confirmReset()" class="bg-red-100 text-red-600 px-6 py-2 rounded-lg font-bold hover:bg-red-200 transition-colors">
                ⚠️ Remise à zéro
            </button>
            <a href="<?php echo BASE_URL; ?>/" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-bold hover:bg-gray-300 transition-colors">
                Retour au Tableau de Bord
            </a>
        </div>
    </header>

    <?php if ($reset_success): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-8 rounded-r-xl shadow-sm" role="alert">
        <p class="font-bold">Succès !</p>
        <p>L'application a été remise à zéro avec succès.</p>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <!-- Gestion des Pièces -->
        <div class="bg-white p-8 rounded-2xl shadow-sm">
            <h2 class="text-xl font-bold mb-6 text-gray-800 flex items-center gap-2">🏠 Gestion des Pièces</h2>
            <form id="add-room-form" action="<?php echo BASE_URL; ?>/admin/add-room" method="POST" class="mb-8 grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nom de la pièce</label>
                    <input type="text" name="nom" required placeholder="ex: Salon" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Choisissez un Emoji</label>
                    <div id="emoji-picker" class="flex flex-wrap gap-2 p-3 bg-gray-50 rounded-lg border">
                        <!-- Emojis will be populated by JS -->
                    </div>
                    <input type="hidden" name="emoji" id="selected-emoji" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Couleur de fond</label>
                    <div id="color-picker" class="grid grid-cols-8 md:grid-cols-10 gap-2">
                        <!-- Colors will be populated by JS -->
                    </div>
                    <input type="hidden" name="couleur" id="selected-color" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Zone de la pièce</label>
                    <select name="zone" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="maison">🏠 Maison</option>
                        <option value="jardin">🌳 Jardin</option>
                        <option value="voiture">🚗 Voiture</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition-colors shadow-lg">
                    + Ajouter la pièce
                </button>
            </form>

            <div class="space-y-2">
                <?php foreach ($rooms as $room): ?>
                <div class="flex items-center justify-between p-3 rounded-lg bg-<?php echo $room['couleur']; ?> border border-gray-200">
                    <span class="font-bold text-gray-800"><?php echo $room['emoji']; ?> <?php echo $room['nom']; ?></span>
                    <a href="<?php echo BASE_URL; ?>/admin?delete_room=<?php echo $room['id']; ?>" class="text-red-500 hover:text-red-700 text-sm font-bold">Supprimer</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Formulaire d'ajout de tâche -->
        <div class="bg-white p-8 rounded-2xl shadow-sm">
            <h2 class="text-xl font-bold mb-6 text-gray-800 flex items-center gap-2">🎯 Nouvelle Tâche Type</h2>
            <form action="<?php echo BASE_URL; ?>/admin/add-task" method="POST">
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Titre de la tâche</label>
                    <input type="text" name="titre" required placeholder="ex: Passer l'aspirateur" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pièce / Zone</label>
                    <select name="room_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="0">Aucune (Général)</option>
                        <?php foreach ($rooms as $room): ?>
                            <option value="<?php echo $room['id']; ?>"><?php echo $room['emoji']; ?> <?php echo $room['nom']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Fréquence suggérée</label>
                    <select name="frequence" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="Quotidien">Quotidien</option>
                        <option value="Hebdomadaire">Hebdomadaire</option>
                        <option value="Mensuel">Mensuel</option>
                        <option value="Saisonnier">Saisonnier</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 rounded-lg hover:bg-indigo-700 transition-colors shadow-lg">
                    Ajouter au catalogue
                </button>
            </form>
        </div>
    </div>

    <!-- Gestion des Profils -->
    <div class="bg-white p-8 rounded-2xl shadow-sm mb-12">
        <h2 class="text-xl font-bold mb-6 text-gray-800 flex items-center gap-2">👥 Gestion des Profils</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <form action="<?php echo BASE_URL; ?>/admin/add-profile" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nom du profil</label>
                    <input type="text" name="nom" required placeholder="ex: Lucie" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Choisissez un Emoji</label>
                    <div id="profile-emoji-picker" class="flex flex-wrap gap-2 p-3 bg-gray-50 rounded-lg border">
                        <!-- Emojis will be populated by JS -->
                    </div>
                    <input type="hidden" name="emoji" id="selected-profile-emoji" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Couleur</label>
                    <div id="profile-color-picker" class="grid grid-cols-8 md:grid-cols-10 gap-2">
                        <!-- Colors will be populated by JS -->
                    </div>
                    <input type="hidden" name="couleur" id="selected-profile-color" required>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 rounded-lg hover:bg-indigo-700 transition-colors shadow-lg">
                    + Créer le profil
                </button>
            </form>

            <div class="space-y-3">
                <h3 class="font-bold text-gray-600 mb-2">Profils enregistrés</h3>
                <?php foreach ($profiles as $profile): ?>
                <div class="flex items-center justify-between p-4 rounded-xl bg-white border-2 border-gray-100 hover:border-indigo-200 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-<?php echo $profile['couleur']; ?> flex items-center justify-center text-white font-bold shadow-sm">
                            <?php
                                // Extract emoji if it's in the name or if we add an emoji field
                                echo isset($profile['emoji']) ? $profile['emoji'] : substr($profile['nom'], 0, 1);
                    ?>
                        </div>
                        <span class="font-bold text-gray-800"><?php echo $profile['nom']; ?></span>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/admin?delete_profile=<?php echo $profile['id']; ?>"
                       onclick="return confirm('Supprimer ce profil ?')"
                       class="text-red-400 hover:text-red-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Liste des tâches existantes -->
    <div class="bg-white p-8 rounded-2xl shadow-sm">
        <h2 class="text-xl font-bold mb-6 text-gray-800">Catalogue des Tâches</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b">
                        <th class="pb-4 font-bold text-gray-600">Titre</th>
                        <th class="pb-4 font-bold text-gray-600">Zone / Pièce</th>
                        <th class="pb-4 font-bold text-gray-600">Fréquence</th>
                        <th class="pb-4 font-bold text-gray-600 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($tasks as $task): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 font-medium text-gray-800"><?php echo $task['titre']; ?></td>
                        <td class="py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600">
                                <?php echo $task['room_emoji'] ?? '📋'; ?> <?php echo $task['room_nom'] ?? 'Général'; ?>
                            </span>
                        </td>
                        <td class="py-4 text-gray-500 text-sm"><?php echo $task['frequence']; ?></td>
                        <td class="py-4 text-right">
                                <a href="<?php echo BASE_URL; ?>/admin?delete=<?php echo $task['id']; ?>"
                               onclick="return confirm('Supprimer cette tâche du catalogue ?')"
                               class="text-red-500 hover:text-red-700 font-bold text-sm">
                                Supprimer
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="/assets/js/admin.js"></script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
