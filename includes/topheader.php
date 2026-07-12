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

    <!-- Heritage Loading Preloader -->
    <div id="preloader">
        <div class="preloader-wrap text-center" style="opacity: 0; transform: translateY(20px);">
            <h1 class="font-serif fw-bold mb-2" style="font-size: 3rem; color: var(--color-accent); letter-spacing: 0.05em; font-family: var(--font-serif) !important;">KUEHLEGACY</h1>
            <p class="font-mono text-muted tracking-wider text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.2em;">Melestarikan Warisan Rasa</p>
            <div class="font-mono mt-4 fw-bold preloader-percent" style="font-size: 1.5rem; color: var(--color-amber);">00%</div>
        </div>
        <script>
            // Fail-safe: hide preloader after 1.5 seconds if GSAP fails or stalls
            setTimeout(function() {
                var loader = document.getElementById('preloader');
                if (loader && loader.style.display !== 'none') {
                    loader.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                    loader.style.opacity = '0';
                    loader.style.transform = 'translateY(-100%)';
                    setTimeout(function() {
                        loader.style.display = 'none';
                    }, 400);
                }
            }, 1500);
        </script>
    </div>

    <!-- App Main Layout Container -->
    <div class="app-container"> <!-- Container #1 (Closed at bottom of pages) -->
        
        <!-- Sidebar Panel (Left) -->
        <aside class="sidebar-panel">
            <a href="<?php echo $root_path; ?>index.php" class="sidebar-logo">
                <img src="<?php echo $root_path; ?>sources/header/logofull.svg" alt="KuehLegacy Logo">
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