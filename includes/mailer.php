<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

function sendMail(string $to, string $subject, string $html): bool {
    if (!defined('SMTP_USER') || empty(SMTP_USER) || strpos(SMTP_USER, 'VOTRE_') === 0) {
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: MangaShop <" . SITE_EMAIL . ">\r\n";
        return @mail($to, $subject, $html, $headers);
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = (SMTP_SECURE === 'ssl') ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(SMTP_USER, SITE_NAME);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html;

        $mail->send();
        return true;
    } catch (Exception $e) {
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: MangaShop <" . SITE_EMAIL . ">\r\n";
        return @mail($to, $subject, $html, $headers);
    }
}

function sendOrderConfirmationToClient(array $order, array $items) {
    if (empty($order['email'])) return false;

    $itemsHtml = '';
    foreach ($items as $item) {
        $itemsHtml .= '<tr>
            <td style="padding:8px 0;border-bottom:1px solid #f0ebe3;font-size:14px;color:#31231e;">' . htmlspecialchars($item['title'] ?? 'Article') . '</td>
            <td style="padding:8px 0;border-bottom:1px solid #f0ebe3;font-size:14px;text-align:center;color:#31231e;">' . (int)($item['qty'] ?? $item['quantity'] ?? 1) . '</td>
            <td style="padding:8px 0;border-bottom:1px solid #f0ebe3;font-size:14px;text-align:right;color:#31231e;">' . number_format((float)($item['price'] ?? 0), 2) . ' MAD</td>
        </tr>';
    }

    $subject = 'Commande #' . $order['order_number'] . ' confirmee - MangaShop';
    $html = '<!DOCTYPE html><html><body style="margin:0;padding:0;background:#f9f7f3;font-family:sans-serif;">
    <div style="max-width:600px;margin:40px auto;background:#fff;border-radius:16px;overflow:hidden;border:1px solid #e2dcd3;">
        <div style="background:#31231e;padding:32px;text-align:center;">
            <h1 style="color:#fff;margin:0;font-size:24px;letter-spacing:-0.02em;">MangaShop</h1>
            <p style="color:rgba(255,255,255,0.6);margin:8px 0 0;font-size:14px;">Votre commande est confirmee</p>
        </div>
        <div style="padding:32px;">
            <p style="font-size:16px;color:#31231e;margin-bottom:8px;">Bonjour <strong>' . htmlspecialchars($order['name']) . '</strong>,</p>
            <p style="font-size:14px;color:#7d7067;line-height:1.6;">Nous avons bien recu votre commande <strong>#' . $order['order_number'] . '</strong>. Elle sera traitee dans les plus brefs delais.</p>

            <div style="background:#f9f7f3;border-radius:10px;padding:20px;margin:24px 0;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead><tr>
                        <th style="text-align:left;font-size:12px;color:#7d7067;text-transform:uppercase;padding-bottom:8px;">Produit</th>
                        <th style="text-align:center;font-size:12px;color:#7d7067;text-transform:uppercase;padding-bottom:8px;">Qte</th>
                        <th style="text-align:right;font-size:12px;color:#7d7067;text-transform:uppercase;padding-bottom:8px;">Prix</th>
                    </tr></thead>
                    <tbody>' . $itemsHtml . '</tbody>
                </table>
                <div style="display:flex;justify-content:space-between;margin-top:16px;padding-top:12px;border-top:2px solid #e2dcd3;">
                    <span style="font-weight:700;font-size:15px;color:#31231e;">Total</span>
                    <span style="font-weight:800;font-size:16px;color:#a24f2b;">' . number_format((float)$order['total'], 2) . ' MAD</span>
                </div>
            </div>

            <div style="background:#fff;border:1px solid #e2dcd3;border-radius:10px;padding:16px;margin-bottom:24px;">
                <p style="margin:0 0 6px;font-size:12px;text-transform:uppercase;color:#7d7067;font-weight:700;">Livraison a</p>
                <p style="margin:0;font-size:14px;color:#31231e;font-weight:600;">' . htmlspecialchars($order['address']) . ', ' . htmlspecialchars($order['city']) . '</p>
            </div>

            <p style="font-size:13px;color:#7d7067;text-align:center;">Des questions ? Contactez-nous a <a href="mailto:' . SITE_EMAIL . '" style="color:#a24f2b;">' . SITE_EMAIL . '</a></p>
        </div>
        <div style="background:#f9f7f3;padding:16px;text-align:center;font-size:12px;color:#a49e97;"> ' . date('Y') . ' MangaShop - Tous droits reserves</div>
    </div></body></html>';

    return sendMail($order['email'], $subject, $html);
}

