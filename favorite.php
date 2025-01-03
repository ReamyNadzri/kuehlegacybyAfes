<?php
include('header.php');

// Testing Data Appearance
$favorites = [
    [
        'kuehID' => 1,
        'kuehName' => 'Kueh 1',
        'kuehDesc' => 'Description 1',
        'image' => 'https://i.ytimg.com/vi/xVVHZ_CJllg/hq720.jpg?sqp=-oaymwEhCK4FEIIDSFryq4qpAxMIARUAAAAAGAElAADIQj0AgKJD&rs=AOn4CLAOruLYeZiF3LKxnnGK63k51jC8HQ',
        'tagKueh' => 'tag 1, tag 2, tag 3',
        'date' => '2024-12-20'

    ],
    [
        'kuehID' => 2,
        'kuehName' => 'Kuih 2',
        'kuehDesc' => 'Description 2',
        'image' => 'https://i.ytimg.com/vi/xVVHZ_CJllg/hq720.jpg?sqp=-oaymwEhCK4FEIIDSFryq4qpAxMIARUAAAAAGAElAADIQj0AgKJD&rs=AOn4CLAOruLYeZiF3LKxnnGK63k51jC8HQ',
        'tagKueh' => 'tag 1, tag 2, tag 3',
        'date' => '2024-12-22'

    ],
    [
        'kuehID' => 3,
        'kuehName' => 'Kueh 3',
        'kuehDesc' => 'Description 3',
        'image' => 'https://i.ytimg.com/vi/xVVHZ_CJllg/hq720.jpg?sqp=-oaymwEhCK4FEIIDSFryq4qpAxMIARUAAAAAGAElAADIQj0AgKJD&rs=AOn4CLAOruLYeZiF3LKxnnGK63k51jC8HQ',
        'tagKueh' => 'tag 1, tag 2, tag 3',
        'date' => '2024-12-18'

    ]
];

// search function
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';


if ($searchQuery) {
    $favorites = array_filter($favorites, function ($favorite) use ($searchQuery) {
        return strpos(strtolower($favorite['kuehName']), strtolower($searchQuery)) !== false ||
            strpos(strtolower($favorite['kuehDesc']), strtolower($searchQuery)) !== false ||
            strpos(strtolower($favorite['tagKueh']), strtolower($searchQuery)) !== false;
    });
}

