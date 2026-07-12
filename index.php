<?php
include('includes/topheader.php');
include('includes/bottomheader.php');
include('connection.php');

// Fetch recipes dynamically with fallbacks
$recipes_tg = [];
$recipes_jh = [];

if ($condb) {
    // Terengganu Recipes (Origin = 1)
    $tg_sql = "SELECT k.*, o.NAMESTATE as ORIGIN_NAME, COALESCE(u.NAME, a.NAME) as CREATOR_NAME, u.IMAGE as USER_IMAGE
               FROM kueh k
               LEFT JOIN origin o ON k.ORIGINID = o.ORIGINCODE
               LEFT JOIN users u ON k.USERNAME = u.USERNAME
               LEFT JOIN admin a ON k.USERNAME = a.USERNAME
               WHERE k.ORIGINID = 1
               LIMIT 4";
    $tg_result = mysqli_query($condb, $tg_sql);
    if ($tg_result) {
        while ($row = mysqli_fetch_assoc($tg_result)) {
            $recipes_tg[] = $row;
        }
    }

    // Johor Recipes (Origin = 2)
    $jh_sql = "SELECT k.*, o.NAMESTATE as ORIGIN_NAME, COALESCE(u.NAME, a.NAME) as CREATOR_NAME, u.IMAGE as USER_IMAGE
               FROM kueh k
               LEFT JOIN origin o ON k.ORIGINID = o.ORIGINCODE
               LEFT JOIN users u ON k.USERNAME = u.USERNAME
               LEFT JOIN admin a ON k.USERNAME = a.USERNAME
               WHERE k.ORIGINID = 2
               LIMIT 4";
    $jh_result = mysqli_query($condb, $jh_sql);
    if ($jh_result) {
        while ($row = mysqli_fetch_assoc($jh_result)) {
            $recipes_jh[] = $row;
        }
    }
}

// Fallback items if database doesn't have records yet
$fallback_tg = [
    ['KUEHID' => 1, 'KUEHNAME' => 'Chek Mek Molek', 'IMAGE' => 'sources/index/chekMekMolek.jpg', 'CREATOR_NAME' => 'Mak Kita'],
    ['KUEHID' => 2, 'KUEHNAME' => 'Kayu Keramat', 'IMAGE' => 'sources/index/kayuKeramat.jpg', 'CREATOR_NAME' => 'Mak Kita'],
    ['KUEHID' => 3, 'KUEHNAME' => 'Pulut Nyior', 'IMAGE' => 'sources/index/pulutNyior.jpg', 'CREATOR_NAME' => 'Mak Kita'],
    ['KUEHID' => 4, 'KUEHNAME' => 'Tok Haji Serban', 'IMAGE' => 'sources/index/tokHajiSerban.jpg', 'CREATOR_NAME' => 'Mak Kita']
];

$fallback_jh = [
    ['KUEHID' => 5, 'KUEHNAME' => 'Kuih Makmur', 'IMAGE' => 'sources/index/almond.jpg', 'CREATOR_NAME' => 'Cik Esah'],
    ['KUEHID' => 6, 'KUEHNAME' => 'Kuih Bangkit', 'IMAGE' => 'sources/index/kelapa.jpg', 'CREATOR_NAME' => 'Mak Uda'],
    ['KUEHID' => 7, 'KUEHNAME' => 'Karas Johor', 'IMAGE' => 'sources/index/gulamelaka.jpg', 'CREATOR_NAME' => 'Tok Nab'],
    ['KUEHID' => 8, 'KUEHNAME' => 'Kuih Talam Pandan', 'IMAGE' => 'sources/index/pandan.jpg', 'CREATOR_NAME' => 'Mak Long']
];

$display_tg = !empty($recipes_tg) ? $recipes_tg : $fallback_tg;
$display_jh = !empty($recipes_jh) ? $recipes_jh : $fallback_jh;
?>

<!-- Carousel Redesign (Hero Section) -->
<div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="sources/ramadan/1.png" class="d-block w-100" alt="Seni Warisan Kueh">
        </div>
        <div class="carousel-item">
            <img src="sources/ramadan/2.png" class="d-block w-100" alt="Resipi Tradisi Pilihan">
        </div>
        <div class="carousel-item">
            <img src="sources/ramadan/3.png" class="d-block w-100" alt="Perkongsian Rasa">
        </div>
        <div class="carousel-item">
            <img src="sources/ramadan/4.png" class="d-block w-100" alt="Ramadan Mubarak">
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Sebelum</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Seterusnya</span>
    </button>
