
<div class="section-tab" id="tab-categories">
  <div class="page-header">
    <div>
      <h1 class="page-title">Rayons & Genres</h1>
      <div class="page-subtitle">Organisation thématique du catalogue.</div>
    </div>
    <button onclick="openModal('categoryModal')" style="padding:12px 24px; background:var(--ink); color:#fff; border-radius:12px; font-weight:700; border:none; cursor:pointer; display:flex; align-items:center; gap:8px; transition:0.3s;" onmouseover="this.style.background='var(--primary)'">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Nouvelle Catégorie
    </button>
  </div>
  <div class="card">
    <div class="table-container">
      <table>
        <thead>
          <tr><th>Icône</th><th>Nom du Rayon</th><th>Slug</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php 
          $cats = $db->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
          foreach($cats as $c): 
          ?>
          <tr>
            <td>
              <div style="width:40px; height:40px; background:<?= e($c['color']) ?>; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:20px;">
                <?= e($c['icon']) ?>
              </div>
            </td>
            <td style="font-weight:800; color:var(--ink);"><?= e($c['name']) ?></td>
            <td style="color:var(--muted); font-family:monospace;"><?= e($c['slug']) ?></td>
            <td>
              <div style="display:flex; gap:10px;">
                <button onclick='editCategory(<?= htmlspecialchars(json_encode($c), ENT_QUOTES, "UTF-8") ?>)' style="background:none; border:none; color:var(--primary); cursor:pointer; padding:5px;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </button>
                <form method="POST" style="display:inline" onsubmit="return confirm('Attention: Supprimer une catégorie peut affecter les produits liés. Continuer ?')">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                  <input type="hidden" name="action" value="delete_category">
                  <input type="hidden" name="category_id" value="<?= $c['id'] ?>">
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
