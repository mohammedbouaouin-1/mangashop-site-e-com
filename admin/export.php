<?php
require_once '../includes/config.php';
$db = getDB();


if (!($_SESSION['admin'] ?? false)) {
    header('Location: index.php'); exit;
}


$token = $_GET['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    http_response_code(403); die('Token CSRF invalide.');
}

$type = $_GET['type'] ?? '';

if ($type === 'orders') {
    $rows = $db->query("SELECT id, order_number, customer_name, customer_email, customer_phone, city, customer_address, total, status, tracking_number, notes, created_at FROM orders ORDER BY created_at DESC")->fetchAll();
    $filename = 'commandes_' . date('Y-m-d') . '.csv';
    $headers  = ['ID','Référence','Nom','Email','Téléphone','Ville','Adresse','Total (MAD)','Statut','N° Suivi','Notes','Date'];
    $fields   = ['id','order_number','customer_name','customer_email','customer_phone','city','customer_address','total','status','tracking_number','notes','created_at'];
} elseif ($type === 'users') {
    $rows = $db->query("SELECT id, name, email, role, phone, city, created_at FROM users ORDER BY created_at DESC")->fetchAll();
    $filename = 'clients_' . date('Y-m-d') . '.csv';
    $headers  = ['ID','Nom','Email','Rôle','Téléphone','Ville','Inscrit le'];
    $fields   = ['id','name','email','role','phone','city','created_at'];
} else {
    http_response_code(400); die('Type inconnu.');
}

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');


echo "\xEF\xBB\xBF";

$out = fopen('php://output', 'w');
fputcsv($out, $headers, ';');
foreach ($rows as $row) {
    $line = [];
    foreach ($fields as $f) $line[] = $row[$f] ?? '';
    fputcsv($out, $line, ';');
}
fclose($out);
exit;
