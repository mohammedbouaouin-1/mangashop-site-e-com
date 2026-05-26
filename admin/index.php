<?php
require_once '../includes/config.php';
$db = getDB();


try { $db->query("SELECT role FROM users LIMIT 1"); } catch (Exception $e) {
    $db->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'user'");
}
try { $db->query("SELECT tracking_number FROM orders LIMIT 1"); } catch (Exception $e) {
    $db->exec("ALTER TABLE orders ADD COLUMN tracking_number VARCHAR(100) DEFAULT NULL");
}
try { $db->query("SELECT livreur_id FROM orders LIMIT 1"); } catch (Exception $e) {
    $db->exec("ALTER TABLE orders ADD COLUMN livreur_id INT DEFAULT NULL");
}
try { $db->query("SELECT id FROM login_attempts LIMIT 1"); } catch (Exception $e) {
    $db->exec("CREATE TABLE IF NOT EXISTS login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip VARCHAR(45) NOT NULL,
        attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_ip_time (ip, attempted_at)
    )");
}

try { $db->query("SELECT user_id FROM reviews LIMIT 1"); } catch (Exception $e) {
    $db->exec("ALTER TABLE reviews ADD COLUMN user_id INT DEFAULT NULL, ADD CONSTRAINT fk_review_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL");
}
try { $db->query("SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_NAME='reviews' AND CONSTRAINT_NAME='uq_user_product_review' AND TABLE_SCHEMA=DATABASE()"); } catch (Exception $e) {}

$uqCheck = $db->query("SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_NAME='reviews' AND CONSTRAINT_NAME='uq_user_product_review' AND TABLE_SCHEMA=DATABASE()");
if ((int)$uqCheck->fetchColumn() === 0) {
    try { $db->exec("ALTER TABLE reviews ADD UNIQUE KEY uq_user_product_review (user_id, product_id)"); } catch (Exception $e) {}
}

try {
    $fkCheck = $db->query("SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME='orders' AND CONSTRAINT_NAME='orders_ibfk_livreur' AND TABLE_SCHEMA=DATABASE()");
    if ((int)$fkCheck->fetchColumn() === 0) {
        $db->exec("ALTER TABLE orders ADD CONSTRAINT orders_ibfk_livreur FOREIGN KEY (livreur_id) REFERENCES users(id) ON DELETE SET NULL");
    }
} catch (Exception $e) {}


$checkAdmin = $db->prepare("SELECT id FROM users WHERE email = ?");
$checkAdmin->execute(['admin@mangashop.ma']);
if (!$checkAdmin->fetch()) {
    $randomPass = bin2hex(random_bytes(8)); 
    $db->prepare("INSERT INTO users (name, email, password, role) VALUES ('Administrateur', 'admin@mangashop.ma', ?, 'admin')")
       ->execute([password_hash($randomPass, PASSWORD_DEFAULT)]);
    
    error_log("[MangaShop Setup] Compte admin créé — Email: admin@mangashop.ma — Mot de passe: $randomPass");
    
    if (in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1'])) {
        echo '<div style="position:fixed;top:0;left:0;right:0;z-index:9999;background:#1e3a5f;color:#fff;padding:16px 24px;font-family:monospace;font-size:14px;">';
        echo ' Compte admin créé pour la première fois.<br>';
        echo "Email: <strong>admin@mangashop.ma</strong> | Mot de passe: <strong>$randomPass</strong><br>";
        echo '<em>Notez ce mot de passe maintenant — il ne sera plus affiché.</em>';
        echo '</div><div style="height:80px"></div>';
    }
}


if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
function csrf_token(): string { return $_SESSION['csrf_token']; }
function csrf_check(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('<div style="padding:40px;font-family:sans-serif;color:#b91c1c;background:#fee2e2;border-radius:12px;text-align:center;">
            <strong>Erreur de sécurité</strong> — Token CSRF invalide.<br>
            <a href="index.php">Retour au dashboard</a></div>');
    }
}


function getClientIp(): string {
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}
function isRateLimited(PDO $db): bool {
    $ip   = getClientIp();
    $window = date('Y-m-d H:i:s', strtotime('-15 minutes'));
    $stmt = $db->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND attempted_at > ?");
    $stmt->execute([$ip, $window]);
    return (int)$stmt->fetchColumn() >= 5;
}
function recordAttempt(PDO $db): void {
    $ip = getClientIp();
    $db->prepare("INSERT INTO login_attempts (ip) VALUES (?)")->execute([$ip]);
    
    $db->exec("DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)");
}


