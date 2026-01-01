<?php
// reset_app.php
file_put_contents('tasks.json', json_encode([], JSON_PRETTY_PRINT));
file_put_contents('rooms.json', json_encode([], JSON_PRETTY_PRINT));
file_put_contents('profiles.json', json_encode([], JSON_PRETTY_PRINT));
file_put_contents('history.json', json_encode([], JSON_PRETTY_PRINT));
if (file_exists('last_reset.txt')) {
    unlink('last_reset.txt');
}

header('Location: admin.php?reset_success=1');
exit;
