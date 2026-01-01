<?php
require_once 'includes/bootstrap.php';

$today = date('Y-m-d');

$stmt = $pdo->prepare("UPDATE tasks SET date_prevue = NULL WHERE date_prevue = ?");
$stmt->execute([$today]);

header('Location: index.php');
