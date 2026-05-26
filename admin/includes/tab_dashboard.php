
<div class="section-tab active" id="tab-dashboard">
  <div class="page-header">
    <div>
      <div class="page-title">Tableau de bord</div>
      <div class="page-subtitle">Statistiques en temps réel et activités récentes.</div>
    </div>
    <div style="font-size:12px;color:var(--muted);font-weight:700;background:#fff;padding:10px 18px;border-radius:var(--radius-md);border:1px solid var(--border);box-shadow:var(--shadow-sm);">
      Aujourd'hui, <?= date('d F Y') ?>
    </div>
  </div>

  
  <?php if (!empty($low_stock_products)): ?>
  <div class="stock-alert" style="background:#fff1f0;border-left:4px solid var(--red);border-radius:12px;padding:20px 24px;margin-bottom:32px;display:flex;align-items:flex-start;gap:16px;">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#af3e3e" stroke-width="2" style="flex-shrink:0;margin-top:2px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div style="flex:1;">
      <div style="font-weight:800;color:#af3e3e;font-size:14px;margin-bottom:6px;">
         <?= count($low_stock_products) ?> produit<?= count($low_stock_products)>1?'s':'' ?> en stock critique (≤ 5 unités)
      </div>
      <div style="display:flex;flex-wrap:wrap;gap:8px;">
        <?php foreach($low_stock_products as $lsp): ?>
        <span style="background:#fff;border:1px solid #fca5a5;border-radius:8px;padding:4px 12px;font-size:12px;font-weight:700;color:#991b1b;">
          <?= e($lsp['title']) ?> — <strong style="color:#af3e3e;"><?= $lsp['stock'] ?></strong> restant<?= $lsp['stock']>1?'s':'' ?>
        </span>
        <?php endforeach; ?>
      </div>
    </div>
    <button onclick="showTab('products')" style="background:var(--red);color:#fff;border:none;border-radius:8px;padding:8px 16px;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap;">
      Gérer le stock →
    </button>
  </div>
  <?php endif; ?>

  
  <div class="stats-grid">
    <div class="stat-card">
      <div>
        <div class="stat-label">Commandes</div>
        <div class="stat-value"><?= number_format($stats['orders']) ?></div>
        <div class="stat-sub">+<?= $stats['orders_today'] ?> aujourd'hui</div>
      </div>
      <div class="stat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg></div>
    </div>
    <div class="stat-card">
      <div>
        <div class="stat-label">Chiffre d'Affaire</div>
        <div class="stat-value"><?= number_format($stats['revenue'], 0) ?> <span style="font-size:14px;color:var(--muted);font-weight:600;">MAD</span></div>
        <div class="stat-sub">+<?= number_format($stats['revenue_today'], 0) ?> MAD aujourd'hui</div>
      </div>
      <div class="stat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg></div>
    </div>
    <div class="stat-card">
      <div>
        <div class="stat-label">Catalogue</div>
        <div class="stat-value"><?= $stats['products'] ?></div>
        <?php if($stats['low_stock'] > 0): ?>
        <div class="stat-sub warn"><?= $stats['low_stock'] ?> en stock critique</div>
        <?php else: ?>
        <div class="stat-sub">Stock OK </div>
        <?php endif; ?>
      </div>
      <div class="stat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></div>
    </div>
    <div class="stat-card">
      <div>
        <div class="stat-label">Clients inscrits</div>
        <div class="stat-value"><?= $stats['customers'] ?></div>
        <div class="stat-sub"><?= $stats['bundles'] ?> bundle<?= $stats['bundles']>1?'s':'' ?> en vente</div>
      </div>
      <div class="stat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
    </div>
  </div>

  
  <div class="card">
    <div class="card-head">
      <h3>Dernières Transactions</h3>
      <button onclick="showTab('orders')" style="font-size:12px;font-weight:700;color:var(--primary);background:none;border:none;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.transform='translateX(5px)'" onmouseout="this.style.transform=''">Explorer toutes les commandes &rarr;</button>
    </div>
    <div class="table-container">
      <table>
        <thead>
          <tr><th>Client</th><th>Ville</th><th>Montant</th><th>État</th><th>Enregistré le</th></tr>
        </thead>
        <tbody>
          <?php if (empty($orders)): ?>
          <tr><td colspan="5" style="text-align:center;padding:60px;color:var(--muted);font-weight:600;">
            <div style="font-size:36px;margin-bottom:12px;opacity:0.3;"></div>
            Aucune commande pour l'instant.
          </td></tr>
          <?php else: ?>
          <?php foreach(array_slice($orders, 0, 8) as $o): ?>
          <tr>
            <td>
              <div class="client-box">
                <div class="client-avatar"><?= mb_substr($o['customer_name'] ?? 'U', 0, 1) ?></div>
                <div style="font-weight:700;color:var(--ink);"><?= e($o['customer_name']) ?></div>
              </div>
            </td>
            <td><span style="font-weight:500;font-size:12px;"><?= e($o['city']) ?></span></td>
            <td><span style="font-weight:900;color:var(--red);font-size:15px;"><?= number_format($o['total'], 2) ?> <span style="font-size:11px;">MAD</span></span></td>
            <td><span class="badge b-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
            <td style="font-size:12px;font-weight:500;color:var(--muted);"><?= date('d M, Y', strtotime($o['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  
  <?php
  $monthlyData = [];
  for ($i = 5; $i >= 0; $i--) {
      $month = date('Y-m', strtotime("-$i months"));
      $label = date('M Y', strtotime("-$i months"));
      $stmt  = $db->prepare("SELECT COALESCE(SUM(total),0) FROM orders WHERE DATE_FORMAT(created_at,'%Y-%m')=? AND status != 'cancelled'");
      $stmt->execute([$month]);
      $monthlyData[] = ['label' => $label, 'total' => (float)$stmt->fetchColumn()];
  }
  $topProducts = $db->query("SELECT p.title, SUM(oi.quantity) as sold FROM order_items oi JOIN products p ON oi.product_id = p.id GROUP BY oi.product_id ORDER BY sold DESC LIMIT 5")->fetchAll();
  ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-top:24px;">
    <div class="card">
      <div class="card-head"><h3>Ventes des 6 derniers mois (MAD)</h3></div>
      <div style="padding:20px;height:280px;"><canvas id="chartMonthly"></canvas></div>
    </div>
    <div class="card">
      <div class="card-head"><h3>Top 5 produits vendus</h3></div>
      <div style="padding:20px;height:280px;"><canvas id="chartTop"></canvas></div>
    </div>
  </div>
  <script>
  (function(){
    const monthLabels = <?= json_encode(array_column($monthlyData,'label')) ?>;
    const monthTotals = <?= json_encode(array_column($monthlyData,'total')) ?>;
    const topLabels   = <?= json_encode(array_column($topProducts,'title')) ?>;
    const topSold     = <?= json_encode(array_column($topProducts,'sold')) ?>;
    const cc = '#a24f2b', cb = 'rgba(162,79,43,0.08)';
    new Chart(document.getElementById('chartMonthly'),{type:'line',data:{labels:monthLabels,datasets:[{label:'CA (MAD)',data:monthTotals,borderColor:cc,backgroundColor:cb,borderWidth:2.5,tension:0.4,fill:true,pointBackgroundColor:cc,pointRadius:4}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,0.04)'},ticks:{color:'#7d7067',font:{size:11}}},x:{grid:{display:false},ticks:{color:'#7d7067',font:{size:11}}}}}});
    new Chart(document.getElementById('chartTop'),{type:'bar',data:{labels:topLabels.map(l=>l.length>18?l.substring(0,18)+'…':l),datasets:[{label:'Unités vendues',data:topSold,backgroundColor:['#a24f2b','#c47a50','#d99a74','#e8b99a','#f3d4bf'],borderRadius:6,borderSkipped:false}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,0.04)'},ticks:{color:'#7d7067',font:{size:11}}},x:{grid:{display:false},ticks:{color:'#7d7067',font:{size:10}}}}}});
  })();
  </script>
</div>
