<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$amount = (int)($_POST['amount'] ?? 0);
$orderId = (int)($_POST['order_id'] ?? 0);

if ($amount <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Paramètres invalides']);
    exit;
}

if (!STRIPE_SECRET_KEY) {
    http_response_code(500);
    echo json_encode(['error' => 'Stripe non configuré']);
    exit;
}

$fields = [
    'amount'   => $amount,
    'currency' => 'mad',
    'metadata[order_id]' => $orderId,
    'description' => 'Commande MangaShop #' . $orderId,
];

$isLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1']);

$ch = curl_init('https://api.stripe.com/v1/payment_intents');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, STRIPE_SECRET_KEY . ':');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($curlErr) {
    http_response_code(502);
    echo json_encode(['error' => 'Réseau: ' . $curlErr]);
    exit;
}

$data = json_decode($response, true);

if (!empty($data['client_secret'])) {
    echo json_encode(['client_secret' => $data['client_secret']]);
} else {
    http_response_code(500);
    echo json_encode(['error' => $data['error']['message'] ?? 'Erreur Stripe inconnue']);
}
