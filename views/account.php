<?php require_once 'includes/header.php'; ?>

<style>

.account-v4 {
    font-family: 'Inter', sans-serif;
    color: var(--ink);
    animation: fadeIn 0.4s ease forwards;
}

@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }


.acct-header {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: var(--shadow-sm);
    margin-bottom: 32px;
}
.acct-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), #c8714a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    font-weight: 800;
    box-shadow: 0 8px 24px rgba(162, 79, 43, 0.3);
}
.acct-logout {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 100px;
    font-size: 14px;
    font-weight: 600;
    color: var(--ink-soft);
    background: var(--white);
    border: 1px solid var(--border);
    text-decoration: none;
    transition: all 0.2s;
    box-shadow: var(--shadow-sm);
}
.acct-logout:hover {
    color: var(--red);
    border-color: var(--border);
    background: var(--bg);
}


.acct-tabs-wrapper {
    display: flex;
    gap: 4px;
    background: var(--white);
    padding: 6px;
    border-radius: 100px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    width: max-content;
    margin-bottom: 32px;
}
.acct-tab {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 24px;
    border-radius: 100px;
    font-size: 14px;
    font-weight: 600;
    color: var(--ink-soft);
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.25, 1, 0.5, 1);
}
.acct-tab:hover {
    color: var(--ink);
}
.acct-tab.active {
    background: var(--bg);
    color: var(--primary);
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
}
.tab-badge {
    background: var(--white);
    color: var(--ink);
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 11px;
    box-shadow: var(--shadow-sm);
}
.acct-tab.active .tab-badge {
    background: var(--primary);
    color: #fff;
}


.acct-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
}


.order-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px;
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    background: var(--white);
    margin-bottom: 16px;
    transition: all 0.3s ease;
    cursor: pointer;
}
.order-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary);
}
.order-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: var(--bg);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--border);
}
.status-badge {
    padding: 6px 14px;
    border-radius: 100px;
    font-size: 12px;
    font-weight: 700;
    border: 1px solid var(--border);
    background: var(--bg);
    color: var(--ink);
}
.status-badge.pending {
    background: rgba(162, 79, 43, 0.1);
    color: var(--primary);
    border-color: rgba(162, 79, 43, 0.2);
}


.order-timeline {
    display: none;
    padding: 20px 24px;
    background: var(--bg);
    border-top: 1px solid var(--border);
    border-radius: 0 0 var(--radius-lg) var(--radius-lg);
    margin-top: -2px;
}
.order-timeline.open { display: block; }
.timeline-steps {
    display: flex;
    align-items: center;
    gap: 0;
    position: relative;
    overflow-x: auto;
    padding: 8px 0;
}
.tl-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    min-width: 80px;
    position: relative;
}
.tl-step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 16px;
    left: 50%;
    width: 100%;
    height: 2px;
    background: var(--border);
    z-index: 0;
}
.tl-step.done:not(:last-child)::after { background: var(--primary); }
.tl-dot {
    width: 32px; height: 32px;
    border-radius: 50%;
    border: 2px solid var(--border);
    background: var(--white);
    display: flex; align-items: center; justify-content: center;
    z-index: 1;
    font-size: 14px;
    color: var(--muted);
    transition: all 0.3s;
}
.tl-step.done .tl-dot {
    background: var(--primary);
    border-color: var(--primary);
    color: #fff;
}
.tl-step.current .tl-dot {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    color: #fff !important;
    animation: pulseGlow 2s infinite ease-in-out;
}
@keyframes pulseGlow {
    0% { box-shadow: 0 0 0 0px rgba(162,79,43,0.4); }
    70% { box-shadow: 0 0 0 8px rgba(162,79,43,0); }
    100% { box-shadow: 0 0 0 0px rgba(162,79,43,0); }
}
.tl-label {
    font-size: 11px; font-weight: 600;
    color: var(--muted); margin-top: 8px;
    text-align: center;
}
.tl-step.done .tl-label, .tl-step.current .tl-label { color: var(--primary); }


.acct-avatar {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), #c8714a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    font-weight: 900;
    box-shadow: 0 8px 24px rgba(162, 79, 43, 0.25);
    border: 3px solid var(--white);
    outline: 2px solid var(--border);
}


.profile-form-input {
    width: 100%; padding: 12px 16px;
    background: var(--white); border: 1.5px solid var(--border);
    border-radius: 10px; color: var(--ink); font-size: 14px;
    font-family: inherit; transition: all 0.2s ease;
    box-sizing: border-box;
}
.profile-form-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(162, 79, 43, 0.05); }