</div>

<!-- Ingredients Bento Section -->
<div class="section-header">
    <span class="section-tagline">Bahan-Bahan Utama</span>
    <h2 class="section-title">Bahan Popular</h2>
</div>

<div class="bento-grid">
    <!-- Gula Melaka (Featured - Bento Col 2) -->
    <a href="recipes/kuehListing.php?search=melaka" class="double-bezel-outer bento-col-2">
        <div class="double-bezel-inner bento-card">
            <img src="sources/index/gulamelaka.jpg" alt="Gula Melaka" class="bento-card-bg">
            <div class="bento-card-overlay"></div>
            <div class="bento-card-content">
                <span class="bento-card-subtitle">Pemanis Tradisi</span>
                <h3 class="bento-card-title">Gula Melaka</h3>
            </div>
        </div>
    </a>

    <!-- Daun Pandan (Featured - Bento Col 2) -->
    <a href="recipes/kuehListing.php?search=pandan" class="double-bezel-outer bento-col-2">
        <div class="double-bezel-inner bento-card">
            <img src="sources/index/pandan.jpg" alt="Daun Pandan" class="bento-card-bg">
            <div class="bento-card-overlay"></div>
            <div class="bento-card-content">
                <span class="bento-card-subtitle">Aroma Asli</span>
                <h3 class="bento-card-title">Daun Pandan</h3>
            </div>
        </div>
    </a>

    <!-- Daun Pisang -->
    <a href="recipes/kuehListing.php?search=daun" class="double-bezel-outer">
        <div class="double-bezel-inner bento-card">
            <img src="sources/index/daunpisang.jpg" alt="Daun Pisang" class="bento-card-bg">
            <div class="bento-card-overlay"></div>
            <div class="bento-card-content">
                <span class="bento-card-subtitle">Pembungkus Alami</span>
                <h3 class="bento-card-title" style="font-size: 1.25rem !important;">Daun Pisang</h3>
            </div>
        </div>
    </a>

    <!-- Kacang Almond -->
    <a href="recipes/kuehListing.php?search=almond" class="double-bezel-outer">
        <div class="double-bezel-inner bento-card">
            <img src="sources/index/almond.jpg" alt="Kacang Almond" class="bento-card-bg">
            <div class="bento-card-overlay"></div>
            <div class="bento-card-content">
                <span class="bento-card-subtitle">Rasa Moden</span>
                <h3 class="bento-card-title" style="font-size: 1.25rem !important;">Kacang Almond</h3>
            </div>
        </div>
    </a>

    <!-- Kelapa Parut -->
    <a href="recipes/kuehListing.php?search=kelapa" class="double-bezel-outer">
        <div class="double-bezel-inner bento-card">
            <img src="sources/index/kelapa.jpg" alt="Kelapa Parut" class="bento-card-bg">
            <div class="bento-card-overlay"></div>
            <div class="bento-card-content">
                <span class="bento-card-subtitle">Lemak Manis</span>
                <h3 class="bento-card-title" style="font-size: 1.25rem !important;">Isi Kelapa</h3>
            </div>
        </div>
    </a>

    <!-- Ubi Kayu -->
    <a href="recipes/kuehListing.php?search=ubi" class="double-bezel-outer">
        <div class="double-bezel-inner bento-card">
            <img src="sources/index/ubi.jpg" alt="Ubi Kayu" class="bento-card-bg">
            <div class="bento-card-overlay"></div>
            <div class="bento-card-content">
                <span class="bento-card-subtitle">Makanan Ruji</span>
                <h3 class="bento-card-title" style="font-size: 1.25rem !important;">Ubi Kayu</h3>
            </div>
        </div>
    </a>
</div>

<hr class="custom-divider">

<!-- Terengganu Recipes Section -->
<div class="section-header">
    <span class="section-tagline">Pantai Timur</span>
    <h2 class="section-title">Resipi Tradisi Terengganu</h2>
</div>

