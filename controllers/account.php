<?php

require_once 'models/functions.php';


if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];


$stmtUser = getDB()->prepare("SELECT role FROM users WHERE id = ?");
$stmtUser->execute([(int)$user['id']]);
$user['role'] = $stmtUser->fetchColumn() ?: 'user';
$_SESSION['user']['role'] = $user['role'];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['role'] === 'livreur') {
    $action = $_POST['action'] ?? '';
    if ($action === 'update_delivery_status') {
        $orderId = (int)($_POST['order_id'] ?? 0);
        $newStatus = $_POST['status'] ?? '';
        if (in_array($newStatus, ['processing', 'shipped', 'delivered'])) {
            $stmt = getDB()->prepare("UPDATE orders SET status = ? WHERE id = ? AND livreur_id = ?");
            $stmt->execute([$newStatus, $orderId, (int)$user['id']]);
            
            
            require_once 'includes/mailer.php';
            $updatedOrder = getDB()->prepare("SELECT * FROM orders WHERE id=?");
            $updatedOrder->execute([$orderId]);
            $orderRow = $updatedOrder->fetch();
            if ($orderRow) sendOrderStatusEmail($orderRow);
            
            $_SESSION['profile_success'] = "Statut de la livraison #{$orderRow['order_number']} mis à jour en : " . ucfirst($newStatus);
        }
        header('Location: account.php?tab=deliveries');
        exit;
    }
}

$tab  = $_GET['tab'] ?? (($user['role'] === 'livreur') ? 'deliveries' : 'profile');


if ($user['role'] === 'livreur') {
    $stmtOrders = getDB()->prepare("SELECT * FROM orders WHERE livreur_id = ? ORDER BY created_at DESC");
    $stmtOrders->execute([(int)$user['id']]);
    $orders = $stmtOrders->fetchAll();
} else {
    $orders = getOrdersByEmail($user['email'], 10);
}

foreach ($orders as &$o) {
    $stmt = getDB()->prepare("
        SELECT oi.quantity, COALESCE(p.title, 'Article') as title
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([(int)$o['id']]);
    $o['items'] = $stmt->fetchAll();
}
unset($o);

$wishlist = getWishlistItems($user['id']);

$pageTitle = 'Mon Compte';
require_once 'views/account.php';
