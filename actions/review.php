<?php
require_once '../includes/config.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../catalogue.php');
    exit;
}

$productId   = (int)($_POST['product_id'] ?? 0);
$productSlug = trim($_POST['product_slug'] ?? '');
$rating      = (int)($_POST['rating'] ?? 0);
$comment     = trim($_POST['comment'] ?? '');
$userId      = (int)$_SESSION['user']['id'];
$name        = $_SESSION['user']['name'];

if (!$productId || $rating < 1 || $rating > 5 || !$comment) {
    $err = urlencode('Veuillez remplir tous les champs et choisir une note.');
    header("Location: ../product.php?slug=$productSlug&review_err=$err");
    exit;
}

$db = getDB();


$check = $db->prepare("SELECT id FROM reviews WHERE product_id = ? AND user_id = ?");
$check->execute([$productId, $userId]);
if ($check->fetch()) {
    $err = urlencode('Vous avez déjà laissé un avis pour ce produit.');
    header("Location: ../product.php?slug=$productSlug&review_err=$err");
    exit;
}

$db->prepare("INSERT INTO reviews (product_id, user_id, customer_name, rating, comment) VALUES (?,?,?,?,?)")
   ->execute([$productId, $userId, $name, $rating, $comment]);


$db->prepare("UPDATE products SET rating = (SELECT AVG(rating) FROM reviews WHERE product_id = ?), review_count = (SELECT COUNT(*) FROM reviews WHERE product_id = ?) WHERE id = ?")
   ->execute([$productId, $productId, $productId]);

header("Location: ../product.php?slug=$productSlug&review=ok");
exit;
