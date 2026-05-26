
<div class="section-tab" id="tab-bundles">
  <div class="page-header">
    <div>
      <h1 class="page-title">Bundles & Packs</h1>
      <div class="page-subtitle">Gestion des offres groupées et packs cadeaux.</div>
    </div>
    <button onclick="openModal('bundleModal')"
      style="padding:12px 24px; background:var(--ink); color:#fff; border-radius:12px; font-weight:700; border:none; cursor:pointer; display:flex; align-items:center; gap:8px; transition:0.3s;"
      onmouseover="this.style.background='var(--primary)'" onmouseout="this.style.background='var(--ink)'">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Nouveau Bundle
    </button>
  </div>

  <div class="card">
    <div class="card-head">
      <h3>Tous les bundles</h3>
      <div class="search-box">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="searchBundles" placeholder="Nom du pack..." oninput="filterTable('bundlesTable','searchBundles')">
      </div>
    </div>
    <div class="table-container">
      <table id="bundlesTable">
        <thead>
          <tr><th>Visuel</th><th>Nom du Pack</th><th>Prix</th><th>Ancien Prix</th><th>Description</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php
          $allBundles = $db->query("SELECT * FROM bundles ORDER BY id DESC")->fetchAll();
          if (empty($allBundles)):
          ?>
          <tr><td colspan="6" style="text-align:center; padding:80px; color:var(--muted); font-weight:600;">
            <div style="font-size:48px; margin-bottom:16px; opacity:0.2;"></div>
            <div style="font-size:18px; font-weight:800; color:var(--ink); margin-bottom:8px;">Aucun bundle</div>
            Créez votre premier pack en cliquant sur « Nouveau Bundle ».
          </td></tr>
          <?php else: ?>
          <?php foreach($allBundles as $bun): ?>
          <tr>
            <td>
              <img src="<?= asset($bun['image_url']) ?>" class="prod-preview"
                   referrerpolicy="no-referrer"
                   onerror="this.src='../assets/img/placeholder.jpg'">
            </td>
            <td>
              <div style="font-weight:800; color:var(--ink); font-size:15px;"><?= e($bun['name']) ?></div>
              <div style="font-size:11px; color:var(--muted); font-weight:500; margin-top:2px;">Slug : <?= e($bun['slug']) ?></div>
            </td>
            <td style="font-weight:900; color:#000; font-size:16px;">
              <?= number_format($bun['price'], 2) ?> <span style="font-size:11px;">MAD</span>
            </td>
            <td>
              <?php if($bun['old_price']): ?>
                <span style="text-decoration:line-through; color:var(--muted); font-size:13px; font-weight:600;">
                  <?= number_format($bun['old_price'], 2) ?> MAD
                </span>
              <?php else: ?>
                <span style="color:var(--muted); font-size:11px;">—</span>
              <?php endif; ?>
            </td>
            <td style="max-width:220px;">
              <div style="font-size:13px; color:var(--ink-soft); overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
                <?= e($bun['description'] ?: '—') ?>
              </div>
            </td>
            <td>
              <div style="display:flex; gap:10px;">
                <button onclick='editBundle(<?= htmlspecialchars(json_encode($bun), ENT_QUOTES, "UTF-8") ?>)'
                  style="background:none; border:none; color:var(--primary); cursor:pointer; padding:5px;" title="Modifier">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </button>
                <form method="POST" style="display:inline" onsubmit="return confirm('Supprimer ce bundle ?')">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                  <input type="hidden" name="action" value="delete_bundle">
                  <input type="hidden" name="bundle_id" value="<?= $bun['id'] ?>">
                  <button type="submit" style="background:none; border:none; color:var(--red); cursor:pointer; padding:5px;" title="Supprimer">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