<div class="recipes-grid">
    <?php foreach ($display_tg as $kueh): 
        $img = !empty($kueh['IMAGE']) ? $root_path . 'kueh_images/' . $kueh['IMAGE'] : $root_path . 'sources/default-kueh.jpg';
        if (!file_exists($img) && !empty($kueh['IMAGE'])) {
            $img = $kueh['IMAGE']; // Fallback directly to path if it is mock/custom
        }
    ?>
        <a href="recipes/kuehDetails.php?id=<?php echo $kueh['KUEHID']; ?>" class="double-bezel-outer">
            <div class="double-bezel-inner">
                <!-- Embossed Monogram Watermark -->
                <svg class="recipe-card-watermark" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M 28 20 L 36 20 L 36 23 L 33 23 L 33 77 L 36 77 L 36 80 L 28 80 L 28 77 L 31 77 L 31 23 L 28 23 Z" fill="currentColor"/>
                    <path d="M 33 50 C 37 43 47 31 73 24 C 71 29 67 34 62 37 C 65 35 69 33 72 30 C 69 35 64 40 56 44 C 60 42 63 40 66 37 C 61 44 54 49 45 52 C 48 50 51 48 53 45 C 47 51 40 55 33 56 Z" fill="currentColor"/>
                    <path d="M 33 53 C 41 59 50 67 58 75 C 60 77 63 78 66 78 C 66 76 64 74 62 72 C 54 65 45 57 37 49 Z" fill="currentColor"/>
                </svg>
                <div class="recipe-card-img-wrapper">
                    <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($kueh['KUEHNAME']); ?>" class="recipe-card-img">
                    <span class="recipe-card-tag">Terengganu</span>
                </div>
                <h4 class="recipe-card-title"><?php echo htmlspecialchars($kueh['KUEHNAME']); ?></h4>
                <div class="recipe-card-meta d-flex align-items-center gap-2">
                    <i class="bi bi-person text-success"></i>
                    <span>Oleh <?php echo htmlspecialchars($kueh['CREATOR_NAME']); ?></span>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<hr class="custom-divider">

<!-- Johor Recipes Section -->
<div class="section-header">
    <span class="section-tagline">Semenanjung Selatan</span>
    <h2 class="section-title">Resipi Tradisi Johor</h2>
</div>

<div class="recipes-grid">
    <?php foreach ($display_jh as $kueh): 
        $img = !empty($kueh['IMAGE']) ? $root_path . 'kueh_images/' . $kueh['IMAGE'] : $root_path . 'sources/default-kueh.jpg';
        if (!file_exists($img) && !empty($kueh['IMAGE'])) {
            $img = $kueh['IMAGE'];
        }
    ?>
        <a href="recipes/kuehDetails.php?id=<?php echo $kueh['KUEHID']; ?>" class="double-bezel-outer">
            <div class="double-bezel-inner">
                <!-- Embossed Monogram Watermark -->
                <svg class="recipe-card-watermark" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M 28 20 L 36 20 L 36 23 L 33 23 L 33 77 L 36 77 L 36 80 L 28 80 L 28 77 L 31 77 L 31 23 L 28 23 Z" fill="currentColor"/>
                    <path d="M 33 50 C 37 43 47 31 73 24 C 71 29 67 34 62 37 C 65 35 69 33 72 30 C 69 35 64 40 56 44 C 60 42 63 40 66 37 C 61 44 54 49 45 52 C 48 50 51 48 53 45 C 47 51 40 55 33 56 Z" fill="currentColor"/>
                    <path d="M 33 53 C 41 59 50 67 58 75 C 60 77 63 78 66 78 C 66 76 64 74 62 72 C 54 65 45 57 37 49 Z" fill="currentColor"/>
                </svg>
                <div class="recipe-card-img-wrapper">
                    <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($kueh['KUEHNAME']); ?>" class="recipe-card-img">
                    <span class="recipe-card-tag">Johor</span>
                </div>
                <h4 class="recipe-card-title"><?php echo htmlspecialchars($kueh['KUEHNAME']); ?></h4>
                <div class="recipe-card-meta d-flex align-items-center gap-2">
                    <i class="bi bi-person text-success"></i>
                    <span>Oleh <?php echo htmlspecialchars($kueh['CREATOR_NAME']); ?></span>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<?php
include('includes/footer.php');
include('includes/popup.php');
?>

<!-- Outer Layout Closing Tags -->
</main> <!-- content-body -->
</div> <!-- content-wrapper-compat-4 -->
</div> <!-- content-wrapper-compat-3 -->
</div> <!-- main-layout -->
</div> <!-- app-container -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>