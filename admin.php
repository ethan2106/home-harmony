<?php
$profiles = json_decode(file_get_contents("profiles.json"), true);
$tasks = json_decode(file_get_contents("tasks.json"), true) ?? [];
$rooms = json_decode(file_get_contents("rooms.json"), true) ?? [];

// Suppression d'une tâche
if (isset($_GET['delete'])) {
    $idToDelete = $_GET['delete'];
    $tasks = array_filter($tasks, function($t) use ($idToDelete) {
        return $t['id'] != $idToDelete;
    });
    file_put_contents('tasks.json', json_encode(array_values($tasks), JSON_PRETTY_PRINT));
    header('Location: admin.php');
    exit;
}

// Suppression d'une pièce
if (isset($_GET['delete_room'])) {
    $idToDelete = $_GET['delete_room'];
    $rooms = array_filter($rooms, function($r) use ($idToDelete) {
        return $r['id'] != $idToDelete;
    });
    file_put_contents('rooms.json', json_encode(array_values($rooms), JSON_PRETTY_PRINT));
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration - Entretien</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

    <div class="container mx-auto py-8 px-4">
        <header class="flex justify-between items-center mb-10 bg-white p-6 rounded-xl shadow-sm">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Configuration</h1>
                <p class="text-gray-500">Gérez vos pièces et votre catalogue de tâches.</p>
            </div>
            <a href="index.php" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-bold hover:bg-gray-300 transition-colors">
                Retour au Tableau de Bord
            </a>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- Gestion des Pièces -->
            <div class="bg-white p-8 rounded-2xl shadow-sm">
                <h2 class="text-xl font-bold mb-6 text-gray-800 flex items-center gap-2">🏠 Gestion des Pièces</h2>
                <form id="add-room-form" action="add_room.php" method="POST" class="mb-8 grid grid-cols-1 gap-6">
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

                    <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition-colors shadow-lg">
                        + Ajouter la pièce
                    </button>
                </form>

                <div class="space-y-2">
                    <?php foreach ($rooms as $room): ?>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-<?php echo $room['couleur']; ?> border border-gray-200">
                        <span class="font-bold text-gray-800"><?php echo $room['emoji']; ?> <?php echo $room['nom']; ?></span>
                        <a href="admin.php?delete_room=<?php echo $room['id']; ?>" class="text-red-500 hover:text-red-700 text-sm font-bold">Supprimer</a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Formulaire d'ajout de tâche -->
            <div class="bg-white p-8 rounded-2xl shadow-sm">
                <h2 class="text-xl font-bold mb-6 text-gray-800 flex items-center gap-2">🎯 Nouvelle Tâche Type</h2>
                <form action="add_task.php" method="POST">
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
                            <option value="jardin">🌳 Jardin</option>
                            <option value="voiture">🚗 Voiture</option>
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
                                <?php 
                                    $roomName = "Général";
                                    $roomEmoji = "📋";
                                    if (isset($task['room_id'])) {
                                        if ($task['room_id'] === 'jardin') { $roomName = "Jardin"; $roomEmoji = "🌳"; }
                                        elseif ($task['room_id'] === 'voiture') { $roomName = "Voiture"; $roomEmoji = "🚗"; }
                                        else {
                                            foreach ($rooms as $r) {
                                                if ($r['id'] == $task['room_id']) {
                                                    $roomName = $r['nom'];
                                                    $roomEmoji = $r['emoji'];
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600">
                                    <?php echo $roomEmoji; ?> <?php echo $roomName; ?>
                                </span>
                            </td>
                            <td class="py-4 text-gray-500 text-sm"><?php echo $task['frequence']; ?></td>
                            <td class="py-4 text-right">
                                <a href="admin.php?delete=<?php echo $task['id']; ?>" 
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const emojiPicker = document.getElementById('emoji-picker');
    const colorPicker = document.getElementById('color-picker');
    const selectedEmojiInput = document.getElementById('selected-emoji');
    const selectedColorInput = document.getElementById('selected-color');
    const form = document.getElementById('add-room-form');

    const emojis = ['🛋️', '🛏️', '🍽️', '🍳', '🛁', '🚽', '🖥️', '📚', '👕', '📦', '🌿', '🚗', '工具'];
    const colors = [
        'slate-100', 'red-100', 'orange-100', 'amber-100', 'yellow-100', 
        'lime-100', 'green-100', 'emerald-100', 'teal-100', 'cyan-100', 
        'sky-100', 'blue-100', 'indigo-100', 'violet-100', 'purple-100', 
        'fuchsia-100', 'pink-100', 'rose-100'
    ];

    // Populate Emojis
    emojis.forEach(emoji => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = emoji;
        btn.className = 'p-2 rounded-lg text-2xl hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500';
        btn.addEventListener('click', () => {
            selectedEmojiInput.value = emoji;
            document.querySelectorAll('#emoji-picker button').forEach(b => b.classList.remove('bg-indigo-200'));
            btn.classList.add('bg-indigo-200');
        });
        emojiPicker.appendChild(btn);
    });

    // Populate Colors
    colors.forEach(color => {
        const colorDiv = document.createElement('div');
        colorDiv.className = `w-10 h-10 rounded-lg cursor-pointer bg-${color} border-2 border-transparent hover:border-indigo-500`;
        colorDiv.dataset.color = color;
        colorDiv.addEventListener('click', () => {
            selectedColorInput.value = color;
            document.querySelectorAll('#color-picker div').forEach(d => d.classList.remove('ring-4', 'ring-offset-2', 'ring-indigo-500'));
            colorDiv.classList.add('ring-4', 'ring-offset-2', 'ring-indigo-500');
        });
        colorPicker.appendChild(colorDiv);
    });

    // Set default values for the first emoji and color
    if (emojis.length > 0) {
        emojiPicker.children[0].click();
    }
    if (colors.length > 0) {
        colorPicker.children[0].click();
    }

    // Form validation
    form.addEventListener('submit', function(e) {
        if (!selectedEmojiInput.value || !selectedColorInput.value) {
            e.preventDefault();
            alert('Veuillez sélectionner un emoji et une couleur.');
        }
    });
});
</script>

</body>
</html>
