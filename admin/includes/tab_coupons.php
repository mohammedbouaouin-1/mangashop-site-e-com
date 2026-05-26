<?php
$coupons = $db->query("SELECT * FROM promo_codes ORDER BY id DESC")->fetchAll();
?>

<div class="section-tab" id="tab-coupons">
  <div class="page-header">
    <div>
      <div class="page-title">Codes Promo</div>
      <div class="page-subtitle">Créez et gérez vos codes de réduction.</div>
    </div>
    <button onclick="document.getElementById('couponModal').style.display='flex'" class="btn-action">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Nouveau code
    </button>
  </div>

  <div class="card">
    <div class="table-container">
      <table>
        <thead>
          <tr><th>Code</th><th>Réduction</th><th>Utilisations</th><th>Expire le</th><th>Statut</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php if (empty($coupons)): ?>
          <tr><td colspan="6" style="text-align:center; padding:60px; color:var(--muted); font-weight:600;">
            <div style="font-size:32px; margin-bottom:12px; opacity:0.3;"></div>
            Aucun code promo. Créez-en un !
          </td></tr>
          <?php else: ?>
          <?php foreach ($coupons as $c): ?>
          <tr>
            <td><span style="font-weight:800; font-size:15px; letter-spacing:0.05em; color:var(--ink);"><?= e($c['code']) ?></span></td>
            <td><span style="font-weight:700; color:var(--primary); font-size:16px;">-<?= $c['discount_pct'] ?>%</span></td>
            <td>
              <span style="font-size:13px; color:var(--muted);"><?= $c['used'] ?> / <?= $c['max_uses'] ?></span>
              <div style="height:4px; background:var(--bg); border-radius:4px; margin-top:4px; width:80px;">
                <div style="height:100%; width:<?= min(100, round($c['used']/$c['max_uses']*100)) ?>%; background:var(--primary); border-radius:4px;"></div>
              </div>
            </td>
            <td style="font-size:13px; color:var(--muted);"><?= $c['expires_at'] ? date('d/m/Y', strtotime($c['expires_at'])) : '—' ?></td>
            <td>
              <?php if ($c['active']): ?>
                <span class="badge b-delivered">Actif</span>
              <?php else: ?>
                <span class="badge b-cancelled">Inactif</span>
              <?php endif; ?>
            </td>
            <td>
              <button onclick="editCoupon(<?= htmlspecialchars(json_encode($c)) ?>)" style="background:none;border:none;color:var(--primary);cursor:pointer;padding:5px;" title="Modifier">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </button>
              <form method="POST" style="display:inline" onsubmit="return confirm('Supprimer ce code ?')">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="action" value="delete_coupon">
                <input type="hidden" name="coupon_id" value="<?= $c['id'] ?>">
                <button type="submit" style="background:none;border:none;color:var(--red);cursor:pointer;padding:5px;">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                </button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>


<div id="couponModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:16px; padding:32px; width:100%; max-width:460px; box-shadow:0 20px 60px rgba(0,0,0,0.2);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
      <h3 style="font-size:18px; font-weight:800; color:var(--ink);" id="couponModalTitle">Nouveau code promo</h3>
      <button onclick="document.getElementById('couponModal').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:20px;color:var(--muted);">&times;</button>
    </div>
    <form method="POST" style="display:flex; flex-direction:column; gap:16px;">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <input type="hidden" name="action" value="save_coupon">
      <input type="hidden" name="coupon_id" id="couponId" value="0">
      <div>
        <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:6px;">Code</label>
        <input type="text" name="code" id="couponCode" required placeholder="Ex: MANGA20" style="width:100%;padding:12px 14px;border:1.5px solid var(--border);border-radius:10px;font-size:14px;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;box-sizing:border-box;">
      </div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        <div>
          <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:6px;">Réduction (%)</label>
          <input type="number" name="discount_pct" id="couponPct" min="1" max="100" value="10" required style="width:100%;padding:12px 14px;border:1.5px solid var(--border);border-radius:10px;font-size:14px;box-sizing:border-box;">
        </div>
        <div>
          <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:6px;">Utilisations max</label>
          <input type="number" name="max_uses" id="couponMaxUses" min="1" value="100" required style="width:100%;padding:12px 14px;border:1.5px solid var(--border);border-radius:10px;font-size:14px;box-sizing:border-box;">
        </div>
      </div>
      <div>
        <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:6px;">Date d'expiration (optionnel)</label>
        <input type="date" name="expires_at" id="couponExpires" style="width:100%;padding:12px 14px;border:1.5px solid var(--border);border-radius:10px;font-size:14px;box-sizing:border-box;">
      </div>
      <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:14px;font-weight:600;color:var(--ink);">
        <input type="checkbox" name="active" id="couponActive" checked style="width:16px;height:16px;"> Actif
      </label>
      <button type="submit" class="btn-action" style="padding:14px;font-size:14px;justify-content:center;">Enregistrer</button>
    </form>
  </div>
</div>

<script>
function editCoupon(c) {
  document.getElementById('couponModalTitle').textContent = 'Modifier le code';
  document.getElementById('couponId').value      = c.id;
  document.getElementById('couponCode').value    = c.code;
  document.getElementById('couponPct').value     = c.discount_pct;
  document.getElementById('couponMaxUses').value = c.max_uses;
  document.getElementById('couponExpires').value = c.expires_at || '';
  document.getElementById('couponActive').checked = c.active == 1;
  document.getElementById('couponModal').style.display = 'flex';
}
</script>