$sortOrder = $_GET['sort'] ?? 'desc'; // Sort by latest first
usort($favorites, function ($a, $b) use ($sortOrder) {
    return $sortOrder === 'asc'
        ? strtotime($a['date']) - strtotime($b['date'])
        : strtotime($b['date']) - strtotime($a['date']);
});
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kueh Kegemaran Anda</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        /* Add your styles here */
        body {
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            padding: 0;
            margin-top: 10px;
        }

        .content {
            flex: 3;
            padding: 20px;
        }

        .favorite-item {
            display: flex;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 15px;
            position: relative;
        }

        .favorite-item img {
            width: 200px;
            height: 200px;
            object-fit: cover;
            margin-right: 20px;
        }

        .favorite-item-details {
            flex: 1;
            padding: 10px;
        }

        .favorite-item h3 {
            font-size: 1.4rem;
            margin: 10px 0;
        }

        .favorite-item p {
            font-size: 1rem;
            color: #666;
        }

        .favorite-item .tag {
            font-size: 1rem;
            color: #f57c00;
            margin-top: 5px;
        }

        .favorite-item .bookmark-icon {
            position: absolute;
            right: 10px;
            top: 10px;
            font-size: 1.5rem;
            color: #f57c00;
            cursor: pointer;
        }

        .w3-bar a {
            background-color: transparent;
            color: black;
            border: 1px solid black;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease-in-out;
        }

        .w3-bar a:hover {
            background-color: black;
            color: white;
            text-decoration: none;
        }

        .comment-section {
            flex: 1;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            margin-left: 20px;
        }

        .comment-section h4 {
            margin-bottom: 15px;
        }

        .comment-section textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .comment-section button {
            width: 100%;
            padding: 10px;
            background-color: orange;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .comment-section button:hover {
            background-color: #f57c00;
        }

        /* Search Bar Style */
        .w3-input.w3-bar-item[type="text"] {
            width: 250px;
            padding: 8px 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px;
            padding-left: 30px;
        }

        .w3-input.w3-bar-item[type="text"]:focus {
            outline: none;
        }

        .w3-bar {
            display: flex;
            align-items: center;
            gap: 10px;
           
        }

        .w3-bar form {
            margin: 0;
        }

        .w3-bar div {
            flex-shrink: 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Favorite Kueh Content -->
        <div class="content">
            <!-- Display Total Favorites -->
            <div class="w3-padding-32">
                <h3>Total Items in Favorites: <?php echo count($favorites); ?></h3>
            </div>

            <!-- Sort Buttons and Search Bar -->
            <div class="w3-bar w3-padding">
                <!-- Search Bar -->
                <form method="get" action="">
                    <div style="position: relative; display: inline-block;">
                        <!-- Magnifying Glass Icon -->
                        <i class="fa fa-search"
                            style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%);"></i>
                        <!-- Input Field -->
                        <input type="text" class="w3-input w3-bar-item" name="search" placeholder="Cari..."
                            value="<?php echo htmlspecialchars($searchQuery); ?>" style="padding-left: 30px;">
                    </div>
                </form>

                <!-- Sort Buttons -->
                <div>
                    <a href="?sort=desc" class="w3-button">Cari Yang Terkini</a>
                    <a href="?sort=asc" class="w3-button">Cari Yang Terlama</a>
                </div>
            </div>

            <!-- content -->
            <?php if (empty($favorites)): ?>
                <div class="w3-center w3-padding-32">
                    <i class="fa fa-heart w3-xxlarge" style="color: #f57c00;"></i>
                    <h3>Tiada kueh kegemaran dijumpai</h3>
                </div>
            <?php else: ?>
                <?php foreach ($favorites as $favorite): ?>
                    <a href="kueh_details.php?kuehID=<?php echo $favorite['kuehID']; ?>" style="text-decoration: none;">
                        <div class="favorite-item">
                            <img src="<?php echo htmlspecialchars($favorite['image']); ?>"
                                alt="<?php echo htmlspecialchars($favorite['kuehName']); ?>">
                            <div class="favorite-item-details">
                                <h3><?php echo htmlspecialchars($favorite['kuehName']); ?></h3>
                                <p><?php echo htmlspecialchars($favorite['kuehDesc']); ?></p>
                                <div class="tag"><?php echo htmlspecialchars($favorite['tagKueh']); ?></div>
                                <p>Date Added: <?php echo htmlspecialchars($favorite['date']); ?></p>
                            </div>
                            <i class="fa fa-bookmark bookmark-icon" aria-hidden="true"></i>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Comment Section -->
        <div class="comment-section">
            <h4>Berikan Maklum Balas:</h4>
            <form action="submit_feedback.php" method="POST">
                <textarea name="feedback" placeholder="Sila tulis maklum balas di sini.."></textarea>
                <br>
                <button type="submit">Hantar</button>
            </form>
            <h4>Sila jangan masukkan sebarang maklumat peribadi (data peribadi) dalam borang maklum balas ini, termasuk
                nama dan butiran peribadi anda.

                Kami akan gunakan maklumat ini untuk membantu kami memperbaiki perkhidmatan kami. Dengan menghantar
                maklum balas ini, anda bersetuju untuk membenarkan maklumat anda diproses sejajar dengan Dasar Privasi
                dan Terma Perkhidmatan kami. </h4>
        </div>
    </div>
</body>

</html>
<?php include('footer.php'); ?>
