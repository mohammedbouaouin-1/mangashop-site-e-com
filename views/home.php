<?php require_once 'includes/header.php'; ?>



<section class="hero-premium-section">
    
    <div style="position:absolute; inset:0; z-index:0; background:radial-gradient(circle at 80% 20%, rgba(162,79,43,0.08) 0%, transparent 50%), radial-gradient(circle at 20% 80%, rgba(198,156,109,0.05) 0%, transparent 50%); filter:blur(40px);"></div>

    <div id="premiumSlider" class="premium-slider-container">
        <?php foreach(array_slice($heroProducts, 0, 4) as $idx => $p): ?>
        <div class="premium-slide <?= $idx === 0 ? 'active' : '' ?>" style="position:absolute; inset:0; display:flex; align-items:center; padding:0 5%; opacity:0; visibility:hidden; transition:all 0.8s cubic-bezier(0.25, 1, 0.5, 1);">
            
            <div style="flex:1; max-width:550px; z-index:2; transform:translateY(40px); opacity:0; transition:all 0.8s 0.2s cubic-bezier(0.25, 1, 0.5, 1);" class="slide-content-anim">
                <span style="display:inline-block; padding:6px 16px; background:rgba(255,255,255,0.8); backdrop-filter:blur(8px); color:var(--primary); font-size:12px; font-weight:800; border-radius:var(--radius-full); box-shadow:0 4px 12px rgba(0,0,0,0.05); margin-bottom:24px; letter-spacing:0.04em; text-transform:uppercase; border:1px solid rgba(255,255,255,1);">À la Une</span>
                
                <h1 style="font-size:clamp(2.5rem, 5vw, 4rem); font-weight:900; line-height:1.1; color:var(--ink); margin-bottom:20px; letter-spacing:-0.03em;"><?= e($p['title']) ?></h1>
                <p style="font-size:17px; color:var(--ink-soft); line-height:1.6; margin-bottom:36px; max-width:480px; font-weight:400;"><?= e(substr($p['description'] ?? '', 0, 140)) ?>...</p>

                <div style="display:flex; align-items:center; gap:20px;">
                    <a href="product.php?slug=<?= e($p['slug']) ?>" class="btn-saas" style="padding:16px 36px; font-size:16px; box-shadow:0 8px 20px rgba(162,79,43,0.3);">
                        Acheter maintenant
                    </a>
                    <span style="font-size:24px; font-weight:900; color:var(--ink);"><?= number_format($p['price'], 2) ?> MAD</span>
                </div>
            </div>

            <div style="flex:1; display:flex; justify-content:center; align-items:center; z-index:1; transform:translateY(-20px) scale(0.95); opacity:0; transition:all 1s 0.1s cubic-bezier(0.25, 1, 0.5, 1);" class="slide-visual-anim">
                <div class="slider-scene-3d">
                    <div class="slider-book-3d">
                        <div class="slider-book-cover">
                            <img src="<?= e($p['image_url']) ?>" alt="<?= e($p['title']) ?>" style="width:100%; height:100%; object-fit:cover;">
                            <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(49,35,30,0.3), transparent); pointer-events:none;"></div>
                        </div>
                        <div class="slider-book-spine"></div>
                        <div class="slider-book-pages"></div>
                    </div>
                </div>
            </div>

        </div>
        <?php endforeach; ?>
    </div>

    
    <div class="slider-dots-container">
        <div style="display:flex; gap:12px;" id="sliderDots">
            <?php foreach(array_slice($heroProducts, 0, 4) as $idx => $p): ?>
                <button onclick="goToPremiumSlide(<?= $idx ?>)" class="premium-dot <?= $idx === 0 ? 'active' : '' ?>" aria-label="Slide <?= $idx + 1 ?>"></button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>

.premium-slide.active {
    opacity: 1 !important;
    visibility: visible !important;
}
.premium-slide.active .slide-content-anim {
    transform: translateY(0) !important;
    opacity: 1 !important;
}
.premium-slide.active .slide-visual-anim {
    transform: translateY(0) scale(1) !important;
    opacity: 1 !important;
}


.slider-scene-3d {
    perspective: 1200px;
    width: 100%;
    max-width: 290px;
    aspect-ratio: 1/1.4;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
}
.slider-book-3d {
    width: 100%;
    height: 100%;
    position: relative;
    transform-style: preserve-3d;
    transform: rotateY(-20deg) rotateX(10deg);
    transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1);
}
.slider-book-cover {
    position: absolute;
    inset: 0;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 12px 18px 36px rgba(0,0,0,0.22);
    backface-visibility: hidden;
    z-index: 2;
    transform: translateZ(10px);
}
.slider-book-spine {
    position: absolute;
    top: 0;
    bottom: 0;
    left: -10px;
    width: 20px;
    background: var(--primary);
    transform: rotateY(-90deg);
    transform-origin: right;
    z-index: 1;
    box-shadow: inset -3px 0 8px rgba(0,0,0,0.6);
}
.slider-book-pages {
    position: absolute;
    inset: 3px 0 3px 3px;
    background: var(--bg2);
    transform: translateZ(-10px);
    box-shadow: 5px 5px 15px rgba(0,0,0,0.15);
    border-radius: 0 4px 4px 0;
    border: 1.5px solid var(--border);
}


