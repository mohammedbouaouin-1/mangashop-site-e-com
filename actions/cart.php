<?php

require_once '../includes/config.php';
require_once '../models/functions.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? 'get';

function jsonResponse(array $data): void {
    echo json_encode($data);
    exit;
}

function getCartSummary(): array {
    $subtotal = 0;
    $totalQty = 0;

    foreach ($_SESSION['cart'] as $pid => $row) {
        $item = (strpos((string)$pid, 'b_') === 0)
            ? getBundleById((int)substr((string)$pid, 2))
            : getProductById($pid);

        if ($item) {
            $subtotal += $item['price'] * $row['qty'];
            $totalQty += $row['qty'];
        }
    }

    
    $freeBooks = $totalQty >= 7 ? 4 : ($totalQty >= 5 ? 2 : ($totalQty >= 3 ? 1 : 0));
    $shipping  = ($totalQty >= 2) ? 0 : 29.00;

    
    $discount = 0;
    if (!empty($_SESSION['promo'])) {
        $discount = round($subtotal * $_SESSION['promo']['pct'] / 100, 2);
    }

    $total = max(0, $subtotal - $discount + $shipping);

    return [
        'subtotal'  => number_format($subtotal, 2),
        'totalQty'  => $totalQty,
        'freeBooks' => $freeBooks,
        'shipping'  => $shipping === 0 ? 'Gratuite' : number_format($shipping, 2) . ' MAD',
        'discount'  => number_format($discount, 2),
        'total'     => number_format($total, 2),
        'cartCount' => $totalQty
    ];
}


if ($action === 'get') {
    $items = [];
    foreach ($_SESSION['cart'] as $pid => $row) {
        if (strpos((string)$pid, 'b_') === 0) {
            $item = getBundleById((int)substr((string)$pid, 2));
            if ($item) {
                $item['title'] = $item['name'];
                $item['id']    = 'b_' . $item['id'];
                $items[]       = array_merge($item, ['qty' => $row['qty']]);
            }
        } else {
            $item = getProductById($pid);
            if ($item) {
                $items[]       = array_merge($item, ['qty' => $row['qty']]);
            }
        }
    }
    jsonResponse(array_merge(['items' => $items], getCartSummary()));
}

if ($action === 'add') {
    $pidInput = $_POST['product_id'] ?? $_POST['product_ids'] ?? '';
    $qty = max(1, (int)($_POST['qty'] ?? 1));
    if ($pidInput) {
        $pids = is_string($pidInput) && strpos($pidInput, ',') !== false 
            ? explode(',', $pidInput) 
            : (array)$pidInput;
            
        foreach ($pids as $pid) {
            $pid = trim($pid);
            if ($pid !== '') {
                
                if (strpos((string)$pid, 'b_') !== 0) {
                    $currentQty = ($_SESSION['cart'][$pid]['qty'] ?? 0) + $qty;
                    if (!checkStock((int)$pid, $currentQty)) {
                        jsonResponse(['success' => false, 'message' => 'Stock insuffisant pour cet article.']);
                    }
                }
                $_SESSION['cart'][$pid]['qty'] = ($_SESSION['cart'][$pid]['qty'] ?? 0) + $qty;

                
                if (!empty($_SESSION['user']['id']) && strpos((string)$pid, 'b_') !== 0) {
                    $db = getDB();
                    $newQty = $_SESSION['cart'][$pid]['qty'];
                    $db->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?,?,?)
                                  ON DUPLICATE KEY UPDATE quantity = ?, updated_at = CURRENT_TIMESTAMP")
                       ->execute([$_SESSION['user']['id'], $pid, $newQty, $newQty]);
                }
            }
        }
        jsonResponse(array_merge(['success' => true], getCartSummary()));
    }
    jsonResponse(['success' => false, 'message' => 'Invalid product']);
}

if ($action === 'buy_now') {
    $_SESSION['cart'] = [];
    $pid = $_POST['product_id'] ?? '';
    $qty = max(1, (int)($_POST['qty'] ?? 1));
    if ($pid) {
        $_SESSION['cart'][$pid] = ['qty' => $qty];
        jsonResponse(array_merge(['success' => true], getCartSummary()));
    }
    jsonResponse(['success' => false, 'message' => 'Invalid product']);
}

if ($action === 'remove') {
    unset($_SESSION['cart'][$_POST['product_id'] ?? '']);
    jsonResponse(array_merge(['success' => true], getCartSummary()));
}

if ($action === 'update') {
    $pid   = $_POST['product_id'] ?? '';
    $delta = (int)($_POST['delta'] ?? 0);

    if ($pid && isset($_SESSION['cart'][$pid])) {
        $_SESSION['cart'][$pid]['qty'] += $delta;

        if ($_SESSION['cart'][$pid]['qty'] <= 0) {
            unset($_SESSION['cart'][$pid]);
            jsonResponse(array_merge(['success' => true, 'newQty' => 0], getCartSummary()));
        }

        $item = (strpos((string)$pid, 'b_') === 0)
            ? getBundleById((int)substr((string)$pid, 2))
            : getProductById($pid);

        jsonResponse(array_merge([
            'success'   => true,
            'newQty'    => $_SESSION['cart'][$pid]['qty'],
            'lineTotal' => number_format(($item['price'] ?? 0) * $_SESSION['cart'][$pid]['qty'], 2)
        ], getCartSummary()));
    }
    jsonResponse(['success' => false]);
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];
    jsonResponse(array_merge(['success' => true], getCartSummary()));
}

jsonResponse(['error' => 'Unknown action']);
