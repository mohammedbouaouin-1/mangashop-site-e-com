
<div class="section-tab" id="tab-products">
  <div class="page-header">
    <div>
      <h1 class="page-title">Stock Manga</h1>
      <div class="page-subtitle">Gestion complète du catalogue de mangas.</div>
    </div>
    <button onclick="openModal('productModal')" style="padding:12px 24px; background:var(--ink); color:#fff; border-radius:12px; font-weight:700; border:none; cursor:pointer; display:flex; align-items:center; gap:8px; transition:0.3s;" onmouseover="this.style.background='var(--primary)'">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Nouveau Produit
    </button>
  </div>
  <div class="card">
    <div class="card-head">
      <h3>Tous les produits</h3>
      <div class="search-box">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="searchProducts" placeholder="Titre, auteur..." oninput="filterTable('productsTable','searchProducts')">
      </div>
    </div>
    <div class="table-container">
      <table id="productsTable" data-search-cols="1,2">
        <thead>
          <tr><th>Couverture</th><th>Titre / Auteur</th><th>Prix Unitaire</th><th>Badge</th><th>Stock</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach($products as $p): ?>
          <tr>
            <td>
              <img src="<?= asset($p['image_url']) ?>" class="prod-preview" referrerpolicy="no-referrer" onerror="this.src='../assets/img/placeholder.jpg'">
            </td>
            <td>
              <div style="font-weight:800; color:var(--ink); font-size:15px;"><?= e($p['title']) ?></div>
              <div style="font-size:13px; color:var(--muted); font-weight:500;"><?= e($p['author']) ?></div>
            </td>
            <td style="font-weight:900; color:#000; font-size:16px;">
              <?= number_format($p['price'], 2) ?> <span style="font-size:11px;">MAD</span>
              <?php if($p['old_price']): ?>
                <div style="text-decoration:line-through; color:var(--muted); font-size:11px;"><?= number_format($p['old_price'], 2) ?></div>
              <?php endif; ?>
            </td>
            <td>
              <?php if($p['badge']): ?>
                <span class="badge" style="background:var(--bg-alt); color:var(--ink); border:1px solid var(--border);"><?= ucfirst($p['badge']) ?></span>
              <?php else: ?>
                <span style="color:var(--muted); font-size:11px; font-weight:600; text-transform:uppercase;">Classique</span>
              <?php endif; ?>
            </td>
            <td>
              <span style="font-weight:700; color:<?= $p['stock'] <= 5 ? 'var(--red)' : ($p['stock'] < 10 ? '#d97706' : 'var(--ink)') ?>"><?= $p['stock'] ?><?= $p['stock'] <= 5 ? ' ' : '' ?></span>
            </td>
            <td>
              <div style="display:flex; gap:10px;">
                <button onclick='editProduct(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, "UTF-8") ?>)' style="background:none; border:none; color:var(--primary); cursor:pointer; padding:5px;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </button>
                <form method="POST" style="display:inline" onsubmit="return confirm('Voulez-vous vraiment supprimer ce produit ?')">
                  <input type="hidden" name="action" value="delete_product">
                  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                  <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                  <button type="submit" style="background:none; border:none; color:var(--red); cursor:pointer; padding:5px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
