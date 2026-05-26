<!-- Newsletter -->
<div class="section-tab" id="tab-newsletter">
  <div class="page-header">
    <div>
      <h1 class="page-title">Communauté</h1>
      <div class="page-subtitle">Gestion des abonnés à la newsletter.</div>
    </div>
  </div>
  <div class="stats-grid" style="grid-template-columns:1fr;">
    <div class="stat-card" style="text-align:center; padding:80px;">
      <div class="stat-label">Rayonnement actuel</div>
      <div class="stat-value" style="font-size:72px; font-weight:950; color:var(--primary);"><?= $newsletters ?></div>
      <p style="color:var(--muted); margin-top:16px; font-weight:600; font-size:15px;">Abonnés actifs recevant vos bulletins d'information.</p>
    </div>
  </div>

  <?php $emails=$db->query("SELECT id,email,created_at FROM newsletter ORDER BY created_at DESC LIMIT 100")->fetchAll(); ?>
  <?php if($emails): ?>
  <div class="card" style="max-width:800px; margin: 0 auto;">
    <div class="table-container">
      <table>
        <thead>
          <tr><th>Adresse de Contact</th><th>Rejoint le</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach($emails as $em): ?>
          <tr>
            <td style="font-weight:700; color:var(--ink);"><?= e($em['email']) ?></td>
            <td style="color:var(--muted); font-weight:500;"><?= date('d F, Y', strtotime($em['created_at'])) ?></td>
            <td>
               <form method="POST" style="display:inline" onsubmit="return confirm('Retirer cet abonné ?')">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="action" value="delete_newsletter">
                <input type="hidden" name="id" value="<?= $em['id'] ?>">
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
  <?php endif; ?>
</div>
