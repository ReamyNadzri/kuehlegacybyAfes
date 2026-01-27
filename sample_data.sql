-- Sample Data for KuehLegacy System
-- Traditional Malaysian Kuih Recipes
-- Run this after creating the database schema

USE kuehlegacy;

-- ========================================
-- 1. FOODTYPE (Categories of Kuih)
-- ========================================
INSERT INTO foodtype (FOODTYPECODE, TYPENAME) VALUES
(1, 'Kuih Tepung Beras (Rice Flour Based)'),
(2, 'Kuih Tepung Gandum (Wheat Flour Based)'),
(3, 'Kuih Pulut (Glutinous Rice Based)'),
(4, 'Kuih Goreng (Fried Kuih)'),
(5, 'Kuih Kukus (Steamed Kuih)'),
(6, 'Kuih Bakar (Baked Kuih)'),
(7, 'Kuih Berbentuk (Molded Kuih)'),
(8, 'Kuih Tradisional (Traditional Festive)');

-- ========================================
-- 2. METHOD (Cooking Methods)
-- ========================================
INSERT INTO method (METHODID, METHODNAME) VALUES
(1, 'Kukus (Steaming)'),
(2, 'Goreng (Deep Frying)'),
(3, 'Bakar (Baking/Grilling)'),
(4, 'Rebus (Boiling)'),
(5, 'Panggang (Roasting)'),
(6, 'Celup (Coating/Dipping)');

-- ========================================
-- 3. ORIGIN (Malaysian States)
-- ========================================
INSERT INTO origin (ORIGINCODE, NAMESTATE) VALUES
(1, 'Johor'),
(2, 'Kedah'),
(3, 'Kelantan'),
(4, 'Melaka'),
(5, 'Negeri Sembilan'),
(6, 'Pahang'),
(7, 'Pulau Pinang'),
(8, 'Perak'),
(9, 'Perlis'),
(10, 'Selangor'),
(11, 'Terengganu'),
(12, 'Sabah'),
(13, 'Sarawak'),
(14, 'Kuala Lumpur'),
(15, 'Labuan'),
(16, 'Putrajaya');

-- ========================================
-- 4. POPULARITY (Rating Levels)
-- ========================================
INSERT INTO popularity (POPULARID, LEVELSTAR, RATING) VALUES
(1, '⭐⭐⭐⭐⭐', 5),
(2, '⭐⭐⭐⭐', 4),
(3, '⭐⭐⭐', 3),
(4, '⭐⭐', 2),
(5, '⭐', 1);

-- ========================================
-- 5. ADMIN ACCOUNT (Default Admin)
-- ========================================
INSERT INTO admin (USERNAME, NAME, PASSWORD, EMAIL, IMAGE) VALUES
('admin', 'System Administrator', 'admin123', 'admin@kuehlegacy.com', NULL);

-- ========================================
-- 6. KUEH RECIPES (Traditional Malaysian Kuih)
-- ========================================

-- Recipe 1: Kuih Lapis (Layered Cake)
INSERT INTO kueh (KUEHID, KUEHNAME, KUEHDESC, FOODTYPECODE, METHODID, POPULARID, ORIGINID, VIDEO, IMAGE, USERNAME) VALUES
(1, 'Kuih Lapis', 
'Kuih Lapis adalah sejenis kuih tradisional berbilang lapisan yang sangat popular di Malaysia. Setiap lapisan dibuat daripada tepung beras, santan dan gula, kemudian dikukus satu persatu sehingga membentuk lapisan berwarna-warni yang cantik. Teksturnya yang kenyal dan manis menjadikannya hidangan istimewa dalam majlis-majlis keraian.',
1, 1, 1, 4, 'https://www.youtube.com/watch?v=example1', 'kuih_lapis.jpg', 'admin');

-- Recipe 2: Kuih Seri Muka
INSERT INTO kueh (KUEHID, KUEHNAME, KUEHDESC, FOODTYPECODE, METHODID, POPULARID, ORIGINID, VIDEO, IMAGE, USERNAME) VALUES
(2, 'Kuih Seri Muka',
'Kuih Seri Muka atau Kuih Salat terdiri daripada dua lapisan - lapisan bawah pulut putih dan lapisan atas custard pandan berwarna hijau. Nama "seri muka" bermaksud "wajah cantik" kerana lapisan pandan yang licin dan menawan. Gabungan pulut yang wangi dengan custard pandan yang lembut menghasilkan rasa yang sempurna.',
3, 1, 1, 10, 'https://www.youtube.com/watch?v=example2', 'seri_muka.jpg', 'admin');

