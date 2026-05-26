<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

$db    = getDB();
$email = trim($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email invalide.']);
    exit;
}

try {
    $db->prepare("INSERT INTO newsletter (email) VALUES (?)")->execute([$email]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Vous êtes déjà inscrit(e).']);
}
