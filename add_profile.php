<?php
// add_profile.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $emoji = $_POST['emoji'] ?? '';
    $couleur = $_POST['couleur'] ?? 'indigo-500';

    if (!empty($nom)) {
        $profiles = json_decode(file_get_contents('profiles.json'), true) ?? [];
        
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
        file_put_contents('profiles.json', json_encode($profiles, JSON_PRETTY_PRINT));
    }
}

header('Location: admin.php');
exit;