-- Recipe 3: Onde-Onde
INSERT INTO kueh (KUEHID, KUEHNAME, KUEHDESC, FOODTYPECODE, METHODID, POPULARID, ORIGINID, VIDEO, IMAGE, USERNAME) VALUES
(3, 'Onde-Onde',
'Onde-Onde adalah kuih tradisional berbentuk bulat berwarna hijau daripada jus pandan. Diisi dengan gula melaka cair di tengahnya dan disalut dengan kelapa parut putih. Apabila digigit, gula melaka yang manis akan meleleh di dalam mulut memberikan pengalaman yang unik dan sedap.',
1, 4, 1, 1, 'https://www.youtube.com/watch?v=example3', 'onde_onde.jpg', 'admin');

-- Recipe 4: Kuih Bahulu
INSERT INTO kueh (KUEHID, KUEHNAME, KUEHDESC, FOODTYPECODE, METHODID, POPULARID, ORIGINID, VIDEO, IMAGE, USERNAME) VALUES
(4, 'Kuih Bahulu',
'Kuih Bahulu adalah kek span tradisional Melayu yang dibuat dalam acuan khas berbentuk bunga. Teksturnya yang lembut dan gebu dengan rasa mentega dan vanilla yang harum menjadikannya popular terutama semasa perayaan Hari Raya. Bahulu yang baik mestilah naik mengembang dengan permukaan yang rata.',
2, 3, 1, 1, 'https://www.youtube.com/watch?v=example4', 'bahulu.jpg', 'admin');

-- Recipe 5: Kuih Ketayap
INSERT INTO kueh (KUEHID, KUEHNAME, KUEHDESC, FOODTYPECODE, METHODID, POPULARID, ORIGINID, VIDEO, IMAGE, USERNAME) VALUES
(5, 'Kuih Ketayap',
'Kuih Ketayap atau Kuih Dadar adalah pancake nipis berwarna hijau pandan yang diisi dengan kelapa parut bergula dan digulung. Kombinasi daun pandan yang wangi dengan kelapa manis memberikan rasa tropika yang autentik. Cara menggulung yang kemas menunjukkan kemahiran tukang masak.',
1, 3, 1, 3, 'https://www.youtube.com/watch?v=example5', 'ketayap.jpg', 'admin');

-- Recipe 6: Kuih Kapit
INSERT INTO kueh (KUEHID, KUEHNAME, KUEHDESC, FOODTYPECODE, METHODID, POPULARID, ORIGINID, VIDEO, IMAGE, USERNAME) VALUES
(6, 'Kuih Kapit',
'Kuih Kapit atau Love Letters adalah sejenis kuih rangup nipis yang dibuat menggunakan acuan besi panas. Adunan santan dan gula dibakar di atas api sehingga garing, kemudian digulung atau dilipat semasa panas. Kuih ini adalah wajib dalam perayaan Tahun Baru Cina dan Hari Raya.',
2, 3, 2, 4, 'https://www.youtube.com/watch?v=example6', 'kapit.jpg', 'admin');

-- Recipe 7: Pulut Inti
INSERT INTO kueh (KUEHID, KUEHNAME, KUEHDESC, FOODTYPECODE, METHODID, POPULARID, ORIGINID, VIDEO, IMAGE, USERNAME) VALUES
(7, 'Pulut Inti',
'Pulut Inti terdiri daripada pulut kuning bersantan yang ditutup dengan serawa kelapa bergula melaka. Warna kuning daripada kunyit dan aroma pandan menjadikan kuih ini sangat menarik. Perpaduan pulut yang wangi dengan kelapa manis adalah kombinasi klasik dalam masakan Melayu.',
3, 1, 1, 10, 'https://www.youtube.com/watch?v=example7', 'pulut_inti.jpg', 'admin');

