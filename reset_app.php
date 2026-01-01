<?php
require_once 'includes/bootstrap.php';

// reset_app.php
$pdo->exec("DELETE FROM tasks");
$pdo->exec("DELETE FROM rooms");
$pdo->exec("DELETE FROM users");
$pdo->exec("DELETE FROM history");
$pdo->exec("DELETE FROM trophies");

// Reset auto-increment
$pdo->exec("DELETE FROM sqlite_sequence WHERE name IN ('tasks', 'rooms', 'users', 'history', 'trophies')");

$configPath = __DIR__ . '/data/last_reset.txt';
if (file_exists($configPath)) {
    unlink($configPath);
}

header('Location: admin.php?reset_success=1');
exit;
