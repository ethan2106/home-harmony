<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $emoji = $_POST['emoji'] ?? '';
    $couleur = $_POST['couleur'] ?? 'indigo-500';

    if (!empty($nom)) {
        $profiles = loadData('profiles.json');
        
        $newId = 1;
        if (!empty($profiles)) {
            $ids = array_column($profiles, 'id');
            $newId = max($ids) + 1;
        }

        $newProfile = [
            'id' => $newId,
            'nom' => $nom,
            'emoji' => $emoji,
            'couleur' => $couleur
        ];

        $profiles[] = $newProfile;
        saveData('profiles.json', $profiles);
    }
}

header('Location: admin.php');
exit;
