<!-- Commandes -->
<div class="section-tab" id="tab-orders">
  <div class="page-header">
    <div>
      <h1 class="page-title">Archives Commandes</h1>
      <div class="page-subtitle">Gestion des ventes et suivis de livraison.</div>
    </div>
    <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
      <!-- Export CSV -->
      <a href="export.php?type=orders&csrf_token=<?= csrf_token() ?>"
        style="padding:10px 20px;background:var(--green);color:#fff;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;display:flex;align-items:center;gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export CSV
      </a>
      <!-- Filtre statut -->
      <select id="filterOrderStatus" onchange="filterOrders()" class="inline-select" style="font-size:13px;padding:8px 12px;">
        <option value="">Tous les statuts</option>
        <option value="pending">En attente</option>
        <option value="processing">En cours</option>
        <option value="shipped">Expédiée</option>
        <option value="delivered">Livrée</option>
        <option value="cancelled">Annulée</option>
      </select>
    </div>
  </div>

  <div class="card">
    <div class="card-head">
      <h3>Toutes les commandes</h3>
      <div class="search-box">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="searchOrders" placeholder="Nom, email, ville..." oninput="filterOrders()">
      </div>
    </div>
    <div class="table-container">
      <table id="ordersTable">
        <thead>
          <tr><th>Référence</th><th>Client / Contact</th><th>Total</th><th>Statut</th><th>N° Suivi</th><th>Lieu</th><th>Date</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php if (empty($orders)): ?>
          <tr><td colspan="8" style="text-align:center;padding:80px;color:var(--muted);font-weight:600;">
            <div style="font-size:48px;margin-bottom:16px;opacity:0.2;"></div>
            <div style="font-size:18px;font-weight:800;color:var(--ink);margin-bottom:8px;">Aucune commande</div>
            Les nouvelles commandes apparaîtront ici automatiquement.
          </td></tr>
          <?php else: ?>
          <?php foreach($orders as $o): ?>
          <?php
            $stmtItems = $db->prepare("SELECT oi.quantity, oi.price, COALESCE(p.title,'Article supprimé') AS item_name, COALESCE(p.image_url,'') AS item_img FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
            $stmtItems->execute([$o['id']]);
            $orderItems = $stmtItems->fetchAll();
          ?>
          <tr data-status="<?= $o['status'] ?>" data-search="<?= strtolower(e($o['customer_name'].' '.$o['customer_email'].' '.$o['city'])) ?>">
            <td style="font-weight:700;color:var(--muted);font-size:11px;">#<?= $o['id'] ?></td>
            <td>
              <div class="client-box">
                <div class="client-avatar"><?= mb_substr($o['customer_name'] ?? 'U', 0, 1) ?></div>
                <div>
                  <div style="font-weight:700;color:var(--ink);"><?= e($o['customer_name']) ?></div>
                  <div style="font-size:11px;color:var(--muted);"><?= e($o['customer_email']) ?></div>
                </div>
              </div>
            </td>
            <td><span style="font-weight:800;color:var(--ink);"><?= number_format($o['total'],2) ?> MAD</span></td>
            <td>
              <form method="POST" style="display:flex; flex-direction:column; gap:6px;">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="tracking_number" value="<?= e($o['tracking_number'] ?? '') ?>">
                
                <select name="status" class="inline-select" onchange="this.form.submit()">
                  <?php foreach(['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                  <option value="<?= $s ?>" <?= $o['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                  <?php endforeach; ?>
                </select>
                
                <div style="position:relative; display:inline-block; margin-top:4px;">
                  <select name="livreur_id" class="inline-select" style="border:1.5px solid #d4a373; border-radius:8px; font-size:11px; font-weight:700; padding:6px 20px 6px 10px; background:#fffbeb; color:#b45309; -webkit-appearance:none; appearance:none; cursor:pointer; width:100%; min-width:140px;" onchange="this.form.submit()">
                    <option value="" style="color:var(--muted);">Assigner coursier...</option>
                    <?php 
                    $livreurs = $db->query("SELECT id, name FROM users WHERE role = 'livreur'")->fetchAll();
                    foreach($livreurs as $l): 
                    ?>
                    <option value="<?= $l['id'] ?>" <?= ($o['livreur_id'] ?? 0) == $l['id'] ? 'selected' : '' ?>><?= e($l['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <div style="position:absolute; top:50%; right:8px; transform:translateY(-50%); pointer-events:none; color:#b45309; font-size:9px;">▼</div>
                </div>
              </form>
            </td>
            <td>
              <?php if($o['tracking_number']): ?>
                <span style="font-family:monospace;font-size:12px;font-weight:700;color:var(--ink);background:var(--bg-alt);padding:3px 8px;border-radius:6px;border:1px solid var(--border);"><?= e($o['tracking_number']) ?></span>
              <?php else: ?>
                <button onclick="openTrackingModal(<?= $o['id'] ?>, '<?= e($o['tracking_number'] ?? '') ?>')"
                  style="font-size:11px;color:var(--muted);background:none;border:1px dashed var(--border);border-radius:6px;padding:3px 8px;cursor:pointer;font-weight:600;" title="Ajouter un numéro de suivi">
                  + Ajouter
                </button>
              <?php endif; ?>
            </td>
            <td><span style="font-size:12px;font-weight:600;"><?= e($o['city']) ?></span></td>
            <td style="font-size:12px;color:var(--muted);"><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
            <td>
              <div style="display:flex;gap:8px;align-items:center;">
                <button onclick='openOrderDetail(<?= htmlspecialchars(json_encode([
                  "id"=>$o["id"],"customer_name"=>$o["customer_name"],"customer_email"=>$o["customer_email"],
                  "customer_phone"=>$o["customer_phone"]??"",
                  "customer_address"=>$o["customer_address"]??"",'city'=>$o["city"]??"",'total'=>$o["total"],
                  "status"=>$o["status"],"tracking_number"=>$o["tracking_number"]??"","notes"=>$o["notes"]??"",
                  "created_at"=>$o["created_at"],"items"=>$orderItems,"livreur_name"=>$o["livreur_name"]??"",
                  "livreur_id"=>$o["livreur_id"]??""
                ]), ENT_QUOTES,"UTF-8") ?>)' class="btn-detail-order">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                  Détails
                </button>
                <form method="POST" style="display:inline" onsubmit="return confirm('Supprimer cette commande ?')">
                  <input type="hidden" name="action" value="delete_order">
                  <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                  <button type="submit" style="background:none;border:none;color:var(--red);cursor:pointer;padding:5px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
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

<style>
.btn-detail-order{background:var(--bg-alt);border:1px solid var(--border);color:var(--primary);cursor:pointer;padding:7px 13px;border-radius:8px;display:flex;align-items:center;gap:5px;font-size:12px;font-weight:700;transition:0.2s;}
.btn-detail-order:hover{background:var(--primary);color:#fff;}
.od-section-label{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:12px;}
</style>

<!-- Modal Détails Commande -->
<div id="orderDetailModal" class="modal-overlay">
  <div class="modal-content" style="max-width:800px; width:95%; max-height:92vh; display:flex; flex-direction:column; overflow:hidden;">
    
    <!-- Header: title, date, and status side-by-side -->
    <div class="modal-head" style="padding: 24px 32px; border-bottom:1px solid var(--border); background:var(--white); flex-shrink:0;">
      <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
        <div>
          <h2 class="modal-title" id="odTitle" style="font-size:24px; font-weight:900; margin:0; line-height:1.2;">Commande</h2>
          <div id="odDate" style="font-size:12.5px; color:var(--muted); margin-top:4px; font-weight:500;"></div>
        </div>
        <div id="odStatus"></div>
      </div>
      <button onclick="closeModal('orderDetailModal')" style="background:none; border:none; cursor:pointer; color:var(--muted); display:flex; align-items:center; justify-content:center; padding:6px; border-radius:50%; transition:all 0.2s;" onmouseover="this.style.background='var(--bg)'; this.style.color='var(--red)';" onmouseout="this.style.background='none'; this.style.color='var(--muted)';">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    
    <!-- Body with custom cards -->
    <div class="modal-body" style="padding: 32px; overflow-y:auto; overflow-x:hidden; flex:1; display:flex; flex-direction:column; gap:28px;">
      
      <!-- Grid client + livraison in elegant modern cards -->
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:24px;">
        
        <!-- Card 1: Client details -->
        <div style="background:var(--bg); border:1px solid var(--border); border-radius:18px; padding:24px; display:flex; flex-direction:column; gap:16px; transition:transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';">
          <div style="display:flex; align-items:center; gap:8px; border-bottom:1px solid var(--border); padding-bottom:12px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.5" style="flex-shrink:0;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span class="od-section-label" style="margin:0; font-size:11px; font-weight:800; letter-spacing:0.06em; color:var(--primary);">Informations Client</span>
          </div>
          <div>
            <div id="odName" style="font-weight:900; font-size:16px; color:var(--ink); letter-spacing:-0.01em; margin-bottom:6px;"></div>
            <div style="display:flex; flex-direction:column; gap:6px;">
              <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--ink-soft);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0; color:var(--muted);"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <span id="odEmail"></span>
              </div>
              <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--ink-soft);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0; color:var(--muted);"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <span id="odPhone"></span>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Card 2: Livraison details -->
        <div style="background:var(--bg); border:1px solid var(--border); border-radius:18px; padding:24px; display:flex; flex-direction:column; gap:16px; transition:transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';">
          <div style="display:flex; align-items:center; gap:8px; border-bottom:1px solid var(--border); padding-bottom:12px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.5" style="flex-shrink:0;"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            <span class="od-section-label" style="margin:0; font-size:11px; font-weight:800; letter-spacing:0.06em; color:var(--primary);">Destinataire & Expédition</span>
          </div>
          <div>
            <div id="odCity" style="font-weight:900; font-size:16px; color:var(--ink); letter-spacing:-0.01em; margin-bottom:6px;"></div>
            <div id="odAddress" style="font-size:13px; color:var(--ink-soft); line-height:1.5; margin-bottom:16px;"></div>
            
            <!-- Mode de Paiement -->
            <div style="display:flex; flex-direction:column; gap:6px; margin-bottom:16px;">
              <label style="font-size:10px; font-weight:800; text-transform:uppercase; color:var(--ink-soft); letter-spacing:0.05em; display:flex; align-items:center; gap:4px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Règlement
              </label>
              <div id="odPaymentBadge"></div>
            </div>
            
            <form method="POST" style="display:flex; flex-direction:column; gap:14px; border-top:1px solid var(--border); padding-top:16px;">
              <input type="hidden" name="action" value="update_status">
              <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
              <input type="hidden" name="order_id" id="odOrderId">
              
              <!-- Statut -->
              <div style="display:flex; flex-direction:column; gap:6px;">
                <label style="font-size:10px; font-weight:800; text-transform:uppercase; color:var(--ink-soft); letter-spacing:0.05em;">Statut de la commande</label>
                <select name="status" id="odStatusSelect" class="inline-select" style="width:100%; font-size:13px; padding:10px 14px; border-radius:10px; font-weight:700;">
                  <option value="pending">En attente</option>
                  <option value="processing">En cours</option>
                  <option value="shipped">Expédiée</option>
                  <option value="delivered">Livrée</option>
                  <option value="cancelled">Annulée</option>
                </select>
              </div>

              <!-- Livreur -->
              <div style="display:flex; flex-direction:column; gap:6px;">
                <label style="font-size:10px; font-weight:800; text-transform:uppercase; color:var(--ink-soft); letter-spacing:0.05em;">Livreur Assigné</label>
                <select name="livreur_id" id="odLivreurSelect" class="inline-select" style="width:100%; font-size:13px; padding:10px 14px; border-radius:10px; font-weight:700;">
                  <option value="">Aucun livreur</option>
                  <?php 
                  $livreurs = $db->query("SELECT id, name FROM users WHERE role = 'livreur'")->fetchAll();
                  foreach($livreurs as $l): 
                  ?>
                  <option value="<?= $l['id'] ?>"><?= e($l['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Numéro de Suivi -->
              <div style="display:flex; flex-direction:column; gap:6px;">
                <label style="font-size:10px; font-weight:800; text-transform:uppercase; color:var(--ink-soft); letter-spacing:0.05em;">Numéro de suivi</label>
                <input type="text" name="tracking_number" id="odTrackingInput" placeholder="Aucun numéro de suivi" style="width:100%; padding:10px 14px; border:1.5px solid var(--border); border-radius:10px; font-size:13px; font-family:monospace; font-weight:700; color:var(--ink); background:var(--white);">
              </div>

              <!-- Bouton d'action de sauvegarde -->
              <button type="submit" class="btn btn-primary" style="margin-top:4px; padding:12px; font-size:13px; border-radius:10px; display:flex; align-items:center; justify-content:center; gap:8px; width:100%;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Enregistrer les modifications
              </button>
            </form>
          </div>
        </div>
        
      </div>
      
      <!-- List of ordered items -->
      <div style="display:flex; flex-direction:column; gap:12px;">
        <span class="od-section-label" style="font-size:11px; font-weight:800; letter-spacing:0.06em; color:var(--muted); display:flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
          Articles commandés
        </span>
        <div style="background:var(--white); border:1px solid var(--border); border-radius:18px; padding:20px; overflow:hidden;" id="odItemsContainer">
          <div id="odItems" style="overflow-x:auto;"></div>
        </div>
      </div>
      
      <!-- Total bottom centered -->
      <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; border-top:1px solid var(--border); padding-top:24px; text-align:center; width:100%; gap:8px;">
        <span class="od-section-label" style="font-size:11px; font-weight:800; letter-spacing:0.08em; color:var(--muted); display:block; margin:0;">Total Commande</span>
        <div id="odTotal" style="font-family:'Playfair Display',serif; font-size:42px; font-weight:950; color:var(--ink); line-height:1;"></div>
      </div>
      
    </div>
    
    <!-- Footer with simple Close action -->
    <div class="modal-footer" style="padding: 16px 32px; border-top:1px solid var(--border); background:var(--bg); flex-shrink:0; margin:0;">
      <button class="btn btn-ghost" onclick="closeModal('orderDetailModal')" style="padding:10px 24px; border-radius:10px; font-size:13px; font-weight:700;">Fermer</button>
    </div>
    
  </div>
</div>

<!-- Modal Numéro de Suivi -->
<div id="trackingModal" class="modal-overlay">
  <div class="modal-content" style="max-width:440px;">
    <div class="modal-head">
      <h2 class="modal-title">Numéro de suivi</h2>
      <button onclick="closeModal('trackingModal')" style="background:none;border:none;cursor:pointer;color:var(--muted);">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="update_status">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <input type="hidden" name="order_id" id="trackingOrderId">
      <input type="hidden" name="status" id="trackingStatus">
      <div class="modal-body">
        <div class="form-group">
          <label>Numéro de suivi transporteur</label>
          <input type="text" name="tracking_number" id="trackingNumber" placeholder="Ex: MA123456789FR" style="font-family:monospace;letter-spacing:.05em;">
        </div>
        <p style="font-size:12px;color:var(--muted);margin-top:-8px;">Ce numéro sera visible dans les détails de la commande.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeModal('trackingModal')">Annuler</button>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
      </div>
    </form>
  </div>
</div>

<script>
const ORDER_STATUS_MAP={pending:{label:'En attente',bg:'#fee2e2',color:'#b91c1c'},processing:{label:'En cours',bg:'#e0e7ff',color:'#3730a3'},shipped:{label:'Expédiée',bg:'#fef3c7',color:'#92400e'},delivered:{label:'Livrée',bg:'#d1fae5',color:'#065f46'},cancelled:{label:'Annulée',bg:'#f3f4f6',color:'#4b5563'}};

function filterOrders(){
  const q=(document.getElementById('searchOrders').value||'').toLowerCase();
  const st=document.getElementById('filterOrderStatus').value;
  document.querySelectorAll('#ordersTable tbody tr[data-status]').forEach(row=>{
    const matchSearch=!q||row.dataset.search.includes(q);
    const matchStatus=!st||row.dataset.status===st;
    row.style.display=(matchSearch&&matchStatus)?'':'none';
  });
}

function e(str) {
  if (!str) return '';
  return str.toString()
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function openOrderDetail(o){
  document.getElementById('odTitle').textContent='Commande #'+o.id;
  document.getElementById('odDate').textContent='Passée le '+new Date(o.created_at.replace(' ','T')).toLocaleString('fr-FR');
  document.getElementById('odName').textContent=o.customer_name||'—';
  document.getElementById('odEmail').textContent=o.customer_email||'—';
  document.getElementById('odPhone').textContent=o.customer_phone||'Non renseigné';
  document.getElementById('odCity').textContent=o.city||'—';
  document.getElementById('odAddress').textContent=o.customer_address||'Adresse non renseignée';

  // Parser les informations de paiement techniques stockées dans les notes
  let notesClean = o.notes || '';
  let paymentInfo = { type: 'cod', label: 'Paiement à la livraison (COD)' };

  if (notesClean.includes('Paiement: Carte') || notesClean.includes('Payé via Stripe')) {
    paymentInfo.type = 'stripe';
    paymentInfo.label = 'Payé par Carte (Stripe)';
    const intentMatch = notesClean.match(/\[intent:\s*([^\]]+)\]/i);
    if (intentMatch) {
      paymentInfo.intent = intentMatch[1];
    }
    // Nettoyer la note des mentions Stripe techniques
    notesClean = notesClean.replace(/Paiement:\s*Carte\s*\[Payé\s*via\s*Stripe\]/gi, '');
    notesClean = notesClean.replace(/\[intent:\s*[^\]]+\]/gi, '');
    notesClean = notesClean.replace(/Paiement:\s*Carte/gi, '');
  }
  notesClean = notesClean.replace(/^[|\s]+|[|\s]+$/g, '').trim();
  document.getElementById('odTotal').textContent=parseFloat(o.total).toFixed(2)+' MAD';
  
  // Affichage du badge de règlement haut de gamme
  const payBadge = document.getElementById('odPaymentBadge');
  if (paymentInfo.type === 'stripe') {
    payBadge.innerHTML = `
      <div style="display:flex; flex-direction:column; gap:6px;">
        <span style="display:inline-flex; align-items:center; gap:6px; background:#e0f2fe; color:#0369a1; font-size:12px; font-weight:800; padding:6px 12px; border-radius:30px; border:1px solid rgba(3,105,161,0.15); width:fit-content;">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
          Payé par Carte (Stripe)
        </span>
        ${paymentInfo.intent ? `<span style="font-family:monospace; font-size:11px; color:var(--muted); font-weight:700; padding-left:4px;">Ref: ${paymentInfo.intent}</span>` : ''}
      </div>
    `;
  } else {
    payBadge.innerHTML = `
      <span style="display:inline-flex; align-items:center; gap:6px; background:#fffbeb; color:#b45309; font-size:12px; font-weight:800; padding:6px 12px; border-radius:30px; border:1px solid #fde68a; width:fit-content;">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg>
        Paiement à la livraison (COD)
      </span>
    `;
  }

  const st=ORDER_STATUS_MAP[o.status]||{label:o.status,bg:'#eee',color:'#333'};
  document.getElementById('odStatus').innerHTML=`<span style="display:inline-flex;align-items:center;padding:5px 14px;border-radius:20px;font-size:11px;font-weight:800;text-transform:uppercase;background:${st.bg};color:${st.color};">${st.label}</span>`;
  
  // Remplir le formulaire logistique interactif dans la modal
  document.getElementById('odOrderId').value=o.id;
  document.getElementById('odStatusSelect').value=o.status;
  document.getElementById('odLivreurSelect').value=o.livreur_id||'';
  document.getElementById('odTrackingInput').value=o.tracking_number||'';
  let itemsHtml='';
  if(!o.items||o.items.length===0){itemsHtml='<div style="color:var(--muted);font-size:13px;padding:12px 0;">Aucun article trouvé.</div>';}
  else{
    itemsHtml='<table style="width:100%;border-collapse:collapse;font-size:13.5px;min-width:560px;"><thead><tr style="border-bottom:2px solid var(--border);">'
      +'<th style="text-align:left;font-size:11px;font-weight:800;text-transform:uppercase;color:var(--muted);padding-bottom:12px;">Article</th>'
      +'<th style="text-align:center;font-size:11px;font-weight:800;text-transform:uppercase;color:var(--muted);padding-bottom:12px;width:70px;">Qté</th>'
      +'<th style="text-align:right;font-size:11px;font-weight:800;text-transform:uppercase;color:var(--muted);padding-bottom:12px;width:120px;">Prix unit.</th>'
      +'<th style="text-align:right;font-size:11px;font-weight:800;text-transform:uppercase;color:var(--muted);padding-bottom:12px;width:120px;">Sous-total</th>'
      +'</tr></thead><tbody>';
    o.items.forEach(item=>{
      const sub=(parseFloat(item.price)*parseInt(item.quantity)).toFixed(2);
      itemsHtml+=`<tr style="border-bottom:1px solid var(--border); transition:background 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.01)'" onmouseout="this.style.background='transparent'">
        <td style="padding:14px 0;font-weight:700;color:var(--ink);">${item.item_name}</td>
        <td style="padding:14px 0;text-align:center;"><span style="background:var(--bg-alt);border:1px solid var(--border);border-radius:8px;padding:3px 10px;font-weight:800;color:var(--ink);">${item.quantity}</span></td>
        <td style="padding:14px 0;text-align:right;color:var(--ink-soft);font-weight:600;">${parseFloat(item.price).toFixed(2)} MAD</td>
        <td style="padding:14px 0;text-align:right;font-weight:800;color:var(--primary);">${sub} MAD</td>
      </tr>`;
    });
    itemsHtml+='</tbody></table>';
  }
  document.getElementById('odItems').innerHTML=itemsHtml;
  document.getElementById('orderDetailModal').style.display='flex';
}

function openTrackingModal(orderId, currentTracking){
  document.getElementById('trackingOrderId').value=orderId;
  document.getElementById('trackingNumber').value=currentTracking||'';
  
  const row=document.querySelector(`tr[data-status]`);
  document.getElementById('trackingStatus').value=row?row.dataset.status:'pending';
  document.getElementById('trackingModal').style.display='flex';
}
</script>
