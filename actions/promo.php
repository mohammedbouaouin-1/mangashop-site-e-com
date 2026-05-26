<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action === 'apply') {
    $code = strtoupper(trim($_POST['code'] ?? ''));
    if (!$code) { echo json_encode(['success'=>false,'msg'=>'Code vide.']); exit; }

    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM promo_codes WHERE code = ? AND active = 1");
    $stmt->execute([$code]);
    $promo = $stmt->fetch();

    if (!$promo) {
        echo json_encode(['success'=>false,'msg'=>'Code promo invalide ou expiré.']);
        exit;
    }
    if ($promo['expires_at'] && strtotime($promo['expires_at']) < time()) {
        echo json_encode(['success'=>false,'msg'=>'Ce code promo a expiré.']);
        exit;
    }
    if ($promo['used'] >= $promo['max_uses']) {
        echo json_encode(['success'=>false,'msg'=>'Ce code a atteint sa limite d\'utilisation.']);
        exit;
    }

    $_SESSION['promo'] = ['code' => $code, 'pct' => (int)$promo['discount_pct'], 'id' => $promo['id']];
    echo json_encode(['success'=>true,'pct'=>(int)$promo['discount_pct'],'msg'=>'Code appliqué : -'.$promo['discount_pct'].'%']);
    exit;
}

if ($action === 'remove') {
    unset($_SESSION['promo']);
    echo json_encode(['success'=>true]);
    exit;
}

echo json_encode(['success'=>false,'msg'=>'Action inconnue.']);
