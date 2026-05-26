
<div class="section-tab" id="tab-devis">
  <div class="page-header">
    <div>
      <h1 class="page-title">Projets d'Impression</h1>
      <div class="page-subtitle">Dossiers de devis personnalisés reçus.</div>
    </div>
  </div>
  <?php if($devis): ?>
  <div class="card">
    <div class="table-container">
      <table>
        <thead>
          <tr><th>Porteur de Projet</th><th>Email</th><th>Configuration</th><th>Pagination</th><th>Envoyé le</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach($devis as $d): ?>
          <tr>
            <td style="font-weight:800; color:var(--ink);"><?= e($d['name']) ?></td>
            <td><a href="mailto:<?= e($d['email']) ?>" style="color:var(--primary); text-decoration:underline; font-weight:600;"><?= e($d['email']) ?></a></td>
            <td style="font-size:13px; color:var(--ink-soft); font-weight:500;"><?= e($d['format_type']) ?> <span style="color:var(--muted);">&bull;</span> <?= e($d['cover_type']) ?></td>
            <td style="font-weight:900; color:var(--ink);"><?= (int)$d['pages'] ?> <span style="font-size:11px; color:var(--muted); font-weight:400;">PAGES</span></td>
            <td style="color:var(--muted); font-weight:500;"><?= date('d F, Y', strtotime($d['created_at'])) ?></td>
            <td>
              <form method="POST" style="display:inline" onsubmit="return confirm('Supprimer ce devis ?')">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="action" value="delete_devis">
                <input type="hidden" name="id" value="<?= $d['id'] ?>">
                <button type="submit" style="background:none; border:none; color:var(--red); cursor:pointer; padding:5px;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                </button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php else: ?>
  <div class="card" style="padding:100px; text-align:center; color:var(--muted); font-weight:600;">Aucun dossier en attente.</div>
  <?php endif; ?>
</div>
