
<div class="section-tab" id="tab-users">
  <div class="page-header">
    <div>
      <h1 class="page-title">Communauté MangaShop</h1>
      <div class="page-subtitle">Gestion des comptes clients et administrateurs.</div>
    </div>
    <div style="display:flex;gap:10px;">
      <a href="export.php?type=users&csrf_token=<?= csrf_token() ?>"
        style="padding:10px 18px;background:var(--green);color:#fff;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;display:flex;align-items:center;gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export CSV
      </a>
      <button onclick="openModal('userModal')"
      style="padding:12px 24px; background:var(--ink); color:#fff; border-radius:12px; font-weight:700; border:none; cursor:pointer; display:flex; align-items:center; gap:8px; transition:0.3s;"
      onmouseover="this.style.background='var(--primary)'" onmouseout="this.style.background='var(--ink)'">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Nouvel Utilisateur
    </button>
    </div>
  </div>
  <div class="card">
    <div class="card-head">
      <h3>Tous les utilisateurs</h3>
      <div class="search-box">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="searchUsers" placeholder="Nom, email, ville..." oninput="filterTable('usersTable','searchUsers')">
      </div>
    </div>
    <div class="table-container">
      <table id="usersTable">
        <thead>
          <tr><th>Profil</th><th>Rôle</th><th>Contact</th><th>Localisation</th><th>Inscrit le</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php
          $allUsers = $db->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
          foreach($allUsers as $u):
          ?>
          <tr>
            <td>
              <div class="client-box">
                <div class="client-avatar" style="background:<?= $u['role']==='admin'?'var(--primary)':'var(--bg-alt)' ?>; color:<?= $u['role']==='admin'?'#fff':'var(--ink)' ?>;">
                  <?= mb_substr($u['name'] ?? 'U', 0, 1) ?>
                </div>
                <div>
                  <div style="font-weight:800; color:var(--ink);"><?= e($u['name']) ?></div>
                  <div style="font-size:11px; color:var(--muted);"><?= e($u['email']) ?></div>
                </div>
              </div>
            </td>
            <td>
              <?php
              $bg = '#f0fdf4'; $color = '#166534';
              if ($u['role'] === 'admin') {
                  $bg = '#fef2f2'; $color = '#991b1b';
              } elseif ($u['role'] === 'livreur') {
                  $bg = '#fffbeb'; $color = '#b45309';
              }
              ?>
              <span class="badge" style="background:<?= $bg ?>; color:<?= $color ?>; border:1px solid currentColor; opacity:0.8;">
                <?= ucfirst(e($u['role'])) ?>
              </span>
            </td>
            <td>
              <div style="font-size:13px; font-weight:600; color:var(--ink-soft);"><?= e($u['phone'] ?: 'N/A') ?></div>
            </td>
            <td>
              <div style="font-size:12px; font-weight:500;"><?= e($u['city'] ?: 'N/A') ?></div>
            </td>
            <td style="font-size:12px; color:var(--muted); font-weight:500;">
              <?= date('d/m/Y', strtotime($u['created_at'])) ?>
            </td>
            <td>
              <div style="display:flex; gap:10px; align-items:center;">
                
                <button onclick='editUser(<?= htmlspecialchars(json_encode([
                  "id"      => $u["id"],
                  "name"    => $u["name"],
                  "email"   => $u["email"],
                  "role"    => $u["role"],
                  "phone"   => $u["phone"] ?? "",
                  "address" => $u["address"] ?? "",
                  "city"    => $u["city"] ?? "",
                ]), ENT_QUOTES, "UTF-8") ?>)'
                  title="Modifier"
                  style="background:none; border:none; color:var(--primary); cursor:pointer; padding:5px;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </button>
                
                <?php if($u['id'] != ($_SESSION['admin_user']['id'] ?? 0)): ?>
                <form method="POST" style="display:inline" onsubmit="return confirm('Voulez-vous vraiment supprimer ce compte ?')">
                  <input type="hidden" name="action" value="delete_user">
                  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                  <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                  <button type="submit" title="Supprimer" style="background:none; border:none; color:var(--red); cursor:pointer; padding:5px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                  </button>
                </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