.premium-dot {
    width: 32px;
    height: 4px;
    border-radius: 4px;
    background: rgba(49, 35, 30, 0.15); 
    border: none;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}
.premium-dot:hover {
    background: rgba(49, 35, 30, 0.3);
}
.premium-dot.active {
    width: 48px;
    background: rgba(49, 35, 30, 0.15);
}
.premium-dot.active::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 0%;
    background: var(--primary); 
    border-radius: 4px;
    animation: sliderProgress 6s linear forwards;
}

@keyframes sliderProgress {
    0% { width: 0%; }
    100% { width: 100%; }
}

@media (max-width: 991px) {
    .premium-slide {
        flex-direction: column-reverse;
        justify-content: center;
        text-align: center;
        padding-top: 40px !important;
    }
    .slide-visual-anim {
        margin-bottom: 32px;
    }
    .slider-scene-3d {
        max-width: 200px !important;
    }
    .slide-content-anim {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const slider = document.getElementById('premiumSlider');
    if (slider) slider.style.opacity = '1';
    
    const slides = document.querySelectorAll('.premium-slide');
    const dots = document.querySelectorAll('.premium-dot');
    if (!slides.length) return; 
    
    let currentIdx = 0;
    let slideTimer;

    window.goToPremiumSlide = function(idx) {
        slides[currentIdx].classList.remove('active');
        dots[currentIdx].classList.remove('active');
        
        
        void dots[idx].offsetWidth;
        
        currentIdx = idx;
        slides[currentIdx].classList.add('active');
        dots[currentIdx].classList.add('active');
        
        resetTimer();
    };

    function nextSlide() {
        goToPremiumSlide((currentIdx + 1) % slides.length);
    }

    function resetTimer() {
        clearInterval(slideTimer);
        slideTimer = setInterval(nextSlide, 6000); 
    }

    resetTimer(); 

    
    document.querySelectorAll('.slider-scene-3d').forEach(scene => {
        const book = scene.querySelector('.slider-book-3d');
        scene.addEventListener('mousemove', (e) => {
            const rect = scene.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const xc = rect.width / 2;
            const yc = rect.height / 2;
            const dx = x - xc;
            const dy = y - yc;
            
            const rx = -(dy / yc) * 25; 
            const ry = (dx / xc) * 30 - 20; 
            
            book.style.transform = `rotateX(${rx}deg) rotateY(${ry}deg)`;
        });
        scene.addEventListener('mouseleave', () => {
            book.style.transform = 'rotateY(-20deg) rotateX(10deg)';
        });
    });
});
</script>



<section class="reveal" style="padding: 100px 40px; background:#fff; border-top:1px solid var(--border);">
    <div style="max-width:1200px; margin:0 auto;">
        
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:48px;">
            <div>
                <h2 style="font-size:32px; color:var(--ink); margin-bottom:8px;">Nouveautés</h2>
                <div style="font-size:15px; color:var(--ink-soft);">Les dernières pépites ajoutées à la base de données.</div>
            </div>
            <a href="catalogue.php" style="display:inline-flex; align-items:center; gap:6px; font-size:14px; font-weight:600; color:var(--primary); transition:opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                Voir tout <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(250px, 1fr)); gap:32px;">
            <?php foreach (array_slice($newReleases, 0, 4) as $p): include 'includes/product_card.php'; endforeach; ?>
        </div>
    </div>
</section>


<section class="reveal" style="padding: 0 40px 100px; background:#fff;">
    <div style="max-width:1200px; margin:0 auto;">
        
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:48px; padding-top:60px; border-top:1px solid var(--border);">
            <div>
                <h2 style="font-size:32px; color:var(--ink); margin-bottom:8px;">Les Plus Populaires</h2>
                <div style="font-size:15px; color:var(--ink-soft);">Les tomes qui s'arrachent le plus cette semaine.</div>
            </div>
            <a href="catalogue.php?sort=best" style="display:inline-flex; align-items:center; gap:6px; font-size:14px; font-weight:600; color:var(--primary); transition:opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                Voir le classement <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(250px, 1fr)); gap:32px;">
            <?php foreach (array_slice($bestSellers, 0, 4) as $p): include 'includes/product_card.php'; endforeach; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