$rateLimited = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_email'], $_POST['admin_pass'])) {
    if (isRateLimited($db)) {
        $rateLimited = true;
        $authError   = 'too_many';
    } else {
        $email = trim($_POST['admin_email']);
        $pass  = $_POST['admin_pass'];
        $stmt  = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($pass, $admin['password'])) {
            $_SESSION['admin']      = true;
            $_SESSION['admin_user'] = $admin;
            
            session_regenerate_id(true);
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } else {
            recordAttempt($db);
            $authError = 'invalid';
        }
    }
}
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ../login.php'); exit;
}


if ($_SESSION['admin'] ?? false) {

    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') csrf_check();

    
    function logAdminAction(PDO $db, string $action, string $details): void {
        $user = $_SESSION['admin_user'] ?? [];
        try {
            $db->prepare("INSERT INTO admin_activity_logs (user_id, admin_name, action, details, ip) VALUES (?,?,?,?,?)")
               ->execute([(int)($user['id'] ?? 0), $user['name'] ?? 'Admin', $action, $details, $_SERVER['REMOTE_ADDR'] ?? '']);
        } catch (Exception $e) {}
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {
        $allowedStatus = ['pending','processing','shipped','delivered','cancelled'];
        $newStatus = in_array($_POST['status'], $allowedStatus) ? $_POST['status'] : 'pending';
        $tracking  = trim($_POST['tracking_number'] ?? '');
        
        $livreur_id = isset($_POST['livreur_id'])
            ? (!empty($_POST['livreur_id']) ? (int)$_POST['livreur_id'] : null)
            : $db->query("SELECT livreur_id FROM orders WHERE id = " . (int)$_POST['order_id'])->fetchColumn();
        $db->prepare("UPDATE orders SET status=?, tracking_number=?, livreur_id=? WHERE id=?")
           ->execute([$newStatus, $tracking ?: null, $livreur_id ?: null, (int)$_POST['order_id']]);
        require_once '../includes/mailer.php';
        $updatedOrder = $db->prepare("SELECT * FROM orders WHERE id=?");
        $updatedOrder->execute([(int)$_POST['order_id']]);
        $orderRow = $updatedOrder->fetch();
        if ($orderRow) sendOrderStatusEmail($orderRow);
        logAdminAction($db, 'update_status', "Commande #{$_POST['order_id']} → $newStatus");
        $_SESSION['admin_msg'] = 'Statut mis à jour.';
        header('Location: index.php?tab=orders'); exit;
    }

    if ($action === 'delete_order') {
        $db->prepare("DELETE FROM order_items WHERE order_id=?")->execute([(int)$_POST['order_id']]);
        $db->prepare("DELETE FROM orders WHERE id=?")->execute([(int)$_POST['order_id']]);
        logAdminAction($db, 'delete_order', "Commande #{$_POST['order_id']} supprimée");
        $_SESSION['admin_msg'] = 'Commande supprimée.';
        header('Location: index.php?tab=orders'); exit;
    }

    if ($action === 'save_product') {
        $pid    = (int)($_POST['product_id'] ?? 0);
        $title  = trim($_POST['title']);
        $slug   = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $cat_id = (int)$_POST['category_id'];
        $price  = (float)$_POST['price'];
        $old_p  = !empty($_POST['old_price']) ? (float)$_POST['old_price'] : null;
        $stock  = (int)$_POST['stock'];
        $author = trim($_POST['author']);
        $badge  = $_POST['badge'] ?? '';
        $featured = isset($_POST['featured']) ? 1 : 0;
        $is_new   = isset($_POST['is_new'])   ? 1 : 0;
        $desc   = trim($_POST['description']);
        
        $raw_img = trim($_POST['image_url']);
        $img = '';
        if (preg_match('/^(assets\/|https?:\/\/)/i', $raw_img)) {
            $img = preg_replace('/[<>"\'\\\\]/', '', $raw_img);
        }

        if ($pid > 0) {
            $sql = "UPDATE products SET title=?, slug=?, category_id=?, price=?, old_price=?, stock=?, author=?, image_url=?, description=?, badge=?, featured=?, is_new=? WHERE id=?";
            $db->prepare($sql)->execute([$title, $slug, $cat_id, $price, $old_p, $stock, $author, $img, $desc, $badge, $featured, $is_new, $pid]);
            logAdminAction($db, 'update_product', "Produit #$pid '$title' modifié");
            $_SESSION['admin_msg'] = "Produit '$title' mis à jour.";
        } else {
            $sql = "INSERT INTO products (title, slug, category_id, price, old_price, stock, author, image_url, description, badge, featured, is_new) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
            $db->prepare($sql)->execute([$title, $slug, $cat_id, $price, $old_p, $stock, $author, $img, $desc, $badge, $featured, $is_new]);
            logAdminAction($db, 'add_product', "Nouveau produit '$title' ajouté");
            $_SESSION['admin_msg'] = "Produit '$title' ajouté au catalogue.";
        }
        header('Location: index.php?tab=products'); exit;
    }

    if ($action === 'delete_product') {
        $db->prepare("DELETE FROM products WHERE id=?")->execute([(int)$_POST['product_id']]);
        logAdminAction($db, 'delete_product', "Produit #{$_POST['product_id']} supprimé");
        $_SESSION['admin_msg'] = "Produit supprimé.";
        header('Location: index.php?tab=products'); exit;
    }

    if ($action === 'save_category') {
        $cid   = (int)($_POST['category_id'] ?? 0);
        $name  = trim($_POST['name']);
        $slug  = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $icon  = trim($_POST['icon'] ?? '');
        $color = trim($_POST['color'] ?? '#f1ede6');
        $desc  = trim($_POST['description'] ?? '');
        if ($cid > 0) {
            $db->prepare("UPDATE categories SET name=?, slug=?, icon=?, color=?, description=? WHERE id=?")
               ->execute([$name, $slug, $icon, $color, $desc, $cid]);
            $_SESSION['admin_msg'] = "Rayon '$name' mis à jour.";
        } else {
            $db->prepare("INSERT INTO categories (name, slug, icon, color, description) VALUES (?,?,?,?,?)")
               ->execute([$name, $slug, $icon, $color, $desc]);
            $_SESSION['admin_msg'] = "Nouveau rayon '$name' créé.";
        }
        header('Location: index.php?tab=categories'); exit;
    }

    if ($action === 'delete_category') {
        $db->prepare("DELETE FROM categories WHERE id=?")->execute([(int)$_POST['category_id']]);
        $_SESSION['admin_msg'] = "Rayon supprimé.";
        header('Location: index.php?tab=categories'); exit;
    }

    if ($action === 'delete_devis') {
        $db->prepare("DELETE FROM devis WHERE id=?")->execute([(int)$_POST['id']]);
        $_SESSION['admin_msg'] = "Devis supprimé.";
        header('Location: index.php?tab=devis'); exit;
    }

    if ($action === 'delete_newsletter') {
        $db->prepare("DELETE FROM newsletter WHERE id=?")->execute([(int)$_POST['id']]);
        $_SESSION['admin_msg'] = "Abonné retiré.";
        header('Location: index.php?tab=newsletter'); exit;
    }

    if ($action === 'save_coupon') {
        $code    = strtoupper(trim($_POST['code'] ?? ''));
        $pct     = max(1, min(100, (int)($_POST['discount_pct'] ?? 10)));
        $maxUses = max(1, (int)($_POST['max_uses'] ?? 100));
        $expires = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;
        $active  = isset($_POST['active']) ? 1 : 0;
        $cid     = (int)($_POST['coupon_id'] ?? 0);
        if ($code) {
            if ($cid) {
                $db->prepare("UPDATE promo_codes SET code=?,discount_pct=?,max_uses=?,expires_at=?,active=? WHERE id=?")
                   ->execute([$code,$pct,$maxUses,$expires,$active,$cid]);
            } else {
                $db->prepare("INSERT INTO promo_codes (code,discount_pct,max_uses,expires_at,active) VALUES (?,?,?,?,?)")
                   ->execute([$code,$pct,$maxUses,$expires,$active]);
            }
        }
        $_SESSION['admin_msg'] = "Code promo enregistré.";
        header('Location: index.php?tab=coupons'); exit;
    }

    if ($action === 'delete_coupon') {
        $db->prepare("DELETE FROM promo_codes WHERE id=?")->execute([(int)$_POST['coupon_id']]);
        $_SESSION['admin_msg'] = "Code promo supprimé.";
        header('Location: index.php?tab=coupons'); exit;
    }

    if ($action === 'save_bundle') {
        $bid   = (int)($_POST['bundle_id'] ?? 0);
        $name  = trim($_POST['name']);
        $slug  = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $price = (float)$_POST['price'];
        $old_p = !empty($_POST['old_price']) ? (float)$_POST['old_price'] : null;
        $raw_img = trim($_POST['image_url'] ?? '');
        $img = preg_match('/^(assets\/|https?:\/\/)/i', $raw_img) ? preg_replace('/[<>"\'\\\\]/', '', $raw_img) : '';
        $desc  = trim($_POST['description'] ?? '');
        if ($bid > 0) {
            $db->prepare("UPDATE bundles SET name=?, slug=?, price=?, old_price=?, image_url=?, description=? WHERE id=?")
               ->execute([$name, $slug, $price, $old_p, $img, $desc, $bid]);
            $_SESSION['admin_msg'] = "Bundle '$name' mis à jour.";
        } else {
            $db->prepare("INSERT INTO bundles (name, slug, price, old_price, image_url, description) VALUES (?,?,?,?,?,?)")
               ->execute([$name, $slug, $price, $old_p, $img, $desc]);
            $_SESSION['admin_msg'] = "Bundle '$name' créé.";
        }
        header('Location: index.php?tab=bundles'); exit;
    }

    if ($action === 'delete_bundle') {
        $bid = (int)$_POST['bundle_id'];
        $db->prepare("DELETE FROM bundle_products WHERE bundle_id=?")->execute([$bid]);
        $db->prepare("DELETE FROM bundles WHERE id=?")->execute([$bid]);
        $_SESSION['admin_msg'] = "Bundle supprimé.";
        header('Location: index.php?tab=bundles'); exit;
    }

    if ($action === 'save_user') {
        $uid   = (int)($_POST['user_id'] ?? 0);
        $name  = trim($_POST['name']);
        $email = trim($_POST['email']);
        $role  = in_array($_POST['role'] ?? '', ['admin','user','livreur']) ? $_POST['role'] : 'user';
        $phone = trim($_POST['phone']);
        $addr  = trim($_POST['address']);
        $city  = $_POST['city'];
        $pass  = $_POST['password'] ?? '';
        if ($uid > 0) {
            if (!empty($pass)) {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $db->prepare("UPDATE users SET name=?, email=?, password=?, role=?, phone=?, address=?, city=? WHERE id=?")
                   ->execute([$name, $email, $hash, $role, $phone, $addr, $city, $uid]);
            } else {
                $db->prepare("UPDATE users SET name=?, email=?, role=?, phone=?, address=?, city=? WHERE id=?")
                   ->execute([$name, $email, $role, $phone, $addr, $city, $uid]);
            }
            $_SESSION['admin_msg'] = "Utilisateur '$name' mis à jour.";
        } else {
            $hash = password_hash($pass ?: 'mangashop2024', PASSWORD_DEFAULT);
            $db->prepare("INSERT INTO users (name, email, password, role, phone, address, city) VALUES (?,?,?,?,?,?,?)")
               ->execute([$name, $email, $hash, $role, $phone, $addr, $city]);
            $_SESSION['admin_msg'] = "Utilisateur '$name' créé.";
        }
        header('Location: index.php?tab=users'); exit;
    }

    if ($action === 'delete_user') {
        $db->prepare("DELETE FROM users WHERE id=?")->execute([(int)$_POST['user_id']]);
        $_SESSION['admin_msg'] = "Compte supprimé.";
        header('Location: index.php?tab=users'); exit;
    }

    if ($action === 'reset_data' && ($_POST['confirm_reset'] ?? '') === 'RESET') {
        $db->exec("DELETE FROM order_items");
        $db->exec("DELETE FROM orders");
        $db->exec("DELETE FROM newsletter");
        $db->exec("DELETE FROM devis");
        $_SESSION['admin_msg'] = ' Données réinitialisées.';
        header('Location: index.php'); exit;
    }

    
    $successMsg = $_SESSION['admin_msg'] ?? null;
    unset($_SESSION['admin_msg']);

    $today = date('Y-m-d');
    $stats = [
        'orders'        => $db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
        'orders_today'  => $db->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn(),
        'revenue'       => $db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status != 'cancelled'")->fetchColumn(),
        'revenue_today' => $db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE DATE(created_at) = CURDATE() AND status != 'cancelled'")->fetchColumn(),
        'products'      => $db->query("SELECT COUNT(*) FROM products")->fetchColumn(),
        'low_stock'     => $db->query("SELECT COUNT(*) FROM products WHERE stock <= 5")->fetchColumn(),
        'customers'     => $db->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn(),
        'bundles'       => $db->query("SELECT COUNT(*) FROM bundles")->fetchColumn(),
    ];
    $orders      = $db->query("SELECT o.*, u.name as livreur_name FROM orders o LEFT JOIN users u ON o.livreur_id = u.id ORDER BY o.created_at DESC LIMIT 50")->fetchAll();
    $products    = $db->query("SELECT p.*,c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id ORDER BY p.id DESC LIMIT 100")->fetchAll();
    $low_stock_products = $db->query("SELECT * FROM products WHERE stock <= 5 ORDER BY stock ASC LIMIT 10")->fetchAll();
    $devis       = $db->query("SELECT * FROM devis ORDER BY created_at DESC LIMIT 50")->fetchAll();
    $newsletters = $db->query("SELECT COUNT(*) FROM newsletter")->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin — MangaShop</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;700;900&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #f9f7f3;
      --bg-alt: #f0ebe1;
      --white: #ffffff;
      --ink: #31231e;
      --ink-soft: #685c53;
      --muted: #9c9288;
      --border: #e2dcd3;
      --primary: #a24f2b;
      --gold: #c69c6d;
      --red: #b83b3b;
      --green: #4a7c59;
      --radius-sm: 8px; --radius-md: 14px; --radius-lg: 24px;
      --shadow: 0 10px 30px -10px rgba(42,30,26,0.08),0 4px 6px -2px rgba(0,0,0,0.02);
      --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--ink); line-height:1.5; -webkit-font-smoothing:antialiased; overflow-x:hidden; }

    .admin-layout { display:grid; grid-template-columns:260px 1fr; min-height:100vh; }
    @media(max-width:1024px){.admin-layout{grid-template-columns:80px 1fr;}}
    @media(max-width:768px){.admin-layout{grid-template-columns:1fr;}}

    .sidebar { background:var(--ink); color: #fff; display: flex; flex-direction: column; }
    .sidebar-logo { padding:32px 28px; font-family:'Playfair Display',serif; font-size:24px; font-weight:900; letter-spacing:-0.02em; border-bottom:1px solid rgba(255,255,255,0.08); }
    .sidebar-logo span { color:var(--primary); }
    .sidebar-nav { padding:32px 14px; flex:1; overflow-y:auto; }
    .nav-item { display:flex; align-items:center; gap:14px; padding:14px 18px; margin-bottom:8px; border-radius:12px; font-size:14px; font-weight:600; color:rgba(255,255,255,0.6); cursor:pointer; transition:all 0.3s cubic-bezier(0.16,1,0.3,1); }
    .nav-item:not(.active):hover { color: #fff; background:rgba(255,255,255,0.05); }
    .nav-item.active { color: #fff; background: var(--primary); }
    .nav-count { margin-left:auto; background:rgba(255,255,255,0.15); color: #fff; font-size:11px; padding:2px 8px; border-radius:8px; }
    .nav-item.active .nav-count { background:rgba(0,0,0,0.1); }
    .nav-alert { margin-left:auto; background: var(--red); color: #fff; font-size:10px; font-weight:800; padding:2px 6px; border-radius:8px; }
    .sidebar-footer { padding:24px; background:rgba(0,0,0,0.1); }
    .sidebar-footer a { display:flex; align-items:center; gap:10px; font-size:12px; color:rgba(255,255,255,0.4); padding:8px 12px; border-radius:8px; transition:all 0.2s; text-decoration:none; }
    .sidebar-footer a:hover { color: #fff; background: rgba(255,255,255,0.05); }

    .admin-main { padding:64px; max-width:1400px; margin:0 auto; width:100%; min-width:0; }
    @media(max-width:768px){.admin-main{padding:32px 20px;}}

    .page-header { display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:48px; flex-wrap:wrap; gap:16px; }
    .page-title { font-family:'Playfair Display',serif; font-size:38px; font-weight:900; color:var(--ink); letter-spacing:-0.03em; line-height:1; }
    .page-subtitle { font-size:14px; color:var(--muted); margin-top:8px; font-weight:500; }

    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:28px; margin-bottom:48px; }
    @media(max-width:1200px){.stats-grid{grid-template-columns:repeat(2,1fr);}}
    @media(max-width:600px){.stats-grid{grid-template-columns:1fr;}}
    .stat-card { background:var(--white); border-radius:var(--radius-lg); padding:32px; border:1px solid var(--border); box-shadow:var(--shadow); display:flex; flex-direction:column; position:relative; overflow:hidden; transition:all 0.4s cubic-bezier(0.16,1,0.3,1); }
    .stat-card:hover { transform:translateY(-6px) scale(1.02); border-color:var(--primary); }
    .stat-card::after { content:''; position:absolute; top:0; left:0; width:4px; height:100%; background:var(--primary); opacity:0; transition:0.3s; }
    .stat-card:hover::after { opacity:1; }
    .stat-label { font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.15em; color:var(--muted); margin-bottom:4px; }
    .stat-value { font-family:'Playfair Display',serif; font-size:36px; font-weight:950; color:var(--ink); letter-spacing:-0.02em; }
    .stat-sub { font-size:12px; color:var(--green); font-weight:700; margin-top:6px; }
    .stat-sub.warn { color:var(--red); }
    .stat-icon { position:absolute; top:32px; right:32px; width:48px; height:48px; background:var(--bg); border-radius:16px; display:flex; align-items:center; justify-content:center; color:var(--primary); transition:0.3s; }
    .stat-card:hover .stat-icon { background:var(--primary); color: #fff; }

    .card { background:var(--white); border-radius:var(--radius-lg); border:1px solid var(--border); box-shadow:var(--shadow); overflow:hidden; margin-bottom:40px; }
    .card-head { padding:24px 32px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--border); background: var(--bg); }
    .card-head h3 { font-family:'Playfair Display',serif; font-size:18px; font-weight:800; color:var(--ink); }
    .search-box { display:flex; align-items:center; gap:10px; background:var(--bg); border:1.5px solid var(--border); border-radius:10px; padding:8px 14px; width:260px; transition:0.2s; }
    .search-box:focus-within { border-color:var(--primary); background: var(--white); }
    .search-box input { border:none; background:none; outline:none; font-size:13px; font-family:inherit; color:var(--ink); width:100%; }

    .table-container { overflow-x:auto; width:100%; -webkit-overflow-scrolling:touch; }
    table { width:100%; border-collapse:collapse; min-width:800px; }
    th { text-align:left; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; color:var(--muted); padding:16px 20px; background: var(--bg-alt); }
    td { padding:16px 20px; border-bottom:1px solid var(--border); vertical-align:middle; transition:background 0.2s; }
    tr:last-child td { border-bottom:none; }
    tr:hover td { background:var(--bg); }
    .client-box { display:flex; align-items:center; gap:14px; }
    .client-avatar { width:36px; height:36px; background:var(--bg-alt); border-radius:10px; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:13px; color:var(--primary); border:1px solid var(--border); flex-shrink:0; }
    .badge { display:inline-flex; align-items:center; padding:6px 14px; border-radius:20px; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; }
    .b-pending{background: #fff8e6; color: #d97706;}
    .b-processing{background: #eef2ff; color: #4f46e5;}
    .b-shipped{background: #ecfdf5; color: #059669;}
    .b-delivered{background: #e6f4ea; color: #137333;}
    .b-cancelled{background: #fcecec; color: #b83b3b;}
    select.inline-select { border:1.5px solid var(--border); padding:6px 12px; border-radius:8px; font-size:12px; font-weight:600; background: var(--white); }
    select.inline-select:hover { border-color:var(--primary); background:var(--bg); }
    .prod-preview { width:48px; height:64px; object-fit:cover; border-radius:8px; box-shadow:var(--shadow-sm); border:1px solid var(--border); background:var(--bg-alt); }
    .section-tab { display:none; }
    .section-tab.active { display:block; animation:fadeIn 0.4s ease-out; }
    @keyframes fadeIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
    .btn { padding:14px 24px; border-radius:12px; font-weight:700; cursor:pointer; transition:0.2s; border:none; font-size:14px; }
    .btn-primary { background:var(--ink); color: #fff; }
    .btn-primary:hover { background:var(--primary); transform:translateY(-2px); }
    .btn-ghost { background:var(--bg); color:var(--ink-soft); }
    .stock-alert { background: #ffebeb; color: #b83b3b; font-weight:700; padding:2px 8px; border-radius:4px; font-size:11px; }

    
    .login-container { min-height:100vh; display:flex; align-items:center; justify-content:center; background:var(--bg-alt); padding:20px; }
    .login-box { background: var(--white); border-radius: var(--radius-lg); padding: 48px; max-width: 440px; width: 100%; border: 1px solid var(--border); box-shadow: var(--shadow); text-align: center; }
    .login-logo { width:64px; height:64px; background:var(--ink); color: #fff; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; }
    .login-box h1 { font-family:'Playfair Display',serif; font-size:32px; font-weight:900; margin-bottom:12px; color:var(--ink); }
    .login-box p { color:var(--muted); margin-bottom:40px; font-size:15px; }
    .login-input { width:100%; padding:16px 20px; background: var(--bg); border: 1.5px solid var(--border); border-radius: 12px; outline: none; font-size: 14px; font-family: inherit; margin-bottom: 20px; color: var(--ink); transition: 0.2s; }
    .login-input:focus { border-color:var(--primary); background: var(--white); }
    .login-btn { width:100%; padding:18px; background:var(--ink); color: #fff; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.2s; font-size: 15px; }
    .login-btn:hover:not(:disabled) { background:var(--primary); transform:translateY(-2px); }
    .login-btn:disabled { opacity:0.5; cursor:not-allowed; }
  </style>
</head>
<body>

<?php if (!($_SESSION['admin'] ?? false)): ?>
<div class="login-container">
  <div class="login-box">
    <div class="login-logo">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
    </div>
    <h1>Manga<span style="color:var(--primary)">Shop</span></h1>
    <p>Accès réservé à l'administration</p>
    <?php if (isset($authError)): ?>
    <div style="color:var(--red);font-weight:700;margin-bottom:20px;font-size:13px;background:#fff1f1;padding:12px;border-radius:10px;">
      <?= $authError === 'too_many'
        ? ' Trop de tentatives. Réessayez dans 15 minutes.'
        : ' Accès refusé. Vérifiez vos identifiants.' ?>
    </div>
    <?php endif; ?>
    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <div style="text-align:left;margin-bottom:20px;">
        <label style="font-size:12px;font-weight:700;color:var(--ink);margin-bottom:8px;display:block;text-transform:uppercase;letter-spacing:0.05em;">Adresse Email</label>
        <input type="email" name="admin_email" class="login-input" placeholder="admin@mangashop.ma" required autofocus <?= ($rateLimited ? 'disabled' : '') ?> style="margin-bottom:0;">
      </div>
      <div style="text-align:left;margin-bottom:32px;">
        <label style="font-size:12px;font-weight:700;color:var(--ink);margin-bottom:8px;display:block;text-transform:uppercase;letter-spacing:0.05em;">Mot de passe</label>
        <input type="password" name="admin_pass" class="login-input" placeholder="••••••••" required <?= ($rateLimited ? 'disabled' : '') ?> style="margin-bottom:0;">
      </div>
      <button type="submit" class="login-btn" <?= ($rateLimited ? 'disabled' : '') ?>>Accéder au Terminal</button>
    </form>
  </div>
</div>

<?php else: ?>
<div class="admin-layout">
  <aside class="sidebar">
    <div class="sidebar-logo">Manga<span>Shop</span></div>
    <div class="sidebar-nav">
      <div class="nav-item active" onclick="showTab('dashboard',this)">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        Vue d'ensemble
      </div>
      <div class="nav-item" onclick="showTab('orders',this)">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
        Commandes <span class="nav-count"><?= $stats['orders'] ?></span>
      </div>
      <div class="nav-item" onclick="showTab('products',this)">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        Catalogue
        <?php if($stats['low_stock'] > 0): ?>
        <span class="nav-alert"><?= $stats['low_stock'] ?></span>
        <?php endif; ?>
      </div>
      <div class="nav-item" onclick="showTab('bundles',this)">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        Bundles &amp; Packs
      </div>
      <div class="nav-item" onclick="showTab('devis',this)">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Devis Perso
      </div>
      <div class="nav-item" onclick="showTab('newsletter',this)">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        Newsletter
      </div>
      <div class="nav-item" onclick="showTab('coupons',this)">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        Codes Promo
      </div>

      <div class="nav-item" onclick="showTab('users',this)">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Clients <span class="nav-count"><?= $stats['customers'] ?></span>
      </div>
    </div>
    <div class="sidebar-footer">
      <a href="../index.php" target="_blank" style="margin-bottom:8px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        Aller sur le magasin
      </a>
      <a href="?logout=1">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Déconnexion
      </a>
      <button onclick="document.getElementById('resetModal').style.display='flex'" style="margin-top:8px;width:100%;padding:10px 16px;background:#af3e3e;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px;justify-content:center;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.5"/></svg>
        Réinitialiser les données
      </button>
    </div>
  </aside>

  <main class="admin-main">
    <?php if ($successMsg): ?>
    <div id="adminToast" style="position:fixed;top:24px;right:24px;z-index:9999;background:#065f46;color:#fff;padding:14px 24px;border-radius:12px;font-size:14px;font-weight:700;box-shadow:0 10px 30px rgba(0,0,0,0.15);display:flex;align-items:center;gap:10px;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
      <?= e($successMsg) ?>
    </div>
    <script>setTimeout(()=>{const t=document.getElementById('adminToast');if(t)t.remove();},3000);</script>
    <?php endif; ?>

    <?php
    include 'includes/tab_dashboard.php';
    include 'includes/tab_orders.php';
    include 'includes/tab_products.php';
    // include 'includes/tab_categories.php';
    include 'includes/tab_devis.php';
    include 'includes/tab_newsletter.php';
    include 'includes/tab_bundles.php';
    include 'includes/tab_users.php';
    include 'includes/tab_coupons.php';
    ?>
  </main>
</div>

<!-- Reset Modal -->
<div id="resetModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:20px;padding:40px;max-width:420px;width:90%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
    <div style="font-size:40px;margin-bottom:16px;"></div>
    <h3 style="font-size:20px;font-weight:800;color:var(--ink);margin-bottom:8px;">Réinitialiser les données ?</h3>
    <p style="font-size:14px;color:var(--muted);margin-bottom:24px;">Cette action supprimera toutes les commandes, newsletters et devis. Irréversible.</p>
    <form method="POST">
      <input type="hidden" name="action" value="reset_data">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <input type="text" name="confirm_reset" placeholder='Tapez "RESET" pour confirmer' style="width:100%;padding:12px 16px;border:2px solid var(--border);border-radius:10px;font-size:14px;margin-bottom:16px;font-family:inherit;text-align:center;">
      <div style="display:flex;gap:12px;justify-content:center;">
        <button type="button" onclick="document.getElementById('resetModal').style.display='none'" class="btn btn-ghost">Annuler</button>
        <button type="submit" style="padding:14px 24px;background:var(--red);color:#fff;border:none;border-radius:12px;font-weight:700;cursor:pointer;">Confirmer</button>
      </div>
    </form>
  </div>
</div>

<script>
function showTab(id, el) {
  document.querySelectorAll('.section-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  const target = document.getElementById('tab-' + id);
  if (target) target.classList.add('active');
  if (el) el.classList.add('active');
  else document.querySelectorAll('.nav-item').forEach(n => {
    if (n.getAttribute('onclick')?.includes("'" + id + "'")) n.classList.add('active');
  });
  history.replaceState(null, '', '?tab=' + id);
}
document.addEventListener('DOMContentLoaded', () => {
  const urlTab = new URLSearchParams(window.location.search).get('tab');
  if (urlTab && document.getElementById('tab-' + urlTab)) showTab(urlTab);
});
</script>
<?php include 'includes/modals.php'; ?>
<?php endif; ?>
</body>
</html>