-- Recipe 8: Kuih Talam
INSERT INTO kueh (KUEHID, KUEHNAME, KUEHDESC, FOODTYPECODE, METHODID, POPULARID, ORIGINID, VIDEO, IMAGE, USERNAME) VALUES
(8, 'Kuih Talam',
'Kuih Talam terdiri daripada dua lapisan - lapisan bawah berwarna hijau pandan dan lapisan atas putih santan. Tekstur lapisan bawah yang kenyal bergabung dengan lapisan atas yang lembut creamy menghasilkan kontras yang menarik. Nama "talam" merujuk kepada dulang yang digunakan untuk mengukus kuih ini.',
1, 1, 1, 8, 'https://www.youtube.com/watch?v=example8', 'talam.jpg', 'admin');

-- Recipe 9: Kuih Cara
INSERT INTO kueh (KUEHID, KUEHNAME, KUEHDESC, FOODTYPECODE, METHODID, POPULARID, ORIGINID, VIDEO, IMAGE, USERNAME) VALUES
(9, 'Kuih Cara Manis',
'Kuih Cara adalah kuih comel berbentuk bunga yang dimasak dalam acuan khas. Dibuat daripada tepung beras, santan dan gula, teksturnya lembut dengan bahagian tepi yang rangup. Biasanya dihias dengan bijan putih di atas. Kuih ini sangat popular dalam majlis-majlis tradisional.',
1, 3, 2, 7, 'https://www.youtube.com/watch?v=example9', 'cara.jpg', 'admin');

-- Recipe 10: Kuih Bingka Ubi
INSERT INTO kueh (KUEHID, KUEHNAME, KUEHDESC, FOODTYPECODE, METHODID, POPULARID, ORIGINID, VIDEO, IMAGE, USERNAME) VALUES
(10, 'Kuih Bingka Ubi Kayu',
'Kuih Bingka Ubi adalah kuih tradisional yang diperbuat daripada ubi kayu parut, santan, gula dan telur. Dibakar sehingga permukaannya berwarna keemasan dengan bahagian dalam yang lembut dan moist. Aroma pandan dan vanila memberikan bau yang harum. Sangat popular di negeri-negeri utara.',
1, 3, 1, 2, 'https://www.youtube.com/watch?v=example10', 'bingka_ubi.jpg', 'admin');

-- ========================================
-- 7. ITEMS (Ingredients for each recipe)
-- ========================================

-- Ingredients for Kuih Lapis
INSERT INTO items (KUEHID, NAMEITEM) VALUES
(1, 'Tepung Beras - 500g'),
(1, 'Tepung Ubi Kayu - 100g'),
(1, 'Santan Pekat - 400ml'),
(1, 'Air Pandan - 200ml'),
(1, 'Gula Pasir - 300g'),
(1, 'Garam - 1/4 sudu teh'),
(1, 'Pewarna Makanan (merah, hijau, kuning)');

-- Ingredients for Kuih Seri Muka
INSERT INTO items (KUEHID, NAMEITEM) VALUES
(2, 'Pulut Beras - 400g'),
(2, 'Santan - 500ml'),
(2, 'Garam - 1 sudu teh'),
(2, 'Telur - 3 biji'),
(2, 'Gula Pasir - 200g'),
(2, 'Tepung Jagung - 3 sudu besar'),
(2, 'Air Pandan - 150ml'),
(2, 'Daun Pandan - 5 helai');

-- Ingredients for Onde-Onde
INSERT INTO items (KUEHID, NAMEITEM) VALUES
(3, 'Tepung Pulut - 250g'),
(3, 'Ubi Kentang - 100g (direbus dan dilenyek)'),
(3, 'Air Pandan - 150ml'),
(3, 'Garam - 1/4 sudu teh'),
(3, 'Gula Melaka - 150g (dipotong dadu kecil)'),
(3, 'Kelapa Parut Putih - 200g'),
(3, 'Garam untuk kelapa - sedikit');

-- Ingredients for Kuih Bahulu
INSERT INTO items (KUEHID, NAMEITEM) VALUES
(4, 'Telur - 5 biji'),
(4, 'Gula Kastor - 150g'),
(4, 'Tepung Superfine - 150g'),
(4, 'Esen Vanila - 1 sudu teh'),
(4, 'Serbuk Penaik - 1/2 sudu teh'),
(4, 'Mentega Cair - 2 sudu besar');