.input-icon-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}
.input-icon-wrapper .profile-form-input {
    padding-left: 46px;
}
.input-icon-wrapper svg {
    position: absolute;
    left: 16px;
    color: var(--muted);
    pointer-events: none;
    transition: color 0.2s, transform 0.2s;
}
.input-icon-wrapper .profile-form-input:focus + svg {
    color: var(--primary);
    transform: scale(1.08);
}


.acct-profile-layout {
    display: grid;
    grid-template-columns: 1.2fr 0.8fr;
    gap: 32px;
    align-items: start;
}

.acct-summary-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 32px;
    box-shadow: var(--shadow-sm);
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.acct-stat-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: var(--bg);
    border-radius: var(--radius-md);
    border: 1px solid var(--border);
    transition: transform 0.2s, border-color 0.2s;
}
.acct-stat-item:hover {
    transform: translateY(-2px);
    border-color: var(--primary);
}

.acct-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: var(--white);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 20px;
    box-shadow: var(--shadow-sm);
}

.acct-stat-info {
    display: flex;
    flex-direction: column;
}
.acct-stat-info .stat-label {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    color: var(--muted);
    letter-spacing: 0.5px;
}
.acct-stat-info .stat-val {
    font-size: 15px;
    font-weight: 800;
    color: var(--ink);
    margin-top: 2px;
}

@media (max-width: 900px) {
    .acct-profile-layout { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .acct-header { flex-direction: column; align-items: flex-start; gap: 24px; }
    .acct-tabs-wrapper { width: 100%; overflow-x: auto; }
    .order-card { flex-direction: column; align-items: flex-start; gap: 16px; }
    .order-meta { width: 100%; display: flex; justify-content: space-between; align-items: center; }
}

/* --- EXTRA PREMIUM DELIVERIES STYLES --- */
.livreur-dashboard {
    display: flex;
    flex-direction: column;
    gap: 32px;
    animation: fadeIn 0.4s ease forwards;
}

.livreur-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
}

.livreur-stat-card {
    border-radius: 20px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    border: 1px solid var(--border);
    background: var(--white);
}

.livreur-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
}

.livreur-stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    flex-shrink: 0;
    transition: transform 0.3s;
}

.livreur-stat-card:hover .livreur-stat-icon {
    transform: scale(1.1);
}

.livreur-stat-info {
    display: flex;
    flex-direction: column;
}

.livreur-stat-info .stat-label {
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 4px;
}

.livreur-stat-info .stat-val {
    font-size: 26px;
    font-weight: 900;
}

.livreur-order-grid {
    display: grid;
    grid-template-columns: 1.15fr 0.85fr;
    gap: 32px;
}

@media (max-width: 991px) {
    .livreur-order-grid {
        grid-template-columns: 1fr;
        gap: 24px;
    }
}

.livreur-order-card {
    border: 1px solid var(--border);
    border-radius: 24px;
    overflow: hidden;
    background: var(--white);
    box-shadow: 0 4px 20px rgba(0,0,0,0.02);
    padding: 32px;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    border-left: 5px solid var(--primary);
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.livreur-order-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 32px rgba(0,0,0,0.06);
}

.livreur-btn-action {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 14px;
    border-radius: 14px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 700;
    transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
    box-shadow: var(--shadow-sm);
    border: none;
    cursor: pointer;
}

.livreur-btn-action.call {
    background: #e0f2fe;
    color: #0369a1;
}
.livreur-btn-action.call:hover {
    background: #bae6fd;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(3,105,161,0.15);
}

.livreur-btn-action.whatsapp {
    background: #dcfce7;
    color: #15803d;
}
.livreur-btn-action.whatsapp:hover {
    background: #bbf7d0;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(21,128,61,0.15);
}

.livreur-gps-box {
    background: var(--bg);
    border: 1.5px dashed var(--border);
    border-radius: 18px;
    padding: 20px;
    transition: all 0.2s;
}

.livreur-gps-box:hover {
    border-color: var(--primary);
    background: var(--white);
}

.livreur-btn-gps {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    color: #fff;
    border-radius: 12px;
    padding: 10px 18px;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none;
    margin-top: 14px;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(15,23,42,0.15);
}

.livreur-btn-gps:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(15,23,42,0.25);
    background: linear-gradient(135deg, var(--primary) 0%, #c8714a 100%);
}

.livreur-segmented-control {
    display: flex;
    background: var(--bg);
    padding: 4px;
    border-radius: 16px;
    border: 1px solid var(--border);
    width: 100%;
    gap: 4px;
}

.livreur-segmented-btn {
    flex: 1;
    padding: 12px 8px;
    border-radius: 12px;
    font-size: 11.5px;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
    border: none;
    background: transparent;
    color: var(--ink-soft);
    outline: none;
}

