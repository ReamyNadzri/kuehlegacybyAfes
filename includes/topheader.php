<?PHP
session_start();
date_default_timezone_set("Asia/Kuala_Lumpur");

// Determine root path dynamically
$root_path = "";
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
if (in_array($current_dir, ['auth', 'recipes', 'favorites', 'admin'])) {
    $root_path = "../";
}
?>
<!DOCTYPE html>
<html lang="ms" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title>KuehLegacy | Resipi Warisan Malaysia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- External Frameworks and Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- GSAP & ScrollTrigger for Award-Winning Motion Design -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    
    <!-- Premium Custom Stylesheet -->
    <link rel="stylesheet" href="<?php echo $root_path; ?>style.css">
</head>

<body>

    <!-- Heritage Loading Preloader — Progress-Driven Flipping Book Frames -->
    <div id="preloader" style="position: fixed; inset: 0; background-color: #ffffff !important; z-index: 10000; display: flex; flex-direction: column; align-items: center; justify-content: center; overflow: hidden; will-change: opacity, transform;">
        <div class="preloader-wrap text-center" style="opacity: 0; transform: translateY(20px);">
            <!-- Video Frames Canvas (borderless, blended background) -->
            <div style="width: 360px; height: 203px; margin: 0 auto 1.5rem auto; position: relative; overflow: hidden; background: transparent;">
                <canvas id="preloader-canvas" width="360" height="203" style="display: block; width: 100%; height: 100%; object-fit: cover;"></canvas>
            </div>
            
            <h1 class="font-serif fw-bold mb-2" style="font-size: 2.8rem; color: var(--color-text) !important; letter-spacing: -0.02em; font-family: var(--font-serif) !important;">KuehLegacy</h1>
            <p class="font-mono text-muted tracking-wider text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.25em; color: var(--color-text-muted) !important;">Preserving the Sweet Heritage</p>
            <!-- Progress bar track -->
            <div class="preloader-bar-track" style="width: 200px; height: 3px; background: var(--color-border); border-radius: 100px; margin: 1.25rem auto 0; overflow: hidden;">
                <div id="preloader-bar-fill" style="width: 0%; height: 100%; background: var(--color-accent); border-radius: 100px; transition: width 0.3s ease;"></div>
            </div>
            <div class="font-mono mt-3 fw-bold preloader-percent" id="preloader-percent" style="font-size: 1.5rem; color: var(--color-amber);">0%</div>
        </div>
        
        <script>
            /* ─────────────────────────────────────────────────────
               PROGRESS-DRIVEN FRAME ANIMATION PRELOADER (192 FRAMES)
               Preloads 192 JPEG frames (padded to 5 digits) and draws
               the corresponding frame to canvas based on page load progress.
               Enforces a minimum duration to guarantee smooth visibility.
               ───────────────────────────────────────────────────── */

            (function initVideoFramePreloader() {
                const canvas = document.getElementById('preloader-canvas');
                const preloader = document.getElementById('preloader');
                if (!canvas || !preloader) return;

                // Check if preloader has already run in this session
                if (sessionStorage.getItem('preloaderRun')) {
                    preloader.style.display = 'none';
                    return;
                }
                sessionStorage.setItem('preloaderRun', 'true');

                const ctx = canvas.getContext('2d');

                const totalFrames = 192;
                const rootPath = '<?php echo $root_path; ?>';
                const images = [];
                let preloadedFrames = 0;
                const startTime = Date.now();

                const loadState = {
                    currentProgress: 0,   // smoothed progress 0 -> 1
                    targetProgress: 0,    // target progress 0 -> 1
                    loaded: 0,
                    total: 0,
                    done: false,
                    dismissed: false
                };

                // Gather other page resources (images, scripts, styles)
                function countResources() {
                    const imgs = Array.from(document.querySelectorAll('img')).filter(img => !img.src.includes('book_frames'));
                    const scripts = document.querySelectorAll('script[src]');
                    const links = document.querySelectorAll('link[rel="stylesheet"]');
                    
                    loadState.total = imgs.length + scripts.length + links.length + totalFrames;
                    if (loadState.total === 0) loadState.total = 1;

                    imgs.forEach(img => {
                         if (img.complete) { bump(); return; }
                         img.addEventListener('load', bump, { once: true });
                         img.addEventListener('error', bump, { once: true });
                    });

                    scripts.forEach(() => bump());
                    links.forEach(link => {
                         if (link.sheet) { bump(); return; }
                         link.addEventListener('load', bump, { once: true });
                         link.addEventListener('error', bump, { once: true });
                    });
                }

                function bump() {
                    loadState.loaded++;
                    loadState.targetProgress = Math.min(loadState.loaded / loadState.total, 1);
                }

                window.addEventListener('load', function() {
                    loadState.targetProgress = 1;
                    loadState.done = true;
                });

                setTimeout(function() {
                    if (!loadState.done) {
                        loadState.targetProgress = 1;
                        loadState.done = true;
                    }
                }, 9000);

                // Start resource counting
                countResources();

                const percentEl = document.getElementById('preloader-percent');
                const barFill   = document.getElementById('preloader-bar-fill');

                let lastDrawnFrameIndex = 0;

                function drawFrame(index) {
                    let img = images[index];
                    if (img && img.complete && img.naturalWidth !== 0) {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        const cropY = Math.round(img.naturalHeight * 0.03); // Crop 3% off top/bottom to hide black bars
                        const cropHeight = img.naturalHeight - (cropY * 2);
                        ctx.drawImage(img, 0, cropY, img.naturalWidth, cropHeight, 0, 0, canvas.width, canvas.height);
                        lastDrawnFrameIndex = index;
                    } else {
                        // Fallback to the last successfully drawn frame to avoid flash-to-start stutter
                        let fallbackImg = images[lastDrawnFrameIndex];
                        if (fallbackImg && fallbackImg.complete && fallbackImg.naturalWidth !== 0) {
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                            const cropY = Math.round(fallbackImg.naturalHeight * 0.03);
                            const cropHeight = fallbackImg.naturalHeight - (cropY * 2);
                            ctx.drawImage(fallbackImg, 0, cropY, fallbackImg.naturalWidth, cropHeight, 0, 0, canvas.width, canvas.height);
                        }
                    }
                }

                // Preload first frame, then start batch loading the rest
                const firstImg = new Image();
                firstImg.src = `${rootPath}sources/book_frames/frame_00001.jpg`;
                firstImg.onload = () => {
                    images[0] = firstImg;
                    preloadedFrames++;
                    bump();
                    
                    // Draw first frame immediately
                    drawFrame(0);
                    
                    // Fade-in preloader contents
                    const wrap = document.querySelector('.preloader-wrap');
                    if (wrap && typeof gsap !== 'undefined') {
                        gsap.to(wrap, { opacity: 1, y: 0, duration: 0.4, ease: "power2.out" });
                    } else if (wrap) {
                        wrap.style.opacity = '1';
                        wrap.style.transform = 'translateY(0)';
                    }
                    
                    // Start loop
                    requestAnimationFrame(update);
                    
                    // Start batch loading remaining frames with a 250ms delay to prevent initial main thread stutter
                    setTimeout(loadRemainingFramesInBatches, 250);
                };
                firstImg.onerror = () => {
                    preloadedFrames++;
                    bump();
                    requestAnimationFrame(update);
                    setTimeout(loadRemainingFramesInBatches, 250);
                };

                function loadRemainingFramesInBatches() {
                    const batchSize = 15;
                    let currentFrame = 2;
                    
                    function nextBatch() {
                        if (currentFrame > totalFrames) return;
                        let loadedInBatch = 0;
                        const limit = Math.min(currentFrame + batchSize - 1, totalFrames);
                        const count = limit - currentFrame + 1;
                        
                        for (let i = currentFrame; i <= limit; i++) {
                            const img = new Image();
                            const frameNum = String(i).padStart(5, '0');
                            img.src = `${rootPath}sources/book_frames/frame_${frameNum}.jpg`;
                            img.onload = () => {
                                images[i - 1] = img;
                                preloadedFrames++;
                                bump();
                                loadedInBatch++;
                                if (loadedInBatch === count) {
                                    currentFrame += batchSize;
                                    nextBatch();
                                }
                             };
                             img.onerror = () => {
                                 preloadedFrames++;
                                 bump();
                                 loadedInBatch++;
                                 if (loadedInBatch === count) {
                                     currentFrame += batchSize;
                                     nextBatch();
                                 }
                             };
                        }
                    }
                    nextBatch();
                }

                // Animation & progress updates loop
                function update() {
                    if (loadState.dismissed) return;

                    const elapsed = (Date.now() - startTime) / 1000;
                    const minDuration = 3.0;
                    const timeProgress = Math.min(elapsed / minDuration, 1);
                    const cappedProgress = Math.min(loadState.targetProgress, timeProgress);

                    loadState.currentProgress += (cappedProgress - loadState.currentProgress) * 0.08;

                    if (loadState.currentProgress > 0.998) {
                        loadState.currentProgress = 1;
                    }

                    const frameIndex = Math.floor(loadState.currentProgress * (totalFrames - 1));
                    drawFrame(frameIndex);

                    const pct = Math.round(loadState.currentProgress * 100);
                    if (percentEl) percentEl.textContent = pct + '%';
                    if (barFill)   barFill.style.width = pct + '%';

                    if (loadState.currentProgress >= 1) {
                        loadState.dismissed = true;
                        setTimeout(dismissPreloader, 600);
                        return;
                    }

                    requestAnimationFrame(update);
                }

                function dismissPreloader() {
                    const preloader = document.getElementById('preloader');
                    if (!preloader) return;

                    if (typeof gsap !== 'undefined') {
                        gsap.to(preloader, {
                            opacity: 0,
                            y: -60,
                            duration: 0.45,
                            ease: "power2.out",
                            onComplete: () => {
                                preloader.style.display = 'none';
                                if (typeof ScrollTrigger !== 'undefined') {
                                    ScrollTrigger.refresh();
                                }
                            }
                        });
                    } else {
                        preloader.style.transition = 'opacity 0.45s ease, transform 0.45s ease';
                        preloader.style.opacity = '0';
                        preloader.style.transform = 'translateY(-60px)';
                        setTimeout(() => {
                            preloader.style.display = 'none';
                        }, 450);
                    }
                }
            })();
        </script>
    </div>

    <!-- App Main Layout Container -->
    <div class="app-container"> <!-- Container #1 (Closed at bottom of pages) -->
        
        <!-- Sidebar Panel (Left) -->
        <aside class="sidebar-panel">
            <a href="<?php echo $root_path; ?>index.php" class="brand-logo-wrap mb-4">
                <svg class="brand-monogram" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20,16 H48 V22 H41 V78 H48 V84 H20 V78 H27 V22 H20 Z" fill="var(--color-text)" />
                    <path d="M41,45 C41,45 45,39 52,32 C50,34 47,38 46,42 C51,36 58,30 65,24 C61,28 57,33 55,39 C61,32 69,26 77,20 C71,26 65,33 62,41 C69,32 79,25 88,18 C79,30 69,42 58,51 C52,56 46,55 41,51 Z" fill="var(--color-accent)" />
                    <path d="M41,51 C48,57 56,66 64,75 C68,79 73,80 77,80 C77,77 73,74 69,70 C60,62 50,54 41,46 Z" fill="var(--color-text)" />
                </svg>
                <span class="brand-logo-text">KuehLegacy</span>
            </a>
            
            <hr class="sidebar-divider">
            
            <nav class="sidebar-nav">
                <a href="<?php echo $root_path; ?>index.php" class="sidebar-item <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                    <i class="bi bi-house"></i>
                    <span>Home</span>
                </a>
                
                <a href="<?php echo $root_path; ?>recipes/kuehListing.php?search=" class="sidebar-item <?php echo (basename($_SERVER['PHP_SELF']) == 'kuehListing.php' && (!isset($_GET['search']) || $_GET['search'] === '')) ? 'active' : ''; ?>">
                    <i class="bi bi-search"></i>
                    <span>Carian Resipi</span>
                </a>
                
                <?php if (!empty($_SESSION['username'])): ?>
                    <a href="<?php echo $root_path; ?>auth/userProfile.php" class="sidebar-item <?php echo (basename($_SERVER['PHP_SELF']) == 'userProfile.php') ? 'active' : ''; ?>">
                        <i class="bi bi-journal-text"></i>
                        <span>Resipi Anda</span>
                    </a>
                    <a href="<?php echo $root_path; ?>favorites/favorite.php" class="sidebar-item <?php echo (basename($_SERVER['PHP_SELF']) == 'favorite.php') ? 'active' : ''; ?>">
                        <i class="bi bi-bookmark-heart"></i>
                        <span>Kegemaran</span>
                    </a>
                <?php else: ?>
                    <div class="sidebar-divider"></div>
                    <p class="w3-small px-3" style="color: var(--color-text-muted); font-size: 0.8rem; line-height: 1.4;">
                        Daftar masuk untuk mula berkongsi resipi warisan anda.
                    </p>
                    <a href="<?php echo $root_path; ?>auth/userLogin.php" class="sidebar-item">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Log Masuk / Daftar</span>
                    </a>
                <?php endif; ?>
                
                <div class="sidebar-divider"></div>
                
                <div class="sidebar-section-title">Pilih Wilayah</div>
                <div class="sidebar-states-grid">
                    <a href="<?php echo $root_path; ?>recipes/kuehListing.php?search=johor" class="state-pill">
                        <img src="<?php echo $root_path; ?>sources/negeri/johor.png" alt="Johor">
                        <span>Johor</span>
                    </a>
                    <a href="<?php echo $root_path; ?>recipes/kuehListing.php?search=kedah" class="state-pill">
                        <img src="<?php echo $root_path; ?>sources/negeri/kedah.png" alt="Kedah">
                        <span>Kedah</span>
                    </a>
                    <a href="<?php echo $root_path; ?>recipes/kuehListing.php?search=kelantan" class="state-pill">
                        <img src="<?php echo $root_path; ?>sources/negeri/kelantan.png" alt="Kelantan">
                        <span>Kelantan</span>
                    </a>
                    <a href="<?php echo $root_path; ?>recipes/kuehListing.php?search=terengganu" class="state-pill">
                        <img src="<?php echo $root_path; ?>sources/negeri/terengganu.png" alt="Terengganu">
                        <span>Terengganu</span>
                    </a>
                    <a href="<?php echo $root_path; ?>recipes/kuehListing.php?search=melaka" class="state-pill">
                        <img src="<?php echo $root_path; ?>sources/negeri/melaka.png" alt="Melaka">
                        <span>Melaka</span>
                    </a>
                    <a href="<?php echo $root_path; ?>recipes/kuehListing.php?search=sembilan" class="state-pill">
                        <img src="<?php echo $root_path; ?>sources/negeri/sembilan.png" alt="Negeri Sembilan">
                        <span>Negeri Sembilan</span>
                    </a>
                    <a href="<?php echo $root_path; ?>recipes/kuehListing.php?search=pahang" class="state-pill">
                        <img src="<?php echo $root_path; ?>sources/negeri/pahang.png" alt="Pahang">
                        <span>Pahang</span>
                    </a>
                    <a href="<?php echo $root_path; ?>recipes/kuehListing.php?search=perak" class="state-pill">
                        <img src="<?php echo $root_path; ?>sources/negeri/perak.png" alt="Perak">
                        <span>Perak</span>
                    </a>
                    <a href="<?php echo $root_path; ?>recipes/kuehListing.php?search=perlis" class="state-pill">
                        <img src="<?php echo $root_path; ?>sources/negeri/perlis.png" alt="Perlis">
                        <span>Perlis</span>
                    </a>
                    <a href="<?php echo $root_path; ?>recipes/kuehListing.php?search=penang" class="state-pill">
                        <img src="<?php echo $root_path; ?>sources/negeri/penang.png" alt="Pulau Pinang">
                        <span>Pulau Pinang</span>
                    </a>
                    <a href="<?php echo $root_path; ?>recipes/kuehListing.php?search=selangor" class="state-pill">
                        <img src="<?php echo $root_path; ?>sources/negeri/kl dan selangor.png" alt="Kuala Lumpur & Selangor">
                        <span>Kuala Lumpur & Selangor</span>
                    </a>
                    <a href="<?php echo $root_path; ?>recipes/kuehListing.php?search=sabah" class="state-pill">
                        <img src="<?php echo $root_path; ?>sources/negeri/sabah.png" alt="Sabah">
                        <span>Sabah</span>
                    </a>
                    <a href="<?php echo $root_path; ?>recipes/kuehListing.php?search=sarawak" class="state-pill">
                        <img src="<?php echo $root_path; ?>sources/negeri/sarawak.png" alt="Sarawak">
                        <span>Sarawak</span>
                    </a>
                    <a href="<?php echo $root_path; ?>recipes/kuehListing.php?search=labuan" class="state-pill">
                        <img src="<?php echo $root_path; ?>sources/negeri/labuan.png" alt="Labuan">
                        <span>Labuan</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Layout (Right) -->
        <div class="main-layout"> <!-- Container #2 (Closed at bottom of pages) -->
            
            <!-- Top Nav Header -->
            <header class="top-nav">
                <!-- Mobile Logo and Toggle -->
                <div class="d-lg-none d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-secondary rounded-pill" onclick="document.querySelector('.sidebar-panel').classList.toggle('open')">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <span class="font-serif fw-bold" style="font-family: var(--font-serif) !important;">KuehLegacy</span>
                </div>
                
                <div></div> <!-- Spacer to align nav actions to the right on desktop -->