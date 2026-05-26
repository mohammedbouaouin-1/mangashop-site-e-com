<?php require_once 'includes/header.php'; ?>

<div style="background: var(--bg); padding-bottom: 80px;">

    
    <style>
        .scene-container-3d {
            perspective: 1200px;
            height: 360px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin-bottom: 24px;
            background: radial-gradient(circle at center, rgba(212,175,55,0.08) 0%, rgba(255,255,255,0) 70%);
            cursor: grab;
        }
        .scene-container-3d:active {
            cursor: grabbing;
        }

        .book-mockup {
            --book-width: 170px;
            --book-height: 240px;
            --thickness: 20px;
            --thickness-half: 10px;
            width: var(--book-width);
            height: var(--book-height);
            position: relative;
            transform-style: preserve-3d;
            transform: rotateY(-18deg) rotateX(12deg);
            transition: transform 0.15s linear, width 0.5s ease, height 0.5s ease;
        }

        .book-cover {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            transform-style: preserve-3d;
            transition: all 0.5s ease;
        }

        .book-cover.front {
            transform: translateZ(var(--thickness-half));
            border-radius: 3px 8px 8px 3px;
            z-index: 5;
            box-shadow: inset -2px 0 8px rgba(0,0,0,0.3), inset 1px 1px 0 rgba(255,255,255,0.1);
            overflow: hidden;
        }

        .cover-design {
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;
            border: 1px solid rgba(214, 175, 55, 0.25);
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            padding: 20px 10px;
            color: #fff;
            pointer-events: none;
            background: rgba(0,0,0,0.2);
        }

        .cover-title {
            font-family: 'Playfair Display', serif;
            font-size: 13px;
            color: #fff;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 700;
            text-align: center;
            word-wrap: break-word;
            max-width: 100%;
        }

        .cover-badge {
            width: 32px;
            height: 32px;
            border: 1px solid rgba(255,255,255,0.5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.9);
            font-size: 10px;
            font-weight: 800;
            background: rgba(17, 17, 17, 0.4);
        }

        .cover-subtitle {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.85);
            font-weight: 600;
        }

        .book-cover.back {
            transform: translateZ(calc(var(--thickness-half) * -1)) rotateY(180deg);
            background: var(--primary);
            border-radius: 8px 3px 3px 8px;
            box-shadow: -15px 15px 30px rgba(0,0,0,0.35);
            z-index: 1;
        }

        .book-spine {
            position: absolute;
            height: 100%;
            width: var(--thickness);
            top: 0;
            left: 0;
            background: var(--primary);
            transform: rotateY(-90deg) translateZ(calc(var(--thickness-half) - 1px));
            transform-origin: left center;
            z-index: 4;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.8);
            border-left: 1px solid rgba(255,255,255,0.05);
        }

        .pages-stack {
            position: absolute;
            height: 96%;
            width: 97%;
            top: 2%;
            left: 1%;
            background: var(--bg2);
            transform-style: preserve-3d;
            z-index: 3;
        }

        .pages-stack .edge-right {
            position: absolute;
            height: 100%;
            width: var(--thickness);
            right: 0;
            top: 0;
            background: repeating-linear-gradient(90deg, #f5f5f0 0px, #e8e5df 1px, #f5f5f0 2px, #f5f5f0 4px);
            transform: rotateY(90deg) translateZ(calc(var(--thickness) / -2));
            transform-origin: right center;
            box-shadow: inset 5px 0 10px rgba(0,0,0,0.08);
        }

        .pages-stack .edge-top {
            position: absolute;
            width: 100%;
            height: var(--thickness);
            top: 0;
            left: 0;
            background: repeating-linear-gradient(180deg, #f5f5f0 0px, #e8e5df 1px, #f5f5f0 2px, #f5f5f0 4px);
            transform: rotateX(90deg) translateZ(calc(var(--thickness) / 2));
            transform-origin: top center;
            box-shadow: inset 0 5px 10px rgba(0,0,0,0.08);
        }

        .pages-stack .edge-bottom {
            position: absolute;
            width: 100%;
            height: var(--thickness);
            bottom: 0;
            left: 0;
            background: repeating-linear-gradient(0deg, #f5f5f0 0px, #e8e5df 1px, #f5f5f0 2px, #f5f5f0 4px);
            transform: rotateX(-90deg) translateZ(calc(var(--thickness) / 2));
            transform-origin: bottom center;
            box-shadow: inset 0 -5px 10px rgba(0,0,0,0.08);
        }

        
        .book-mockup.finish-soft .book-cover.front {
            background: linear-gradient(135deg, #2d2d2d, #555);
        }

        .book-mockup.finish-glossy .book-cover.front {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
        }

        .shine-overlay {
            display: none;
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(105deg, rgba(255,255,255,0) 30%, rgba(255,255,255,0.18) 45%, rgba(255,255,255,0.28) 50%, rgba(255,255,255,0.18) 55%, rgba(255,255,255,0) 70%);
            background-size: 250% 100%;
            pointer-events: none;
            z-index: 6;
        }

        .book-mockup.finish-glossy .shine-overlay {
            display: block;
            animation: shineEffect 5s infinite linear;
        }

        @keyframes shineEffect {
            0% { background-position: -150% 0; }
            50% { background-position: 150% 0; }
            100% { background-position: 150% 0; }
        }

        
        .book-mockup.finish-hard .book-cover.front {
            background: linear-gradient(135deg, #1c1412, #a24f2b);
            border: 2px solid rgba(212,175,55,0.3);
            box-shadow: inset -2px 0 10px rgba(0,0,0,0.5), 0 0 10px rgba(212,175,55,0.15);
        }
        .book-mockup.finish-hard .book-cover.front .cover-title {
            color: #fff;
        }
        .book-mockup.finish-hard .book-cover.front .cover-badge {
            border-color: rgba(255,255,255,0.5);
            color: rgba(255,255,255,0.85);
        }
        .book-mockup.finish-hard .book-cover.back {
            background: var(--primary);
            border: 2px solid rgba(255,255,255,0.15);
        }
        .book-mockup.finish-hard .pages-stack .edge-right,
        .book-mockup.finish-hard .pages-stack .edge-top,
        .book-mockup.finish-hard .pages-stack .edge-bottom {
            background: linear-gradient(90deg, #d4af37, #b8952a);
        }

        
        .glass-billing-panel {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 16px;
            padding: 24px;
            margin-top: 24px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.04);
            color: var(--ink);
        }

        .billing-title {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border);
            padding-bottom: 10px;
        }

        .billing-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 13px;
            color: var(--ink-soft);
        }

        .billing-row.grand-total {
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px dashed var(--border);
            font-weight: 800;
            font-size: 15px;
            color: var(--ink);
        }

        .gold-discount-badge {
            background: linear-gradient(135deg, #d4af37, #b8952a);
            color: #1a1209;
            font-weight: 800;
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(212, 175, 55, 0.35);
            display: none;
            animation: badgePulse 1.5s infinite alternate;
        }

        @keyframes badgePulse {
            0% { transform: scale(1); }
            100% { transform: scale(1.08); }
        }

        .live-value {
            font-weight: 700;
            color: var(--ink);
        }

        
        .upload-zone {
            border: 2px dashed var(--border);
            border-radius: var(--radius-md);
            padding: 24px 20px;
            text-align: center;
            background: var(--bg);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
        }
        .upload-zone:hover, .upload-zone.dragover {
            border-color: var(--gold);
            background: rgba(212, 175, 55, 0.03);
            box-shadow: 0 0 16px rgba(212, 175, 55, 0.05);
        }
        .upload-icon {
            width: 44px;
            height: 44px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            color: var(--gold);
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }
        .upload-zone:hover .upload-icon {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: var(--gold);
        }
        .upload-filename {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--green);
            display: none;
            align-items: center;
            gap: 8px;
            background: var(--white);
            padding: 6px 14px;
            border-radius: 100px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }

        
        .paper-preview-card {
            margin-top: 20px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: var(--shadow-sm);
        }
        .paper-texture-box {
            width: 52px;
            height: 52px;
            border-radius: 10px;
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 0 8px rgba(0,0,0,0.04);
            transition: all 0.3s;
        }
        .paper-texture-box::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            opacity: 0.08;
            background-image: radial-gradient(var(--ink) 0.5px, transparent 0.5px), radial-gradient(var(--ink) 0.5px, transparent 0.5px);
            background-size: 4px 4px;
            background-position: 0 0, 2px 2px;
        }

        
        @keyframes floatManga1 {
            0% { transform: translateY(0) rotate(-15deg) scale(1); }
            100% { transform: translateY(-15px) rotate(-12deg) scale(1.02); }
        }
        @keyframes floatManga2 {
            0% { transform: translateY(0) rotate(12deg) scale(1); }
            100% { transform: translateY(-20px) rotate(15deg) scale(1.03); }
        }
        @keyframes floatManga3 {
            0% { transform: translateY(0) rotate(18deg) scale(1); }
            100% { transform: translateY(-12px) rotate(14deg) scale(1.02); }
        }
        @keyframes floatManga4 {
            0% { transform: translateY(0) rotate(-10deg) scale(1); }
            100% { transform: translateY(-18px) rotate(-7deg) scale(1.03); }
        }
        
        .floating-manga {
            transition: opacity 0.5s cubic-bezier(0.4, 0, 0.2, 1), filter 0.5s ease, transform 0.5s ease;
        }
        .print-hero-section:hover .floating-manga {
            opacity: 0.12 !important;
            filter: grayscale(60%) !important;
        }

        @media (max-width: 768px) {
            .floating-mangas-container {
                display: none !important;
            }
        }
    </style>

    
    <section class="print-hero-section" style="padding: 120px 20px 80px; text-align: center; border-bottom: 1px solid var(--border); position: relative; overflow: hidden; background: var(--bg);">
        
        
        <div class="floating-mangas-container" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; overflow: hidden; z-index: 1;">
            
            <div class="floating-manga fm-1" style="position: absolute; top: 8%; left: 5%; width: 110px; height: 160px; transform: rotate(-15deg); opacity: 0.06; filter: grayscale(100%); border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); border: 1px solid var(--border); background-image: url('<?= asset('assets/images/covers/one-piece-vol-1.jpg') ?>'); background-size: cover; background-position: center; animation: floatManga1 8s ease-in-out infinite alternate;"></div>
            
            
            <div class="floating-manga fm-2" style="position: absolute; bottom: 8%; left: 12%; width: 120px; height: 175px; transform: rotate(12deg); opacity: 0.04; filter: grayscale(100%); border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); border: 1px solid var(--border); background-image: url('<?= asset('assets/images/covers/berserk-vol-1.jpg') ?>'); background-size: cover; background-position: center; animation: floatManga2 10s ease-in-out infinite alternate;"></div>
            
            
            <div class="floating-manga fm-3" style="position: absolute; top: 12%; right: 6%; width: 115px; height: 165px; transform: rotate(18deg); opacity: 0.06; filter: grayscale(100%); border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); border: 1px solid var(--border); background-image: url('<?= asset('assets/images/covers/chainsaw-man-vol-1.jpg') ?>'); background-size: cover; background-position: center; animation: floatManga3 9s ease-in-out infinite alternate;"></div>
            
            
            <div class="floating-manga fm-4" style="position: absolute; bottom: 6%; right: 10%; width: 125px; height: 180px; transform: rotate(-10deg); opacity: 0.05; filter: grayscale(100%); border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); border: 1px solid var(--border); background-image: url('<?= asset('assets/images/covers/vagabond-vol-1.jpg') ?>'); background-size: cover; background-position: center; animation: floatManga4 11s ease-in-out infinite alternate;"></div>
            
            
            <div class="floating-manga fm-5" style="position: absolute; top: 40%; left: -2%; width: 100px; height: 145px; transform: rotate(8deg); opacity: 0.03; filter: grayscale(100%); border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); border: 1px solid var(--border); background-image: url('<?= asset('assets/images/covers/death-note-vol-1.jpg') ?>'); background-size: cover; background-position: center; animation: floatManga1 12s ease-in-out infinite alternate;"></div>

            
            <div class="floating-manga fm-6" style="position: absolute; top: 44%; right: -2%; width: 100px; height: 145px; transform: rotate(-12deg); opacity: 0.03; filter: grayscale(100%); border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); border: 1px solid var(--border); background-image: url('<?= asset('assets/images/covers/naruto-vol-1.jpg') ?>'); background-size: cover; background-position: center; animation: floatManga3 12s ease-in-out infinite alternate;"></div>
        </div>

        <div class="section-container" style="max-width: 800px; margin: 0 auto; position: relative; z-index: 2;">
            <span class="section-label" style="display:inline-block; margin-bottom: 16px;">Atelier Impression</span>
            <h1 style="font-family:'Playfair Display',serif; font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 900; line-height: 1.1; margin-bottom: 24px; color: var(--ink);">
                Donnez vie à vos<br>
                <span style="color: var(--gold);">Chef-d'œuvres</span>
            </h1>
            <p style="font-size: 18px; color: var(--ink-soft); line-height: 1.6; margin-bottom: 40px;">
                Impression haute fidélité, reliures artisanales et papiers premium. 
                Le service d'excellence pour les mangakas et passionnés au Maroc.
            </p>
            <div style="display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
                <a href="#configurer" style="display: inline-block; padding: 16px 40px; background: var(--ink); color: #fff; border-radius: var(--radius-sm); font-weight: 700; text-decoration: none; text-transform: uppercase; font-size: 13px; letter-spacing: 1px; transition: background 0.3s;" onmouseover="this.style.background='var(--gold)'; this.style.color='#000';" onmouseout="this.style.background='var(--ink)'; this.style.color='#fff';">
                    Estimer mon projet
                </a>
            </div>
        </div>
    </section>

    
    <section class="section-container" style="padding: 80px 20px;">
        <div style="text-align: center; margin-bottom: 48px;">
            <span class="section-label">Pourquoi nous ?</span>
            <h2 class="section-title">La promesse d'une qualité <span style="color:var(--gold);">inégalée</span></h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 32px;">
            <div style="background: var(--white); padding: 40px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); border: 1px solid var(--border); text-align: center;">
                <div style="width: 60px; height: 60px; background: var(--bg2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--ink)" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
                </div>
                <h3 style="font-family:'Playfair Display',serif; font-size: 22px; font-weight: 800; margin-bottom: 12px;">Impression 1200 DPI</h3>
                <p style="color: var(--muted); font-size: 14px; line-height: 1.6;">Rendu des trames chirurgical pour un noir profond et des détails d'une finesse absolue.</p>
            </div>
            
            <div style="background: var(--white); padding: 40px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); border: 1px solid var(--border); text-align: center;">
                <div style="width: 60px; height: 60px; background: var(--bg2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--ink)" stroke-width="2"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
                </div>
                <h3 style="font-family:'Playfair Display',serif; font-size: 22px; font-weight: 800; margin-bottom: 12px;">Papier Premium</h3>
                <p style="color: var(--muted); font-size: 14px; line-height: 1.6;">Textures certifiées FSC, spécialement sélectionnées pour le meilleur rendu manga.</p>
            </div>

            <div style="background: var(--white); padding: 40px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); border: 1px solid var(--border); text-align: center;">
                <div style="width: 60px; height: 60px; background: var(--bg2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--ink)" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3 style="font-family:'Playfair Display',serif; font-size: 22px; font-weight: 800; margin-bottom: 12px;">Reliure Haute Précision</h3>
                <p style="color: var(--muted); font-size: 14px; line-height: 1.6;">Collage PUR garantissant une résistance maximale et une ouverture à plat parfaite.</p>
            </div>
        </div>
    </section>

    
    <section id="configurer" class="section-container" style="padding: 40px 20px;">
        <div style="background: var(--white); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); border: 1px solid var(--border); overflow: hidden; display: flex; flex-direction: column;">
            
            <?php if ($success): ?>
            <div style="text-align: center; padding: 100px 40px;">
                <div style="width:80px; height:80px; background:var(--green-light); border-radius:50%; display:flex; align-items:center; justify-content:center; margin: 0 auto 24px;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <h2 style="font-family:'Playfair Display',serif; font-size:32px; font-weight:800; margin-bottom:16px;">Demande transmise avec succès</h2>
                <p style="color:var(--ink-soft); font-size:16px; max-width:500px; margin:0 auto 32px;">
                    Votre projet est unique. Notre atelier va étudier vos spécifications et vous recevrez un devis détaillé d'ici 24h ouvrées.
                </p>
                <a href="index.php" style="display:inline-block; padding: 14px 32px; background:var(--bg2); color:var(--ink); font-weight:700; border-radius:var(--radius-sm); border:1px solid var(--border); transition:all 0.2s;" onmouseover="this.style.background='var(--ink)'; this.style.color='#fff';" onmouseout="this.style.background='var(--bg2)'; this.style.color='var(--ink)';">Retour à l'accueil</a>
            </div>
            
            <?php else: ?>
            <div style="display: flex; flex-wrap: wrap;">
                
                <div style="flex: 1; min-width: 300px; padding: 48px 36px; background: var(--bg2); border-right: 1px solid var(--border); display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <h2 style="font-family:'Playfair Display',serif; font-size: 32px; font-weight:800; margin-bottom:8px;">Atelier <span style="color:var(--gold);">Live 3D</span></h2>
                        <p style="color:var(--ink-soft); line-height:1.5; font-size:14px; margin-bottom: 24px;">
                            Visualisez votre ouvrage sous tous les angles avec notre simulateur d'impression haute définition.
                        </p>
                        
                        
                        <div class="scene-container-3d">
                            <div id="liveBook" class="book-mockup finish-soft">
                                
                                <div class="book-cover front">
                                    <div class="shine-overlay"></div>
                                    <div class="cover-design">
                                        <div class="cover-subtitle">MangaShop Premium</div>
                                        <div class="cover-title" id="bookTitle">MANGA</div>
                                        <div class="cover-badge"></div>
                                        <div class="cover-subtitle" id="bookFormatLabel">A5 EDITION</div>
                                    </div>
                                </div>
                                
                                <div class="book-spine" style="display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 20px 0; box-sizing: border-box;">
                                    <div id="bookSpineTitle" style="font-family: 'Playfair Display', serif; font-size: 8px; color: rgba(255,255,255,0.75); text-transform: uppercase; font-weight: 800; transform: rotate(90deg); white-space: nowrap; letter-spacing: 1.5px; pointer-events: none;">MANGA</div>
                                </div>
                                
                                <div class="pages-stack">
                                    <div class="edge-right"></div>
                                    <div class="edge-top"></div>
                                    <div class="edge-bottom"></div>
                                </div>
                                
                                <div class="book-cover back"></div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="glass-billing-panel">
                        <div class="billing-title">
                            <span>Estimation instantanée</span>
                            <span id="discountBadge" class="gold-discount-badge">-10%</span>
                        </div>
                        <div class="billing-row">
                            <span>Format :</span>
                            <span id="labelFormat" class="live-value">A5 (Standard)</span>
                        </div>
                        <div class="billing-row">
                            <span>Couverture :</span>
                            <span id="labelCover" class="live-value">Souple Mate</span>
                        </div>
                        <div class="billing-row">
                            <span>Type de papier :</span>
                            <span id="labelPaper" class="live-value">Bouffant 90g (Crème)</span>
                        </div>
                        <div class="billing-row">
                            <span>Pages :</span>
                            <span id="labelPages" class="live-value">192 pages</span>
                        </div>
                        <div class="billing-row">
                            <span>Quantité :</span>
                            <span id="labelQty" class="live-value">1 ex.</span>
                        </div>
                        <div class="billing-row">
                            <span>Prix unitaire :</span>
                            <span class="live-value"><span id="unitPriceDisp">0.00</span> MAD</span>
                        </div>
                        <div class="billing-row">
                            <span>Frais de préparation :</span>
                            <span class="live-value">50.00 MAD</span>
                        </div>
                        <div class="billing-row grand-total">
                            <span>Total TTC Estimé :</span>
                            <span style="color:var(--gold); font-size: 18px;"><span id="totalPriceDisp">0.00</span> MAD</span>
                        </div>
                    </div>

                    
                    <div class="paper-preview-card">
                        <div class="paper-texture-box" id="paperTextureBox" style="background:#faf5eb;"></div>
                        <div>
                            <div style="font-size:12px; font-weight:800; text-transform:uppercase; color:var(--gold); letter-spacing:0.5px;">Texture du Papier</div>
                            <div id="paperDescTitle" style="font-size:14px; font-weight:700; color:var(--ink); margin-top:2px;">Bouffant 90g (Crème Manga)</div>
                            <div id="paperDescText" style="font-size:11.5px; color:var(--muted); margin-top:2px; line-height:1.4;">Le grain traditionnel jaune crème des mangas japonais de librairie.</div>
                        </div>
                    </div>
                </div>

                <div style="flex: 2; min-width: 300px; padding: 64px 48px;">
                    <?php if ($error): ?>
                        <div style="background:var(--red-light); color:var(--red); padding:16px; border-radius:var(--radius-sm); margin-bottom:32px; font-weight:600; font-size:14px;">
                            <?= e($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:24px;">
                        <style>
                            .print-input {
                                width: 100%; padding: 14px 16px; background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 14px; outline: none; transition: all 0.2s; font-family: inherit;
                            }
                            .print-input:focus {
                                border-color: var(--ink); background: var(--white); box-shadow: 0 0 0 3px rgba(17,17,17,0.05);
                            }
                            .print-label {
                                display: block; font-size: 12px; font-weight: 700; color: var(--ink); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;
                            }
                        </style>

                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px;">
                            <div>
                                <label class="print-label">Nom complet</label>
                                <input type="text" name="name" id="customerName" class="print-input" placeholder="Ex: Mohammed Bouaouin" value="<?= e($autofillUser['name'] ?? '') ?>" required>
                            </div>
                            <div>
                                <label class="print-label">Email de contact</label>
                                <input type="email" name="email" class="print-input" placeholder="artist@domain.com" value="<?= e($autofillUser['email'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:24px;">
                            <div>
                                <label class="print-label">Format souhaité</label>
                                <select name="format_type" id="formatType" class="print-input" style="appearance:none; background-image:url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23111111%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat:no-repeat; background-position:right 16px top 50%; background-size:10px auto;">
                                    <option value="A5">A5 (Standard)</option>
                                    <option value="B6">B6 (Tankobon)</option>
                                    <option value="A4">Grand Format (A4)</option>
                                </select>
                            </div>
                            <div>
                                <label class="print-label">Type de couverture</label>
                                <select name="cover_type" id="coverType" class="print-input" style="appearance:none; background-image:url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23111111%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat:no-repeat; background-position:right 16px top 50%; background-size:10px auto;">
                                    <option value="soft">Souple Mate</option>
                                    <option value="soft_glossy">Souple Brillante</option>
                                    <option value="hard">Édition Rigide</option>
                                </select>
                            </div>
                            <div>
                                <label class="print-label">Type de papier</label>
                                <select name="paper_type" id="paperType" class="print-input" style="appearance:none; background-image:url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23111111%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat:no-repeat; background-position:right 16px top 50%; background-size:10px auto;">
                                    <option value="classic">Bouffant 90g (Crème Manga)</option>
                                    <option value="standard">Offset 80g (Blanc standard)</option>
                                    <option value="luxe">Couché Mat 115g (Luxe Illustration)</option>
                                </select>
                            </div>
                        </div>

                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px;">
                            <div>
                                <label class="print-label">Nombre de pages</label>
                                <input type="number" name="pages" id="pageCount" class="print-input" placeholder="Ex: 192" required min="10" value="192">
                            </div>
                            <div>
                                <label class="print-label">Quantité estimée</label>
                                <input type="number" name="qty" id="quantity" class="print-input" value="1" min="1">
                            </div>
                        </div>

                        <div>
                            <label class="print-label">Déposer vos fichiers (Planches ou Couverture)</label>
                            <div class="upload-zone" id="uploadZone" onclick="document.getElementById('mangaFile').click()">
                                <div class="upload-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                </div>
                                <div style="font-size:13px; font-weight:700; color:var(--ink);">Faites glisser votre fichier ici ou cliquez pour parcourir</div>
                                <div style="font-size:11px; color:var(--muted);">Fichiers acceptés : PDF, ZIP, RAR, PNG, JPG (Max 50Mo)</div>
                                <div class="upload-filename" id="uploadFilename">
                                    <span id="fileNameText">manga_tome_1.zip</span>
                                    <button type="button" id="removeFileBtn" style="border:none; background:none; color:var(--red); font-weight:800; cursor:pointer; font-size:16px; margin-left:6px; padding:2px 6px;" title="Supprimer le fichier">&times;</button>
                                </div>
                                <input type="file" name="manga_file" id="mangaFile" style="display:none;">
                            </div>
                        </div>

                        <div>
                            <label class="print-label">Notes & Spécifications</label>
                            <textarea name="message" class="print-input" rows="4" style="resize:vertical;" placeholder="Ex: Vernis sélectif sur le titre, pages couleur au début..."></textarea>
                        </div>

                        <button type="submit" style="width:100%; padding: 18px; margin-top: 8px; background:var(--ink); color:#fff; border:none; border-radius:var(--radius-sm); font-size:14px; font-weight:700; text-transform:uppercase; letter-spacing:1px; cursor:pointer; transition:all 0.3s;" onmouseover="this.style.background='var(--gold)'; this.style.color='#000';" onmouseout="this.style.background='var(--ink)'; this.style.color='#fff';">
                            Demander un devis détaillé
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const formatTypeInput = document.getElementById('formatType');
        const coverTypeInput = document.getElementById('coverType');
        const paperTypeInput = document.getElementById('paperType');
        const pageCountInput = document.getElementById('pageCount');
        const quantityInput = document.getElementById('quantity');
        const customerNameInput = document.getElementById('customerName');

        const liveBook = document.getElementById('liveBook');
        const bookTitle = document.getElementById('bookTitle');
        const bookSpineTitle = document.getElementById('bookSpineTitle');
        const bookFormatLabel = document.getElementById('bookFormatLabel');

        const labelFormat = document.getElementById('labelFormat');
        const labelCover = document.getElementById('labelCover');
        const labelPaper = document.getElementById('labelPaper');
        const labelPages = document.getElementById('labelPages');
        const labelQty = document.getElementById('labelQty');

        const paperTextureBox = document.getElementById('paperTextureBox');
        const paperDescTitle = document.getElementById('paperDescTitle');
        const paperDescText = document.getElementById('paperDescText');

        const unitPriceDisp = document.getElementById('unitPriceDisp');
        const totalPriceDisp = document.getElementById('totalPriceDisp');
        const discountBadge = document.getElementById('discountBadge');

        let currentUnit = 0;
        let currentTotal = 0;

        let animationFrameUnit = null;
        let animationFrameTotal = null;

        function animateNumber(element, start, end, isUnit = false, duration = 500) {
            const startTime = performance.now();
            
            function update(now) {
                const elapsed = now - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const ease = progress * (2 - progress); 
                const value = start + (end - start) * ease;
                element.textContent = value.toFixed(2);
                
                if (progress < 1) {
                    if (isUnit) {
                        animationFrameUnit = requestAnimationFrame(update);
                    } else {
                        animationFrameTotal = requestAnimationFrame(update);
                    }
                } else {
                    element.textContent = end.toFixed(2);
                }
            }
            
            if (isUnit) {
                if (animationFrameUnit) cancelAnimationFrame(animationFrameUnit);
                animationFrameUnit = requestAnimationFrame(update);
            } else {
                if (animationFrameTotal) cancelAnimationFrame(animationFrameTotal);
                animationFrameTotal = requestAnimationFrame(update);
            }
        }

        function updateSimulator() {
            const format = formatTypeInput.value;
            const cover = coverTypeInput.value;
            const paper = paperTypeInput.value;
            const pages = parseInt(pageCountInput.value) || 0;
            const qty = parseInt(quantityInput.value) || 1;

            
            let width = 170;
            let height = 240;
            let formatLabelText = 'A5 EDITION';
            let formatFriendlyText = 'A5 (Standard)';
            
            if (format === 'B6') {
                width = 150;
                height = 210;
                formatLabelText = 'B6 TANKOBON';
                formatFriendlyText = 'B6 (Tankobon)';
            } else if (format === 'A4') {
                width = 210;
                height = 290;
                formatLabelText = 'A4 DELUXE';
                formatFriendlyText = 'Grand Format (A4)';
            }

            liveBook.style.setProperty('--book-width', `${width}px`);
            liveBook.style.setProperty('--book-height', `${height}px`);
            bookFormatLabel.textContent = formatLabelText;
            labelFormat.textContent = formatFriendlyText;

            
            const thickness = Math.max(4, Math.min(45, 4 + (pages - 10) * 0.1));
            liveBook.style.setProperty('--thickness', `${thickness}px`);
            liveBook.style.setProperty('--thickness-half', `${thickness / 2}px`);
            labelPages.textContent = `${pages} page${pages > 1 ? 's' : ''}`;

            
            liveBook.className = 'book-mockup';
            let coverFriendlyText = 'Souple Mate';
            if (cover === 'soft') {
                liveBook.classList.add('finish-soft');
                coverFriendlyText = 'Souple Mate';
            } else if (cover === 'soft_glossy') {
                liveBook.classList.add('finish-glossy');
                coverFriendlyText = 'Souple Brillante';
            } else if (cover === 'hard') {
                liveBook.classList.add('finish-hard');
                coverFriendlyText = 'Édition Rigide';
            }
            labelCover.textContent = coverFriendlyText;
            labelQty.textContent = `${qty} ex.`;

            
            let paperFriendlyText = 'Bouffant 90g (Crème)';
            let paperBg = '#faf5eb';
            let paperTitleText = 'Bouffant 90g (Crème Manga)';
            let paperDescHTML = "Le grain traditionnel jaune crème des mangas japonais de librairie.";
            
            if (paper === 'standard') {
                paperFriendlyText = 'Offset 80g (Blanc)';
                paperBg = '#ffffff';
                paperTitleText = 'Offset 80g (Blanc Standard)';
                paperDescHTML = "Papier blanc lisse standard, idéal pour un rendu moderne et épuré.";
            } else if (paper === 'luxe') {
                paperFriendlyText = 'Couché Mat 115g (Luxe)';
                paperBg = '#fcfcfc';
                paperTitleText = 'Couché 115g (Luxe Illustration)';
                paperDescHTML = "Touché soyeux et épaisseur premium pour sublimer planches couleurs et artbooks.";
            }

            labelPaper.textContent = paperFriendlyText;
            paperTextureBox.style.background = paperBg;
            paperDescTitle.textContent = paperTitleText;
            paperDescText.textContent = paperDescHTML;

            
            const pagesStack = liveBook.querySelector('.pages-stack');
            if (pagesStack) {
                pagesStack.style.background = paperBg;
                const edgeRight = pagesStack.querySelector('.edge-right');
                const edgeTop = pagesStack.querySelector('.edge-top');
                const edgeBottom = pagesStack.querySelector('.edge-bottom');
                
                if (edgeRight) edgeRight.style.background = 'repeating-linear-gradient(90deg, #f5f5f0 0px, #e8e5df 1px, #f5f5f0 2px, #f5f5f0 4px)';
                if (edgeTop) edgeTop.style.background = 'repeating-linear-gradient(180deg, #f5f5f0 0px, #e8e5df 1px, #f5f5f0 2px, #f5f5f0 4px)';
                if (edgeBottom) edgeBottom.style.background = 'repeating-linear-gradient(0deg, #f5f5f0 0px, #e8e5df 1px, #f5f5f0 2px, #f5f5f0 4px)';
            }

            
            if (customerNameInput) {
                const customerName = customerNameInput.value.trim();
                const displayTitle = customerName ? customerName.substring(0, 14).toUpperCase() : 'MANGA';
                bookTitle.textContent = displayTitle;
                if (bookSpineTitle) {
                    bookSpineTitle.textContent = displayTitle;
                }
            }

            
            let pageRate = 0.15;
            if (format === 'B6') pageRate = 0.12;
            else if (format === 'A4') pageRate = 0.25;

            
            let paperSurcharge = 0;
            if (paper === 'standard') paperSurcharge = 0.02;
            else if (paper === 'luxe') paperSurcharge = 0.08;
            
            pageRate += paperSurcharge;

            let coverSurcharge = 0;
            if (cover === 'soft_glossy') coverSurcharge = 5;
            else if (cover === 'hard') coverSurcharge = 40;

            const baseUnitCost = (pages * pageRate) + coverSurcharge;

            
            let discount = 0;
            if (qty >= 100) {
                discount = 0.30;
                discountBadge.textContent = '-30%';
                discountBadge.style.display = 'inline-block';
            } else if (qty >= 50) {
                discount = 0.20;
                discountBadge.textContent = '-20%';
                discountBadge.style.display = 'inline-block';
            } else if (qty >= 10) {
                discount = 0.10;
                discountBadge.textContent = '-10%';
                discountBadge.style.display = 'inline-block';
            } else {
                discountBadge.style.display = 'none';
            }

            const discountedUnitCost = baseUnitCost * (1 - discount);
            const fixedSetupFee = 50.00;
            const calculatedTotal = (discountedUnitCost * qty) + fixedSetupFee;

            
            animateNumber(unitPriceDisp, currentUnit, discountedUnitCost, true);
            animateNumber(totalPriceDisp, currentTotal, calculatedTotal, false);

            currentUnit = discountedUnitCost;
            currentTotal = calculatedTotal;
        }

        
        formatTypeInput.addEventListener('change', updateSimulator);
        coverTypeInput.addEventListener('change', updateSimulator);
        paperTypeInput.addEventListener('change', updateSimulator);
        pageCountInput.addEventListener('input', updateSimulator);
        quantityInput.addEventListener('input', updateSimulator);
        if (customerNameInput) {
            customerNameInput.addEventListener('input', updateSimulator);
        }

        
        const sceneContainer = document.querySelector('.scene-container-3d');
        if (sceneContainer) {
            sceneContainer.addEventListener('mousemove', function(e) {
                const rect = sceneContainer.getBoundingClientRect();
                const x = e.clientX - rect.left - (rect.width / 2);
                const y = e.clientY - rect.top - (rect.height / 2);
                
                const rotY = -18 + (x / (rect.width / 2)) * 36;
                const rotX = 12 - (y / (rect.height / 2)) * 20;
                
                liveBook.style.transform = `rotateY(${rotY}deg) rotateX(${rotX}deg)`;
            });
            
            sceneContainer.addEventListener('mouseleave', function() {
                liveBook.style.transform = `rotateY(-18deg) rotateX(12deg)`;
            });

            // Touch events for mobile/tablet interactive 3D rotation
            sceneContainer.addEventListener('touchmove', function(e) {
                if (e.touches.length === 1) {
                    const rect = sceneContainer.getBoundingClientRect();
                    const x = e.touches[0].clientX - rect.left - (rect.width / 2);
                    const y = e.touches[0].clientY - rect.top - (rect.height / 2);
                    
                    const rotY = -18 + (x / (rect.width / 2)) * 36;
                    const rotX = 12 - (y / (rect.height / 2)) * 20;
                    
                    liveBook.style.transform = `rotateY(${rotY}deg) rotateX(${rotX}deg)`;
                }
            }, { passive: true });

            sceneContainer.addEventListener('touchend', function() {
                liveBook.style.transform = `rotateY(-18deg) rotateX(12deg)`;
            });
        }

        
        const uploadZone = document.getElementById('uploadZone');
        const mangaFile = document.getElementById('mangaFile');
        const uploadFilename = document.getElementById('uploadFilename');
        const fileNameText = document.getElementById('fileNameText');
        const removeFileBtn = document.getElementById('removeFileBtn');

        if (uploadZone && mangaFile) {
            
            ['dragenter', 'dragover'].forEach(eventName => {
                uploadZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    uploadZone.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    uploadZone.classList.remove('dragover');
                }, false);
            });

            uploadZone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length) {
                    mangaFile.files = files;
                    showFileName(files[0].name);
                }
            }, false);

            mangaFile.addEventListener('change', (e) => {
                if (mangaFile.files.length) {
                    showFileName(mangaFile.files[0].name);
                }
            });

            function showFileName(name) {
                fileNameText.textContent = name;
                uploadFilename.style.display = 'inline-flex';
                
                uploadZone.querySelector('.upload-icon').style.display = 'none';
                uploadZone.querySelectorAll('div')[0].style.display = 'none';
                uploadZone.querySelectorAll('div')[1].style.display = 'none';
            }

            if (removeFileBtn) {
                removeFileBtn.addEventListener('click', (e) => {
                    e.stopPropagation(); 
                    mangaFile.value = '';
                    uploadFilename.style.display = 'none';
                    
                    uploadZone.querySelector('.upload-icon').style.display = 'flex';
                    uploadZone.querySelectorAll('div')[0].style.display = 'block';
                    uploadZone.querySelectorAll('div')[1].style.display = 'block';
                });
            }
        }

        
        updateSimulator();
    });
    </script>

</div>

<?php require_once 'includes/footer.php'; ?>