function sendNewOrderNotificationToAdmin(array $order) {
    $subject = 'Nouvelle commande #' . $order['order_number'] . ' - ' . number_format((float)$order['total'], 2) . ' MAD';
    $html = '<!DOCTYPE html><html><body style="font-family:sans-serif;background:#f9f7f3;">
    <div style="max-width:500px;margin:40px auto;background:#fff;border-radius:12px;padding:28px;border:1px solid #e2dcd3;">
        <h2 style="color:#31231e;margin:0 0 16px;">Nouvelle commande recue</h2>
        <table style="width:100%;font-size:14px;color:#31231e;">
            <tr><td style="padding:6px 0;color:#7d7067;">Reference</td><td style="font-weight:700;">#' . $order['order_number'] . '</td></tr>
            <tr><td style="padding:6px 0;color:#7d7067;">Client</td><td>' . htmlspecialchars($order['name']) . '</td></tr>
            <tr><td style="padding:6px 0;color:#7d7067;">Email</td><td>' . htmlspecialchars($order['email'] ?? '-') . '</td></tr>
            <tr><td style="padding:6px 0;color:#7d7067;">Telephone</td><td>' . htmlspecialchars($order['phone']) . '</td></tr>
            <tr><td style="padding:6px 0;color:#7d7067;">Ville</td><td>' . htmlspecialchars($order['city']) . '</td></tr>
            <tr><td style="padding:6px 0;color:#7d7067;">Total</td><td style="font-weight:800;color:#a24f2b;font-size:16px;">' . number_format((float)$order['total'], 2) . ' MAD</td></tr>
        </table>
        <a href="https://mangashop.ma/admin/index.php" style="display:inline-block;margin-top:20px;padding:12px 24px;background:#31231e;color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:14px;">Voir dans le dashboard</a>
    </div></body></html>';

    return sendMail(SITE_EMAIL, $subject, $html);
}

function sendOrderStatusEmail(array $order) {
    if (empty($order['customer_email'])) return false;

    $statusLabels = [
        'processing' => 'Commande confirmee',
        'shipped'    => 'Commande expediee',
        'delivered'  => 'Commande livree',
        'cancelled'  => 'Commande annulee',
    ];
    $statusMsg = [
        'processing' => 'Votre commande a ete confirmee et est en cours de preparation.',
        'shipped'    => 'Votre commande a ete expediee. Elle arrivera dans 24-48h.',
        'delivered'  => 'Votre commande a ete livree. Bonne lecture !',
        'cancelled'  => 'Votre commande a ete annulee. Contactez-nous si vous avez des questions.',
    ];

    if (!isset($statusLabels[$order['status']])) return false;

    $subject = $statusLabels[$order['status']] . ' #' . $order['order_number'] . ' - MangaShop';
    $msg = $statusMsg[$order['status']];
    $html = '<!DOCTYPE html><html><body style="font-family:sans-serif;background:#f9f7f3;">
    <div style="max-width:500px;margin:40px auto;background:#fff;border-radius:12px;padding:28px;border:1px solid #e2dcd3;text-align:center;">
        <h2 style="color:#31231e;margin:16px 0 8px;">' . $statusLabels[$order['status']] . '</h2>
        <p style="color:#7d7067;font-size:14px;margin-bottom:20px;">' . $msg . '</p>
        <p style="font-size:14px;color:#31231e;font-weight:700;">Commande #' . $order['order_number'] . '</p>
        <p style="font-size:13px;color:#a49e97;margin-top:24px;"> ' . date('Y') . ' MangaShop</p>
    </div></body></html>';

    return sendMail($order['customer_email'], $subject, $html);
}
