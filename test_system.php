<!DOCTYPE html>
<html>

<head>
    <title>KuehLegacy System Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
        }

        h2 {
            color: #666;
            margin-top: 30px;
        }

        .success {
            color: #4CAF50;
        }

        .error {
            color: #f44336;
        }

        .warning {
            color: #ff9800;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .test-item {
            margin: 10px 0;
            padding: 10px;
            background: #f9f9f9;
            border-left: 4px solid #4CAF50;
        }

        .test-item.fail {
            border-left-color: #f44336;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-success {
            background: #4CAF50;
            color: white;
        }

        .badge-error {
            background: #f44336;
            color: white;
        }

        .badge-warning {
            background: #ff9800;
            color: white;
        }

        .links {
            margin: 30px 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background: #45a049;
        }

        .btn-secondary {
            background: #2196F3;
        }

        .btn-secondary:hover {
            background: #0b7dda;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🍰 KuehLegacy System Test Suite</h1>

        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $tests_passed = 0;
        $tests_failed = 0;

        // Test 1: Database Connection
        echo "<h2>1. Database Connection Test</h2>";
        try {
            require 'connection.php';
            if (isset($condb) && $condb) {
                echo "<div class='test-item'><span class='badge badge-success'>PASS</span> Database connected successfully</div>";
                echo "<div class='test-item'>Host: {$_ENV['DB_HOST']} | Database: {$_ENV['DB_NAME']} | User: {$_ENV['DB_USER']}</div>";
                $tests_passed++;
            } else {
                echo "<div class='test-item fail'><span class='badge badge-error'>FAIL</span> Database connection failed</div>";
                $tests_failed++;
            }
        } catch (Exception $e) {
            echo "<div class='test-item fail'><span class='badge badge-error'>FAIL</span> " . $e->getMessage() . "</div>";
            $tests_failed++;
        }

        // Test 2: Table Structure
        echo "<h2>2. Database Tables Test</h2>";
        $tables = ['ADMIN', 'KUEH', 'USERS', 'FAVORITE', 'ITEMS', 'STEPS', 'FOODTYPE', 'METHOD', 'ORIGIN', 'POPULARITY', 'SHOP'];
        echo "<table>";
        echo "<tr><th>Table Name</th><th>Record Count</th><th>Status</th></tr>";

        foreach ($tables as $table) {
            $sql = "SELECT COUNT(*) as count FROM $table";
            $result = mysqli_query($condb, $sql);
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                echo "<tr><td>$table</td><td>{$row['count']}</td><td><span class='badge badge-success'>OK</span></td></tr>";
                $tests_passed++;
            } else {
                echo "<tr><td>$table</td><td>-</td><td><span class='badge badge-error'>FAIL</span></td></tr>";
                $tests_failed++;
            }
        }
        echo "</table>";

        // Test 3: Prepared Statements
        echo "<h2>3. Prepared Statements Test</h2>";
        $sql = "SELECT kuehId, kuehName, image FROM KUEH LIMIT 5";
        $stmt = mysqli_prepare($condb, $sql);
        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            echo "<div class='test-item'><span class='badge badge-success'>PASS</span> Prepared statements working</div>";
            echo "<table>";
            echo "<tr><th>Recipe ID</th><th>Recipe Name</th><th>Image</th><th>Status</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                $imageStatus = !empty($row['image']) ? "Has image" : "No image";
                $imagePath = !empty($row['image']) ? "kueh_images/{$row['image']}" : "";
                $fileExists = $imagePath && file_exists($imagePath) ? "✓ File exists" : "⚠ File missing";
                echo "<tr><td>{$row['kuehId']}</td><td>{$row['kuehName']}</td><td>$imageStatus</td><td>$fileExists</td></tr>";
            }
            echo "</table>";
            mysqli_stmt_close($stmt);
            $tests_passed++;
        } else {
            echo "<div class='test-item fail'><span class='badge badge-error'>FAIL</span> Prepared statements failed</div>";
            $tests_failed++;
        }

        // Test 4: Image Directory
        echo "<h2>4. Image Storage Test</h2>";
        $image_dirs = ['kueh_images', 'admin/admin_images'];
        foreach ($image_dirs as $dir) {
            if (is_dir($dir)) {
                $count = count(glob("$dir/*.*"));
                echo "<div class='test-item'><span class='badge badge-success'>OK</span> $dir/ exists ($count files)</div>";
                $tests_passed++;
            } else {
                echo "<div class='test-item fail'><span class='badge badge-error'>FAIL</span> $dir/ not found</div>";
                $tests_failed++;
            }
        }

        // Test 5: Environment Variables
        echo "<h2>5. Environment Variables Test</h2>";
        $env_vars = ['DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME', 'GOOGLE_CLIENT_ID', 'GOOGLE_CLIENT_SECRET'];
        foreach ($env_vars as $var) {
            if (isset($_ENV[$var]) && !empty($_ENV[$var])) {
                $display_value = ($var == 'DB_PASS' || $var == 'GOOGLE_CLIENT_SECRET') ? '***' : $_ENV[$var];
                echo "<div class='test-item'><span class='badge badge-success'>OK</span> $var = $display_value</div>";
                $tests_passed++;
            } else {
                echo "<div class='test-item fail'><span class='badge badge-error'>FAIL</span> $var not set</div>";
                $tests_failed++;
            }
        }

        // Test 6: Composer Packages
        echo "<h2>6. Dependencies Test</h2>";
        $packages = [
            'vlucas/phpdotenv' => 'vendor/vlucas/phpdotenv',
            'intervention/image' => 'vendor/intervention/image',
            'google/apiclient' => 'vendor/google/apiclient'
        ];
        foreach ($packages as $name => $path) {
            if (is_dir($path)) {
                echo "<div class='test-item'><span class='badge badge-success'>OK</span> $name installed</div>";
                $tests_passed++;
            } else {
                echo "<div class='test-item fail'><span class='badge badge-error'>FAIL</span> $name not found</div>";
                $tests_failed++;
            }
        }

        // Test 7: Critical Files
        echo "<h2>7. Critical Files Test</h2>";
        $critical_files = [
            'connection.php',
            'admin/connection.php',
            'callback.php',
            'userLogin.php',
            'addKueh.php',
            'editKueh.php',
            'kuehDetails.php',
            'admin/index.php',
            'admin/mainpage.php'
        ];
        foreach ($critical_files as $file) {
            if (file_exists($file)) {
                // Check for Oracle code
                $content = file_get_contents($file);
                $has_oracle = preg_match('/oci_(parse|bind|execute|fetch|close)/i', $content);
                if ($has_oracle) {
                    echo "<div class='test-item fail'><span class='badge badge-warning'>WARN</span> $file exists but contains Oracle code</div>";
                    $tests_failed++;
                } else {
                    echo "<div class='test-item'><span class='badge badge-success'>OK</span> $file exists and converted</div>";
                    $tests_passed++;
                }
            } else {
                echo "<div class='test-item fail'><span class='badge badge-error'>FAIL</span> $file not found</div>";
                $tests_failed++;
            }
        }

        mysqli_close($condb);

        // Summary
        $total_tests = $tests_passed + $tests_failed;
        $pass_rate = round(($tests_passed / $total_tests) * 100);

        echo "<h2>📊 Test Summary</h2>";
        echo "<div style='font-size: 18px; padding: 20px; background: #f0f0f0; border-radius: 5px;'>";
        echo "<strong>Total Tests:</strong> $total_tests<br>";
        echo "<strong class='success'>Passed:</strong> $tests_passed<br>";
        echo "<strong class='error'>Failed:</strong> $tests_failed<br>";
        echo "<strong>Pass Rate:</strong> $pass_rate%<br>";

        if ($tests_failed == 0) {
            echo "<div style='margin-top: 20px; padding: 15px; background: #4CAF50; color: white; border-radius: 5px;'>";
            echo "🎉 <strong>ALL TESTS PASSED!</strong> System is ready for use.";
            echo "</div>";
        } else {
            echo "<div style='margin-top: 20px; padding: 15px; background: #ff9800; color: white; border-radius: 5px;'>";
            echo "⚠ <strong>Some tests failed.</strong> Please review the errors above.";
            echo "</div>";
        }
        echo "</div>";
        ?>

        <h2>🔗 Quick Links</h2>
        <div class="links">
            <a href="index.php" class="btn">Home Page</a>
            <a href="welcome.php" class="btn btn-secondary">User Login</a>
            <a href="admin/index.php" class="btn btn-secondary">Admin Login</a>
            <a href="kuehListing.php" class="btn btn-secondary">Browse Recipes</a>
            <a href="test_connection.php" class="btn btn-secondary">Run Again</a>
        </div>
    </div>
</body>

</html>