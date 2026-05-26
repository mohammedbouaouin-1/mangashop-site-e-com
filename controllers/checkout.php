<?php

require_once 'models/functions.php';

if (isset($_GET['commande_ok']) && !empty($_SESSION['last_order'])) {
    $success = true;
    $cartItems = []; $subtotal = 0; $totalQty = 0; $shipping = 0; $discount = 0; $total = 0; $errors = [];
    $pageTitle = 'Commander';
    require_once 'views/checkout.php';
    exit;
}

// Rediriger vers login si non connecté
if (!isset($_SESSION['user'])) {
    header('Location: login.php?redirect=checkout.php');
    exit;
}

if (empty($_SESSION['cart'])) {
    header('Location: catalogue.php');
    exit;
}

$cartItems = [];
$subtotal  = 0;

foreach ($_SESSION['cart'] as $pid => $row) {
    if (strpos((string)$pid, 'b_') === 0) {
        $real_id = (int)substr((string)$pid, 2);
        $item = getBundleById($real_id);
        if ($item) {
            $item['title'] = $item['name'];
            $item['id']    = 'b_' . $item['id'];
        }
    } else {
        $item = getProductById($pid);
    }

    if ($item) {
        $line = $item['price'] * $row['qty'];
        $subtotal += $line;
        $cartItems[] = array_merge($item, ['qty' => $row['qty'], 'line' => $line]);
    }
}

$totalQty = array_sum(array_column($cartItems, 'qty'));
$shipping  = ($totalQty >= 2) ? 0 : 29.00;
$discount  = 0;
if (!empty($_SESSION['promo'])) {
    $discount = round($subtotal * $_SESSION['promo']['pct'] / 100, 2);
}
$total   = $subtotal - $discount + $shipping;
$success = false;
$errors  = [];

if (isset($_GET['stripe_cancel'])) {
    if (isset($_GET['order_id'])) {
        $orphanId = (int)$_GET['order_id'];
        $db = getDB();
        $db->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$orphanId]);
        $db->prepare("DELETE FROM orders WHERE id = ? AND status = 'pending'")->execute([$orphanId]);
    }
    $errors[] = 'Le paiement a été annulé. Veuillez réessayer.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $address = trim($_POST['address'] ?? '');
    $city    = trim($_POST['city']    ?? '');
    $payment = $_POST['payment']      ?? 'cod';
    $notes   = trim($_POST['notes']   ?? '');
    $intentId = trim($_POST['stripe_payment_intent_id'] ?? '');

    if (!$name)    $errors[] = 'Le nom complet est requis.';
    if (!$phone)   $errors[] = 'Le numéro de téléphone est requis.';
    if (!$email)   $errors[] = "L'adresse email est requise.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "L'adresse email est invalide.";
    if (!$address) $errors[] = "L'adresse de livraison est requise.";
    if (!$city)    $errors[] = 'La ville est requise.';

    if ($payment === 'card' && !$intentId) {
        $errors[] = 'Veuillez saisir et valider vos informations de carte bancaire.';
    }

    if (empty($errors)) {
        $orderId = createOrder([
            'name'    => $name,
            'email'   => $email,
            'phone'   => $phone,
            'address' => $address,
            'city'    => $city,
            'total'   => $total,
            'notes'   => $notes . ' | Paiement: ' . ($payment === 'card' ? 'Carte [Payé via Stripe] [intent:'.$intentId.']' : 'COD'),
        ]);

        foreach ($cartItems as $item) {
            addOrderItem($orderId, $item);
        }

        if ($payment === 'card') {
            $db = getDB();
            $db->prepare("UPDATE orders SET status = 'processing' WHERE id = ?")->execute([$orderId]);
        }

        require_once 'includes/mailer.php';
        $orderData = [
            'order_number' => (string)$orderId,
            'name'         => $name,
            'email'        => $email,
            'phone'        => $phone,
            'address'      => $address,
            'city'         => $city,
            'total'        => $total,
        ];
        try {
            sendOrderConfirmationToClient($orderData, $cartItems);
            sendNewOrderNotificationToAdmin($orderData);
        } catch (Exception $ex) {}

        if (!empty($_SESSION['promo'])) {
            $db = getDB();
            $db->prepare("UPDATE promo_codes SET used = used + 1 WHERE id = ?")->execute([$_SESSION['promo']['id']]);
            unset($_SESSION['promo']);
        }

        $_SESSION['cart'] = [];
        $_SESSION['last_order'] = (string)$orderId;
        $success = true;
    }
}

$pageTitle = 'Commander';

// Créer un PaymentIntent Stripe côté serveur pour le Payment Element
$stripeClientSecret = null;
if (STRIPE_SECRET_KEY && !$success) {
    $amountCents = (int)round($total * 100); // Montant en centimes MAD
    if ($amountCents > 0) {
        $fields = http_build_query([
            'amount'      => $amountCents,
            'currency'    => 'mad',
            'description' => 'Commande MangaShop',
            'automatic_payment_methods[enabled]' => 'true',
        ]);
        $isLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1']);
        $ch = curl_init('https://api.stripe.com/v1/payment_intents');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, STRIPE_SECRET_KEY . ':');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $resp = curl_exec($ch);
        $curlErr = curl_error($ch);
        curl_close($ch);
        if (!$curlErr) {
            $intentData = json_decode($resp, true);
            if (!empty($intentData['client_secret'])) {
                $stripeClientSecret = $intentData['client_secret'];
            }
        }
    }
}

require_once 'views/checkout.php';
