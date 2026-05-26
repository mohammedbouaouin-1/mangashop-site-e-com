<?php

require_once 'models/functions.php';

$cartItems = [];
$subtotal  = 0;

foreach ($_SESSION['cart'] as $pid => $row) {
    if (strpos((string)$pid, 'b_') === 0) {
        $real_id = (int)substr((string)$pid, 2);
        $item = getBundleById($real_id);
        if ($item) {
            $item['title'] = $item['name'];
            $item['id'] = 'b_' . $item['id'];
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
$freeBooks = $totalQty >= 7 ? 4 : ($totalQty >= 5 ? 2 : ($totalQty >= 3 ? 1 : 0));
$shipping = ($totalQty >= 2) ? 0 : 29.00;


$discount = 0;
if (!empty($_SESSION['promo'])) {
    $discount = round($subtotal * $_SESSION['promo']['pct'] / 100, 2);
}
$total = max(0, $subtotal - $discount + $shipping);
$pageTitle = 'Mon Panier';

require_once 'views/cart.php';
