<?php
require_once 'includes/functions.php';

// reset_app.php
saveData('tasks.json', []);
saveData('rooms.json', []);
saveData('profiles.json', []);
saveData('history.json', []);

if (file_exists('last_reset.txt')) {
    unlink('last_reset.txt');
}

header('Location: admin.php?reset_success=1');
exit;