-- Ingredients for Kuih Ketayap
INSERT INTO items (KUEHID, NAMEITEM) VALUES
(5, 'Tepung Gandum - 200g'),
(5, 'Telur - 2 biji'),
(5, 'Santan - 300ml'),
(5, 'Air Pandan - 100ml'),
(5, 'Garam - sedikit'),
(5, 'Kelapa Parut - 300g'),
(5, 'Gula Melaka - 200g'),
(5, 'Daun Pandan - 3 helai');

-- Ingredients for Kuih Kapit
INSERT INTO items (KUEHID, NAMEITEM) VALUES
(6, 'Santan Pekat - 500ml'),
(6, 'Gula Pasir - 400g'),
(6, 'Tepung Gandum - 250g'),
(6, 'Tepung Beras - 50g'),
(6, 'Telur - 4 biji'),
(6, 'Garam - 1/4 sudu teh');

-- Ingredients for Pulut Inti
INSERT INTO items (KUEHID, NAMEITEM) VALUES
(7, 'Beras Pulut - 400g'),
(7, 'Santan - 400ml'),
(7, 'Air Pandan - 100ml'),
(7, 'Kunyit Hidup - 2cm'),
(7, 'Garam - 1 sudu teh'),
(7, 'Kelapa Parut - 300g'),
(7, 'Gula Melaka - 250g'),
(7, 'Daun Pandan - 4 helai');

-- Ingredients for Kuih Talam
INSERT INTO items (KUEHID, NAMEITEM) VALUES
(8, 'Tepung Beras - 200g'),
(8, 'Tepung Ubi Kayu - 100g'),
(8, 'Santan - 600ml'),
(8, 'Air Pandan - 250ml'),
(8, 'Gula Pasir - 200g'),
(8, 'Garam - 1 sudu teh'),
(8, 'Tepung Gandum - 2 sudu besar');

-- Ingredients for Kuih Cara
INSERT INTO items (KUEHID, NAMEITEM) VALUES
(9, 'Tepung Beras - 250g'),
(9, 'Santan - 300ml'),
(9, 'Air - 100ml'),
(9, 'Gula Pasir - 200g'),
(9, 'Telur - 2 biji'),
(9, 'Serbuk Penaik - 1/2 sudu teh'),
(9, 'Bijan Putih - untuk hiasan');

-- Ingredients for Kuih Bingka Ubi
INSERT INTO items (KUEHID, NAMEITEM) VALUES
(10, 'Ubi Kayu Parut - 600g'),
(10, 'Santan Pekat - 400ml'),
(10, 'Gula Pasir - 300g'),
(10, 'Telur - 4 biji'),
(10, 'Mentega Cair - 100g'),
(10, 'Esen Vanila - 1 sudu teh'),
(10, 'Daun Pandan - 3 helai'),
(10, 'Garam - 1/2 sudu teh');

-- ========================================
-- 8. STEPS (Cooking Instructions)
-- ========================================

-- Steps for Kuih Lapis
INSERT INTO steps (KUEHID, STEP) VALUES
(1, 'Campurkan tepung beras, tepung ubi kayu dan garam. Gaul rata.'),
(1, 'Masukkan santan dan air pandan secara beransur-ansur sambil dikacau sehingga sebati.'),
(1, 'Masukkan gula dan kacau hingga larut. Tapis adunan.'),
(1, 'Bahagikan adunan kepada beberapa bahagian dan masukkan pewarna mengikut citarasa.'),
(1, 'Sapukan minyak pada loyang. Tuang satu lapisan adunan dan kukus 3-5 minit hingga masak.'),
(1, 'Ulang proses dengan warna berbeza sehingga adunan habis. Pastikan setiap lapisan masak dahulu.'),
(1, 'Kukus lapisan terakhir selama 10 minit. Sejukkan dan potong kepada bentuk yang dikehendaki.');

