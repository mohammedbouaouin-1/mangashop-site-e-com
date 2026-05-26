<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

$q = trim($_GET['q'] ?? '');
if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$db = getDB();
$stmt = $db->prepare("SELECT id, title, slug, price, image_url, author FROM products WHERE title LIKE ? OR author LIKE ? ORDER BY rating DESC, id DESC LIMIT 5");
$stmt->execute(['%' . $q . '%', '%' . $q . '%']);
$products = $stmt->fetchAll();

$results = [];
foreach ($products as $p) {
    $results[] = [
        'title'     => $p['title'],
        'author'    => $p['author'],
        'slug'      => $p['slug'],
        'price'     => number_format($p['price'], 2) . ' MAD',
        'image_url' => asset($p['image_url']),
    ];
}

echo json_encode($results);
exit;