.livreur-segmented-btn.active.processing {
    background: var(--white);
    color: var(--primary);
    box-shadow: var(--shadow-sm);
}
.livreur-segmented-btn.active.shipped {
    background: var(--white);
    color: #b45309;
    box-shadow: var(--shadow-sm);
}
.livreur-segmented-btn.active.delivered {
    background: var(--white);
    color: var(--green);
    box-shadow: var(--shadow-sm);
}

.livreur-segmented-btn:not(.active):hover {
    color: var(--ink);
    background: rgba(0,0,0,0.02);
}
.dark-mode .livreur-segmented-btn:not(.active):hover {
    background: rgba(255,255,255,0.02);
}

@keyframes pulseGlowSuccess {
    0% { box-shadow: 0 0 0 0px rgba(16,185,129,0.2); }
    70% { box-shadow: 0 0 0 8px rgba(16,185,129,0); }
    100% { box-shadow: 0 0 0 0px rgba(16,185,129,0); }
}
</style>

<div class="account-v4 section-container" style="max-width:1100px; margin: 40px auto; padding: 0 20px; min-height: 50vh;">
    
    
    <div class="acct-header">
        <div style="display:flex; align-items:center; gap:20px;">
            <div class="acct-avatar">
                <?= strtoupper(substr($user['name'], 0, 1)) ?>
            </div>
            <div>
                <h1 style="font-size:22px; font-weight:800; letter-spacing:-0.03em; margin-bottom:4px; color:var(--ink); text-transform: capitalize;"><?= ucwords(e($user['name'])) ?></h1>
                <p style="color:var(--muted); font-size:14px; font-weight:500;"><?= e($user['email']) ?></p>
            </div>
        </div>
        <div style="display:flex; align-items:center; gap:12px;">
            <?php if (($user['role'] ?? '') === 'admin'): ?>
                <a href="admin/index.php" class="acct-logout" style="border-color:var(--primary); color:var(--primary);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                    Dashboard Admin
                </a>
            <?php endif; ?>
            <a href="actions/logout.php" class="acct-logout">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Déconnexion
            </a>
        </div>
    </div>

    
    <div class="acct-tabs-wrapper">
        <?php if (($user['role'] ?? '') === 'livreur'): ?>
            <a href="?tab=deliveries" class="acct-tab <?= $tab==='deliveries'?'active':'' ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                Mes Livraisons
                <span class="tab-badge"><?= count($orders) ?></span>
            </a>
            <a href="?tab=profile" class="acct-tab <?= $tab==='profile'?'active':'' ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                Mon Profil
            </a>
        <?php else: ?>
            <a href="?tab=orders" class="acct-tab <?= $tab==='orders'?'active':'' ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                Commandes 
                <span class="tab-badge"><?= count($orders) ?></span>
            </a>
            <a href="?tab=wishlist" class="acct-tab <?= $tab==='wishlist'?'active':'' ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                Favoris
            </a>
            <a href="?tab=profile" class="acct-tab <?= $tab==='profile'?'active':'' ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                Mon Profil
            </a>
        <?php endif; ?>
    </div>

    
    <div class="account-content">
        <?php if ($tab === 'profile'): ?>
            <?php
            $profileSuccess = '';
            $profileError   = '';
            if (!empty($_SESSION['profile_success'])) { $profileSuccess = $_SESSION['profile_success']; unset($_SESSION['profile_success']); }
            if (!empty($_SESSION['profile_error']))   { $profileError   = $_SESSION['profile_error'];   unset($_SESSION['profile_error']); }
            ?>
            <div class="acct-profile-layout">
                
                <div class="acct-card" style="padding:32px;">
                    <h2 style="font-size:18px; font-weight:800; margin-bottom:24px; color:var(--ink);">Modifier mon profil</h2>
                    <?php if ($profileSuccess): ?><div style="background:#ecfdf5;color:#065f46;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:20px;border:1px solid #a7f3d0;"><?= e($profileSuccess) ?></div><?php endif; ?>
                    <?php if ($profileError): ?><div style="background:#fff2f2;color:#e63946;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:20px;border:1px solid #ffebeb;"><?= e($profileError) ?></div><?php endif; ?>
                    
                    <form method="POST" action="actions/profile.php" style="display:flex;flex-direction:column;gap:18px;">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:6px;">Nom complet</label>
                            <div class="input-icon-wrapper">
                                <input type="text" name="name" value="<?= e($user['name']) ?>" required class="profile-form-input">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:6px;">Email</label>
                            <div class="input-icon-wrapper">
                                <input type="email" name="email" value="<?= e($user['email']) ?>" required class="profile-form-input">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:6px;">Téléphone</label>
                            <div class="input-icon-wrapper">
                                <input type="text" name="phone" value="<?= e($user['phone'] ?? '') ?>" class="profile-form-input" placeholder="06 00 00 00 00">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:6px;">Adresse</label>
                            <div class="input-icon-wrapper">
                                <input type="text" name="address" value="<?= e($user['address'] ?? '') ?>" class="profile-form-input" placeholder="Rue, quartier...">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:6px;">Ville</label>
                            <div class="input-icon-wrapper">
                                <input type="text" name="city" value="<?= e($user['city'] ?? '') ?>" class="profile-form-input" placeholder="Casablanca">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="9" y1="22" x2="9" y2="16"></line><line x1="15" y1="22" x2="15" y2="16"></line><line x1="9" y1="16" x2="15" y2="16"></line><path d="M9 8h6"></path><path d="M9 12h6"></path></svg>
                            </div>
                        </div>
                        <hr style="border:none;border-top:1px solid var(--border);margin:4px 0;">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:6px;">Nouveau mot de passe</label>
                            <div class="input-icon-wrapper">
                                <input type="password" name="password" class="profile-form-input" placeholder="Laisser vide pour ne pas changer">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:6px;">Confirmer le mot de passe</label>
                            <div class="input-icon-wrapper">
                                <input type="password" name="password_confirm" class="profile-form-input" placeholder="Répéter le nouveau mot de passe">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary" style="padding:14px;border-radius:10px;font-size:14px;font-weight:700;margin-top:4px;">Enregistrer les modifications</button>
                    </form>
                </div>
                
                
                <div class="acct-summary-card">
                    <h3 style="font-size:16px; font-weight:800; color:var(--ink); border-bottom:1px solid var(--border); padding-bottom:12px; margin-bottom:4px;">Résumé de mon compte</h3>
                    
                    <div class="acct-stat-item">
                        <div class="acct-stat-icon" style="display:flex; align-items:center; justify-content:center;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6M18 9h1.5a2.5 2.5 0 0 0 0-5H18M4 22h16M10 14.66V17c0 .55-.45 1-1 1H4v2h16v-2h-5c-.55 0-1-.45-1-1v-2.34M12 2a5 5 0 0 0-5 5v3c0 2.2 1.8 4 4 4h2c2.2 0 4-1.8 4-4V7a5 5 0 0 0-5-5z"/></svg>
                        </div>
                        <div class="acct-stat-info">
                            <span class="stat-label">Statut Otaku</span>
                            <span class="stat-val" style="color:var(--primary);">Otaku Initié</span>
                        </div>
                    </div>
                    <div class="acct-stat-item">
                        <div class="acct-stat-icon" style="display:flex; align-items:center; justify-content:center;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--ink)" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                        </div>
                        <div class="acct-stat-info">
                            <span class="stat-label">Commandes</span>
                            <span class="stat-val"><?= count($orders) ?> Commande<?= count($orders)>1?'s':'' ?></span>
                        </div>
                    </div>
                    <div class="acct-stat-item">
                        <div class="acct-stat-icon" style="display:flex; align-items:center; justify-content:center;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--red)" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                        </div>
                        <div class="acct-stat-info">
                            <span class="stat-label">Favoris</span>
                            <span class="stat-val"><?= count($wishlist) ?> Manga<?= count($wishlist)>1?'s':'' ?></span>
                        </div>
                    </div>
                    <div class="acct-stat-item">
                        <div class="acct-stat-icon" style="display:flex; align-items:center; justify-content:center;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <div class="acct-stat-info">
                            <span class="stat-label">Membre depuis</span>
                            <span class="stat-val"><?= date('d/m/Y', strtotime($user['created_at'] ?? 'now')) ?></span>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($tab === 'deliveries'): ?>
            <div class="livreur-dashboard">
                <?php
                $profileSuccess = '';
                if (!empty($_SESSION['profile_success'])) { $profileSuccess = $_SESSION['profile_success']; unset($_SESSION['profile_success']); }
                
                $totalAssigned = count($orders);
                $totalFinalized = 0;
                $cashToSettle = 0;
                foreach ($orders as $deliv) {
                    if ($deliv['status'] === 'delivered') {
                        $totalFinalized++;
                        $isDelivCOD = !str_contains($deliv['notes'] ?? '', 'Paiement: Carte');
                        if ($isDelivCOD) {
                            $cashToSettle += $deliv['total'];
                        }
                    }
                }
                ?>
                <?php if ($profileSuccess): ?><div style="background:#ecfdf5;color:#065f46;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:20px;border:1px solid #a7f3d0;"><?= e($profileSuccess) ?></div><?php endif; ?>

                
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px; flex-wrap:wrap; gap:16px;">
                    <div>
                        <h2 style="font-size:20px; font-weight:900; letter-spacing:-0.03em; color:var(--ink); display:flex; align-items:center; gap:10px;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                            Console Logistique Livreur
                        </h2>
                        <p style="color:var(--ink-soft); font-size:13.5px; margin-top:4px;">Gérez vos expéditions, contactez les clients et mettez à jour les statuts en temps réel.</p>
                    </div>
                </div>

                <div class="livreur-stats-grid">
                    
                    <div class="livreur-stat-card">
                        <div class="livreur-stat-icon" style="background:var(--bg); color:var(--primary);">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        </div>
                        <div class="livreur-stat-info">
                            <span class="stat-label" style="color:var(--ink-soft);">Total Livraisons</span>
                            <span class="stat-val" style="color:var(--ink);"><?= $totalAssigned ?> colis</span>
                        </div>
                    </div>
                    
                    
                    <div class="livreur-stat-card">
                        <div class="livreur-stat-icon" style="background:#e6f4ea; color:var(--green);">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                        <div class="livreur-stat-info">
                            <span class="stat-label" style="color:var(--ink-soft);">Livrées / Finalisées</span>
                            <span class="stat-val" style="color:var(--green);"><?= $totalFinalized ?> <span style="font-size:16px; color:var(--muted); font-weight:500;">sur <?= $totalAssigned ?></span></span>
                        </div>
                    </div>
                    
                    
                    <div class="livreur-stat-card">
                        <div class="livreur-stat-icon" style="background:#fffbeb; color:#b45309;">
                            
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="6" width="20" height="12" rx="2" />
                                <circle cx="12" cy="12" r="2" />
                                <path d="M6 12h.01M18 12h.01" />
                            </svg>
                        </div>
                        <div class="livreur-stat-info">
                            <span class="stat-label" style="color:var(--ink-soft);">Espèces à reverser (COD)</span>
                            <span class="stat-val" style="color:#b45309;"><?= number_format($cashToSettle, 2) ?> MAD</span>
                        </div>
                    </div>
                </div>

                <?php if ($orders): ?>
                    <div style="display:flex; flex-direction:column; gap:28px;">
                        <?php foreach ($orders as $o): 
                            $cardBorderColor = 'var(--primary)';
                            if ($o['status'] === 'delivered') $cardBorderColor = 'var(--green)';
                            elseif ($o['status'] === 'shipped') $cardBorderColor = '#d97706';
                        ?>
                            <div class="livreur-order-card" style="border-left-color: <?= $cardBorderColor ?>;">
                                
                                
                                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:20px; margin-bottom:4px; flex-wrap:wrap; gap:16px;">
                                    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                                        <span style="background:var(--bg); color:var(--ink); font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; padding:6px 14px; border-radius:30px; border:1px solid var(--border);">
                                            Commande #<?= e($o['id']) ?>
                                        </span>
                                        
                                        
                                        <?php 
                                        $isCOD = !str_contains($o['notes'] ?? '', 'Paiement: Carte');
                                        if ($isCOD && $o['status'] !== 'delivered'): 
                                        ?>
                                            <span style="background:#fee2e2; color:#ef4444; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; padding:6px 14px; border-radius:30px; border:1px solid rgba(239,68,68,0.15); display:inline-flex; align-items:center; gap:6px;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg>
                                                Espèces à collecter (COD)
                                            </span>
                                        <?php elseif ($isCOD && $o['status'] === 'delivered'): ?>
                                            <span style="background:#d1fae5; color:#10b981; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; padding:6px 14px; border-radius:30px; border:1px solid rgba(16,185,129,0.15); display:inline-flex; align-items:center; gap:6px;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                                Espèces collectées
                                            </span>
                                        <?php else: ?>
                                            <span style="background:#e0f2fe; color:#0284c7; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; padding:6px 14px; border-radius:30px; border:1px solid rgba(2,132,199,0.15); display:inline-flex; align-items:center; gap:6px;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                                Payé en ligne
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="text-align:right;">
                                        <span style="font-size:10px; font-weight:800; text-transform:uppercase; color:var(--muted); letter-spacing:0.05em; display:block; margin-bottom:2px;">Montant Total</span>
                                        <span style="font-family:'Playfair Display',serif; font-size:28px; font-weight:900; color:var(--ink);"><?= number_format($o['total'], 2) ?> <span style="font-size:16px; font-weight:800; color:var(--primary);">MAD</span></span>
                                    </div>
                                </div>

                                
                                <div class="livreur-order-grid">
                                    
                                    
                                    <div style="display:flex; flex-direction:column; gap:24px;">
                                        <div>
                                            <span style="font-size:10px; font-weight:800; text-transform:uppercase; color:var(--muted); letter-spacing:0.08em; display:block; margin-bottom:10px;">Destinataire</span>
                                            <div style="display:flex; align-items:center; gap:16px; background:var(--bg); padding:16px; border-radius:18px; border:1px solid var(--border);">
                                                <div style="width:48px; height:48px; border-radius:12px; background:var(--white); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-weight:900; font-size:18px; color:var(--primary); box-shadow:var(--shadow-sm); flex-shrink:0;">
                                                    <?= strtoupper(substr($o['customer_name'] ?? 'U', 0, 1)) ?>
                                                </div>
                                                <div style="overflow:hidden;">
                                                    <div style="font-size:15.5px; font-weight:800; color:var(--ink); text-overflow:ellipsis; overflow:hidden; white-space:nowrap;"><?= e($o['customer_name']) ?></div>
                                                    <div style="font-size:12.5px; color:var(--ink-soft); text-overflow:ellipsis; overflow:hidden; white-space:nowrap; margin-top:2px;"><?= e($o['customer_email']) ?></div>
                                                </div>
                                            </div>
                                        </div>

                                        
                                        <?php if ($o['customer_phone']): ?>
                                            <div>
                                                <span style="font-size:10px; font-weight:800; text-transform:uppercase; color:var(--muted); letter-spacing:0.08em; display:block; margin-bottom:10px;">Contact Client (<?= e($o['customer_phone']) ?>)</span>
                                                <div style="display:flex; gap:12px;">
                                                    <a href="tel:<?= e($o['customer_phone']) ?>" class="livreur-btn-action call">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                                        Appeler
                                                    </a>
                                                    <?php 
                                                    $cleanPhone = preg_replace('/[^0-9]/', '', $o['customer_phone']);
                                                    if (str_starts_with($cleanPhone, '0')) {
                                                        $cleanPhone = '212' . substr($cleanPhone, 1);
                                                    }
                                                    $waMessage = "Bonjour " . $o['customer_name'] . ", c'est votre livreur MangaShop ! Je suis en route pour vous livrer votre colis (Commande #" . $o['id'] . ") d'un montant de " . number_format($o['total'], 2) . " MAD. Serez-vous disponible à l'adresse suivante : " . $o['customer_address'] . " ?";
                                                    ?>
                                                    <a href="https://wa.me/<?= $cleanPhone ?>?text=<?= urlencode($waMessage) ?>" target="_blank" class="livreur-btn-action whatsapp">
                                                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.513 2.262 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.457L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.97C16.484 2.016 14.017 1 11.997 1 6.558 1 2.13 5.37 2.127 10.8c-.001 1.757.479 3.472 1.39 5.02l-.36 1.31 1.37-.359 1.22.723zm10.74-5.321c-.302-.15-1.786-.881-2.062-.982-.278-.1-.48-.15-.68.15-.2.3-.775.982-.95 1.183-.175.2-.35.225-.65.075-.302-.15-1.274-.469-2.427-1.496-.897-.8-1.502-1.79-1.678-2.091-.175-.3-.018-.462.13-.61.135-.135.302-.35.45-.525.15-.175.2-.3.3-.5.1-.2.05-.375-.025-.525-.075-.15-.68-1.64-1.332-3.212-.51-1.22-1.066-1.06-1.48-1.082-.2-.01-.43-.012-.66-.012-.23 0-.6.09-.913.43-.313.34-1.2 1.17-1.2 2.859 0 1.689 1.23 3.32 1.4 3.54 1.65 2.16 3.56 3.285 5.56 4.09.43.17.86.27 1.18.37.77.24 1.47.2 2.02.12.61-.09 1.786-.73 2.037-1.43.25-.7.25-1.3.175-1.43-.075-.13-.275-.205-.575-.355z"/></svg>
                                                        WhatsApp
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        
                                        <div>
                                            <span style="font-size:10px; font-weight:800; text-transform:uppercase; color:var(--muted); letter-spacing:0.08em; display:block; margin-bottom:8px;">Description du colis</span>
                                            <div style="font-size:13.5px; color:var(--ink-soft); line-height:1.6; background:var(--bg); border:1px solid var(--border); border-radius:18px; padding:18px; display:flex; flex-direction:column; gap:10px;">
                                                <?php if (!empty($o['items'])): ?>
                                                    <?php foreach($o['items'] as $item): ?>
                                                        <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom:6px; border-bottom:1px solid var(--border);">
                                                            <span style="display:inline-flex; align-items:center; gap:8px; font-weight:600; color:var(--ink);">
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="3" style="flex-shrink:0;"><polyline points="20 6 9 17 4 12"/></svg>
                                                                <?= e($item['title']) ?>
                                                            </span>
                                                            <span style="font-weight:900; background:var(--white); color:var(--ink); border:1px solid var(--border); padding:2px 8px; border-radius:8px; font-size:12px;">x<?= $item['quantity'] ?></span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <em style="color:var(--muted);">Détails non disponibles</em>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    
                                    <div style="display:flex; flex-direction:column; gap:24px;">
                                        
                                        
                                        <div>
                                            <span style="font-size:10px; font-weight:800; text-transform:uppercase; color:var(--muted); letter-spacing:0.08em; display:block; margin-bottom:10px;">Adresse de livraison</span>
                                            <div class="livreur-gps-box">
                                                <div style="font-size:16px; font-weight:900; color:var(--ink); letter-spacing:-0.01em;"><?= e($o['city']) ?></div>
                                                <div style="font-size:13px; color:var(--ink-soft); line-height:1.5; margin-top:6px; font-weight:500;"><?= e($o['customer_address']) ?></div>
                                                
                                                <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($o['customer_address'] . ', ' . $o['city']) ?>" 
                                                   target="_blank" 
                                                   class="livreur-btn-gps">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                                                    Itinéraire GPS
                                                </a>
                                            </div>
                                        </div>

                                        
                                        <div>
                                            <span style="font-size:10px; font-weight:800; text-transform:uppercase; color:var(--muted); letter-spacing:0.08em; display:block; margin-bottom:12px;">Statut de la livraison</span>
                                            
                                            <form method="POST" style="width:100%;">
                                                <input type="hidden" name="action" value="update_delivery_status">
                                                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                                <input type="hidden" name="status" id="status-field-<?= $o['id'] ?>" value="<?= $o['status'] ?>">
                                                
                                                <div class="livreur-segmented-control">
                                                    <button type="button" onclick="submitStatus(<?= $o['id'] ?>, 'processing')" class="livreur-segmented-btn processing <?= $o['status']==='processing'?'active':'' ?>">
                                                        A préparer
                                                    </button>
                                                    <button type="button" onclick="submitStatus(<?= $o['id'] ?>, 'shipped')" class="livreur-segmented-btn shipped <?= $o['status']==='shipped'?'active':'' ?>">
                                                        En livraison
                                                    </button>
                                                    <button type="button" onclick="submitStatus(<?= $o['id'] ?>, 'delivered')" class="livreur-segmented-btn delivered <?= $o['status']==='delivered'?'active':'' ?>">
                                                        Livrée
                                                    </button>
                                                </div>
                                            </form>
                                            
                                            <?php if ($o['status'] === 'delivered'): ?>
                                                <div style="display:flex; align-items:center; gap:8px; color:var(--green); font-weight:800; font-size:13px; background:#e6f4ea; padding:12px 16px; border-radius:14px; border:1px solid rgba(16,185,129,0.15); margin-top:14px; justify-content:center; box-shadow:var(--shadow-sm); animation: pulseGlowSuccess 2s infinite ease-in-out;">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="flex-shrink:0;"><polyline points="20 6 9 17 4 12"/></svg>
                                                    Livraison finalisée avec succès
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align:center; padding:100px 20px; background:var(--white); border-radius:var(--radius-lg); border:1px solid var(--border); box-shadow:var(--shadow-sm);">
                        <div style="font-size:48px; margin-bottom:16px; opacity:0.4; display:flex; justify-content:center; color:var(--muted);">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        </div>
                        <h3 style="font-size:20px; font-weight:800; margin-bottom:8px; color:var(--ink);">Aucune livraison</h3>
                        <p style="color:var(--muted); font-size:14px;">Aucune commande ne vous est assignée pour le moment.</p>
                    </div>
                <?php endif; ?>
                
                <script>
                function submitStatus(orderId, statusValue) {
                    const field = document.getElementById('status-field-' + orderId);
                    if (field) {
                        field.value = statusValue;
                        field.form.submit();
                    }
                }
                </script>
            </div>
            </div>

        <?php elseif ($tab === 'orders'): ?>
            <div>
                <?php if ($orders): ?>
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        <?php foreach ($orders as $o): 
                            $statusSteps = ['pending'=>0,'processing'=>1,'shipped'=>2,'delivered'=>3,'cancelled'=>-1];
                            $currentStep = $statusSteps[$o['status']] ?? 0;
                            $isCancelled = $o['status'] === 'cancelled';
                        ?>
                            <div style="border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;background:var(--white);box-shadow:var(--shadow-sm);">
                                <div class="order-card" style="margin-bottom:0;border:none;border-radius:0;" onclick="toggleOrderTimeline(<?= $o['id'] ?>)">
                                    <div style="display:flex; align-items:center; gap:20px;">
                                        <div class="order-icon">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                                        </div>
                                        <div>
                                            <div style="font-size:16px; font-weight:800; color:var(--ink); margin-bottom:4px;">Commande 
                                            <div style="font-size:13px; font-weight:500; color:var(--muted); margin-bottom:8px;">Passée le <?= date('d M Y', strtotime($o['created_at'])) ?></div>
                                            <div style="font-size:13px; color:var(--ink-soft);">
                                                <?php if (!empty($o['items'])) {
                                                    $summary = [];
                                                    foreach($o['items'] as $item) $summary[] = '<strong>'.$item['quantity'].'x</strong> '.e($item['title']);
                                                    echo implode(' • ', $summary);
                                                } else { echo '<em>Détails non disponibles</em>'; } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="order-meta" style="display:flex; align-items:center; gap:24px;">
                                        <div style="font-size:15px; font-weight:800; color:var(--ink);"><?= number_format($o['total'], 2) ?> MAD</div>
                                        <?php
                                        $statusLabels = ['pending'=>'En attente','processing'=>'Confirmée','shipped'=>'Expédiée','delivered'=>'Livrée','cancelled'=>'Annulée'];
                                        $statusColors = ['pending'=>'rgba(162,79,43,0.1)','processing'=>'rgba(59,130,246,0.1)','shipped'=>'rgba(245,158,11,0.1)','delivered'=>'rgba(16,185,129,0.1)','cancelled'=>'rgba(239,68,68,0.1)'];
                                        $statusText = ['pending'=>'var(--primary)','processing'=>'#1d4ed8','shipped'=>'#92400e','delivered'=>'#065f46','cancelled'=>'var(--red)'];
                                        ?>
                                        <span class="status-badge" style="background:<?= $statusColors[$o['status']] ?? 'var(--bg)' ?>;color:<?= $statusText[$o['status']] ?? 'var(--ink)' ?>;border-color:transparent;">
                                            <?= $statusLabels[$o['status']] ?? ucfirst($o['status']) ?>
                                        </span>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2.5" id="tl-arrow-<?= $o['id'] ?>" style="transition:transform 0.3s;"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </div>
                                </div>
                                
                                <div class="order-timeline" id="timeline-<?= $o['id'] ?>">
                                    <?php if ($isCancelled): ?>
                                        <div style="text-align:center;padding:16px;color:var(--red);font-weight:700;font-size:14px;">Commande annulée</div>
                                    <?php else: ?>
                                    <div class="timeline-steps">
                                        <?php
                                        $steps = [
                                            ['pending',   '1', 'En attente'],
                                            ['processing','2', 'Confirmée'],
                                            ['shipped',   '3', 'Expédiée'],
                                            ['delivered', '4', 'Livrée'],
                                        ];
                                        foreach ($steps as $i => [$key, $icon, $label]):
                                            $cls = $i < $currentStep ? 'done' : ($i === $currentStep ? 'current' : '');
                                        ?>
                                        <div class="tl-step <?= $cls ?>">
                                            <div class="tl-dot"><?= $icon ?></div>
                                            <div class="tl-label"><?= $label ?></div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align:center; padding:100px 20px; background:var(--white); border-radius:var(--radius-lg); border:1px solid var(--border); box-shadow:var(--shadow-sm);">
                        <div style="font-size:48px; margin-bottom:16px; opacity:0.4; display:flex; justify-content:center; color:var(--muted);">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                        </div>
                        <h3 style="font-size:20px; font-weight:800; margin-bottom:8px; color:var(--ink);">Aucune commande</h3>
                        <p style="color:var(--muted); font-size:14px;">Vous n'avez pas encore passé de commande sur MangaShop.</p>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($tab === 'wishlist'): ?>
            <div>
                <?php if ($wishlist): ?>
                    <div class="products-grid">
                        <?php foreach ($wishlist as $p): include 'includes/product_card.php'; endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align:center; padding:100px 20px; background:var(--white); border-radius:var(--radius-lg); border:1px solid var(--border); box-shadow:var(--shadow-sm);">
                        <div style="font-size:48px; margin-bottom:16px; opacity:0.4; display:flex; justify-content:center; color:var(--muted);">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                        </div>
                        <h3 style="font-size:20px; font-weight:800; margin-bottom:8px; color:var(--ink);">Aucun favori</h3>
                        <p style="color:var(--muted); font-size:14px;">Explorez le catalogue pour ajouter des articles à vos favoris.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
</div>

<script>
function toggleOrderTimeline(id) {
    const tl = document.getElementById('timeline-' + id);
    const arrow = document.getElementById('tl-arrow-' + id);
    if (!tl) return;
    tl.classList.toggle('open');
    if (arrow) arrow.style.transform = tl.classList.contains('open') ? 'rotate(90deg)' : '';
}
</script>
<?php require_once 'includes/footer.php'; ?>