-- Steps for Kuih Seri Muka
INSERT INTO steps (KUEHID, STEP) VALUES
(2, 'Rendam beras pulut semalaman. Toskan dan kukus bersama santan dan garam selama 20 minit.'),
(2, 'Padatkan pulut dalam loyang yang telah disapu minyak. Ketepikan.'),
(2, 'Pukul telur bersama gula hingga sebati. Masukkan tepung jagung.'),
(2, 'Masak santan dengan daun pandan. Tuangkan ke dalam campuran telur sambil dikacau.'),
(2, 'Masak atas api sederhana sambil dikacau sehingga pekat. Masukkan air pandan.'),
(2, 'Tuangkan adunan pandan di atas pulut. Ratakan dan kukus selama 30 minit.'),
(2, 'Sejukkan sepenuhnya sebelum dipotong. Sapu pisau dengan minyak untuk memudahkan pemotongan.');

-- Steps for Onde-Onde
INSERT INTO steps (KUEHID, STEP) VALUES
(3, 'Campurkan tepung pulut dengan ubi kentang yang dilenyek.'),
(3, 'Masukkan air pandan sedikit demi sedikit sambil diuli hingga menjadi doh yang lembut.'),
(3, 'Bulat-bulatkan doh sebesar biji limau kasturi. Pipihkan dan letak gula melaka di tengah.'),
(3, 'Tutup rapi dan bulatkan semula. Pastikan gula melaka tidak bocor.'),
(3, 'Didihkan air dan masukkan onde-onde. Masak hingga timbul ke permukaan air.'),
(3, 'Angkat dan toskan. Golek di dalam kelapa parut yang telah digaul dengan sedikit garam.'),
(3, 'Hidangkan sejuk atau suam.');

-- Steps for Kuih Bahulu
INSERT INTO steps (KUEHID, STEP) VALUES
(4, 'Panaskan acuan bahulu di dalam oven 180°C. Sapukan minyak pada acuan.'),
(4, 'Pukul telur dan gula menggunakan mixer berkelajuan tinggi sehingga kembang dan gebu (10-15 minit).'),
(4, 'Ayak tepung dan serbuk penaik. Masukkan ke dalam adunan telur secara beransur-ansur.'),
(4, 'Masukkan esen vanila dan mentega cair. Gaul perlahan menggunakan spatula.'),
(4, 'Tuangkan adunan ke dalam acuan bahulu hingga 3/4 penuh.'),
(4, 'Bakar pada suhu 180°C selama 10-12 minit atau sehingga keemasan.'),
(4, 'Keluarkan bahulu dari acuan sejurus selepas keluar dari oven. Sejukkan atas redai.');

-- Steps for Kuih Ketayap
INSERT INTO steps (KUEHID, STEP) VALUES
(5, 'Untuk inti: Masak kelapa parut dengan gula melaka dan daun pandan hingga gula cair dan melekat. Sejukkan.'),
(5, 'Untuk kulit: Campurkan tepung, telur, santan, air pandan dan garam. Kacau hingga licin.'),
(5, 'Tapis adunan dan biarkan rehat selama 30 minit.'),
(5, 'Panaskan kuali dadar. Cedok sedikit adunan dan putar kuali supaya nipis dan rata.'),
(5, 'Masak sebelah sahaja sehingga matang. Angkat dan ulang hingga habis.'),
(5, 'Letakkan inti kelapa di atas kulit, lipat tepi kiri dan kanan, kemudian gulung.'),
(5, 'Potong serong dan hidangkan.');

-- Steps for Kuih Kapit
INSERT INTO steps (KUEHID, STEP) VALUES
(6, 'Pukul telur dan gula hingga kembang dan putih gebu.'),
(6, 'Masukkan santan, tepung gandum dan tepung beras. Kacau hingga sebati.'),
(6, 'Tapis adunan dan biarkan rehat 2 jam atau semalaman untuk hasil yang lebih rangup.'),
(6, 'Panaskan acuan kapit di atas api. Cedok adunan ke dalam acuan.'),
(6, 'Tekan acuan rapat dan bakar kedua-dua belah sehingga keemasan (lebih kurang 1 minit).'),
(6, 'Buka acuan dan cepat-cepat gulung atau lipat kapit semasa masih panas.'),
(6, 'Sejukkan di dalam bekas kedap udara. Kapit yang baik mestilah rangup dan garing.');

