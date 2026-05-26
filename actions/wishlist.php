<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

$db  = getDB();
$pid = (int)($_POST['product_id'] ?? 0);
$user = $_SESSION['user'] ?? null;

if (!$user || !$pid) {
    echo json_encode(['success' => false, 'message' => 'Connectez-vous pour utiliser les favoris.']);
    exit;
}

$check = $db->prepare("SELECT 1 FROM wishlist WHERE user_id=? AND product_id=?");
$check->execute([$user['id'], $pid]);

if ($check->fetchColumn()) {
    $db->prepare("DELETE FROM wishlist WHERE user_id=? AND product_id=?")->execute([$user['id'], $pid]);
    echo json_encode(['success' => true, 'inWishlist' => false]);
} else {
    $db->prepare("INSERT IGNORE INTO wishlist (user_id,product_id) VALUES (?,?)")->execute([$user['id'], $pid]);
    echo json_encode(['success' => true, 'inWishlist' => true]);
}
