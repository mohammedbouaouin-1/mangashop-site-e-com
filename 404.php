<?php
require_once 'includes/config.php';
http_response_code(404);
$pageTitle = 'Page introuvable';
require_once 'includes/header.php';
$db = getDB();
$suggestions = $db->query("SELECT p.*,c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id ORDER BY p.featured DESC LIMIT 6")->fetchAll();
?>
<div style="max-width:900px;margin:80px auto;padding:0 32px;text-align:center">
  <div style="font-family:'Playfair Display',serif;font-size:96px;font-weight:900;color:var(--border);line-height:1;margin-bottom:16px">404</div>
  <h1 style="font-family:'Playfair Display',serif;font-size:26px;margin-bottom:10px">Page introuvable</h1>
  <p style="color:var(--muted);font-size:14px;margin-bottom:32px">Cette page n'existe pas ou a ete deplacee.</p>
  <div style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;margin-bottom:56px">
    <a href="index.php" class="btn-primary">Retour a l'accueil</a>
    <a href="catalogue.php" style="padding:13px 24px;border:1.5px solid var(--border);border-radius:8px;font-size:14px;font-weight:600;color:var(--ink)">Voir le catalogue</a>
  </div>
  <?php if ($suggestions): ?>
  <h2 style="font-family:'Playfair Display',serif;font-size:20px;margin-bottom:20px;text-align:left">Best-Sellers</h2>
  <div class="products-grid" style="text-align:left">
    <?php foreach ($suggestions as $p): include 'includes/product_card.php'; endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<?php require_once 'includes/footer.php'; ?>