-- Steps for Pulut Inti
INSERT INTO steps (KUEHID, STEP) VALUES
(7, 'Rendam beras pulut 4 jam atau semalaman. Toskan.'),
(7, 'Kukus pulut bersama santan, air pandan, kunyit dan garam selama 25-30 minit.'),
(7, 'Kacau sesekali supaya santan sebati. Pastikan pulut masak dan wangi.'),
(7, 'Untuk serawa: Masak kelapa parut dengan gula melaka dan daun pandan sambil dikacau sehingga gula larut dan melekat.'),
(7, 'Letakkan pulut kuning dalam dulang. Padatkan sedikit.'),
(7, 'Taburkan serawa kelapa di atas pulut. Ratakan.'),
(7, 'Sejukkan dan potong kepada bentuk segi empat sama atau segi tiga.');

-- Steps for Kuih Talam
INSERT INTO steps (KUEHID, STEP) VALUES
(8, 'Untuk lapisan bawah: Campurkan tepung beras, tepung ubi kayu, 300ml santan, air pandan dan gula. Kacau rata.'),
(8, 'Tuangkan adunan hijau ke dalam loyang. Kukus 15 minit hingga masak.'),
(8, 'Untuk lapisan atas: Campurkan 300ml santan, tepung gandum, garam. Masak sambil dikacau sehingga pekat.'),
(8, 'Tuangkan adunan putih di atas lapisan hijau yang telah masak. Ratakan.'),
(8, 'Kukus lagi 15-20 minit sehingga lapisan atas masak.'),
(8, 'Biarkan sejuk sepenuhnya sebelum dipotong. Sapu pisau dengan minyak untuk pemotongan yang kemas.');

-- Steps for Kuih Cara
INSERT INTO steps (KUEHID, STEP) VALUES
(9, 'Campurkan tepung beras, gula, santan, air dan telur. Pukul hingga sebati.'),
(9, 'Masukkan serbuk penaik dan gaul rata. Tapis adunan.'),
(9, 'Panaskan acuan cara di atas api sederhana. Sapukan sedikit minyak.'),
(9, 'Tuangkan adunan ke dalam acuan hingga 3/4 penuh. Taburkan bijan.'),
(9, 'Tutup acuan dan masak sehingga tepi kuih garing dan bahagian tengah masak.'),
(9, 'Keluarkan cara menggunakan garfu atau lidi. Sejukkan atas redai.'),
(9, 'Hidangkan sejuk. Simpan dalam bekas kedap udara.');

-- Steps for Kuih Bingka Ubi
INSERT INTO steps (KUEHID, STEP) VALUES
(10, 'Panaskan oven ke suhu 180°C. Sapukan loyang dengan mentega.'),
(10, 'Campurkan ubi kayu parut dengan gula. Kacau hingga gula larut.'),
(10, 'Masukkan telur satu persatu sambil dipukul. Pukul hingga sebati.'),
(10, 'Tuangkan santan, mentega cair, esen vanila dan garam. Kacau rata.'),
(10, 'Tambah daun pandan yang telah diikat simpul. Tuangkan ke dalam loyang.'),
(10, 'Bakar selama 45-60 minit atau sehingga permukaan keemasan dan bila cucuk lidi keluar bersih.'),
(10, 'Sejukkan dalam loyang selama 10 minit. Keluarkan dan potong. Hidangkan sejuk atau suam.');

-- ========================================
-- NOTES:
-- ========================================
-- After running this script, manually add images to the kueh_images folder:
-- - kuih_lapis.jpg
-- - seri_muka.jpg
-- - onde_onde.jpg
-- - bahulu.jpg
-- - ketayap.jpg
-- - kapit.jpg
-- - pulut_inti.jpg
-- - talam.jpg
-- - cara.jpg
-- - bingka_ubi.jpg
--
-- You can download images from:
-- - Google Images (search "kuih [name] malaysia")
-- - Unsplash.com
-- - Pexels.com
-- - Malaysian food blogs
--
-- Recommended image specifications:
-- - Format: JPEG (.jpg)
-- - Max size: 5MB
-- - Recommended dimensions: 1920x1080 or similar aspect ratio
-- ========================================
