<?php
include('header.php');

// Sample user data
$user = [
    "userName" => "Johnny",
    "email" => "johnny@example.com",
    "phoneNum" => "123-456-7890",
    "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
    "profile_image" => "https://thumbs.dreamstime.com/b/cute-anime-girl-chef-kitchen-cute-anime-girl-chef-kitchen-generative-ai-342085902.jpg" // Avatar Profile
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user['userName'] = $_POST['username'];
    $user['email'] = $_POST['email'];
    $user['phoneNum'] = $_POST['phoneNum'];
    $user['description'] = $_POST['description'];

    // Handle uploaded image
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
        $user['profile_image'] = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($_FILES['profileImage']['tmp_name']));
    }
}

$cooking_activity_uploaded = false; // Set this to true when activity is uploaded
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


    <style>
        /* Profile Container */
        .profile-container {
            padding-top: 20px;
            margin-left: 10%;
            width: 80%;
        }

        .profile-header {
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }

        .profile-img-container {
            position: relative;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
        }

        .camera-icon {
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 1.5rem;
            color: white;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            padding: 5px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .profile-img-container:hover .camera-icon {
            opacity: 1;
        }

        .description-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .username {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 0;
        }

        .description {
            font-style: italic;
        }

        .contact-info {
            display: block;
            font-size: 1rem;
            margin-top: 10px;
            padding-left: 0;
            position: relative;
            left: 0;
        }

        .contact-info span {
            margin-bottom: 10px;
        }

        /* Edit-profile button */
        .edit-profile {
            margin-top: 20px;
            text-align: center;
            color: rgb(231, 131, 0);
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            padding: 15px;
            background-color: rgb(255, 255, 255);
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .edit-profile:hover {
            text-decoration: underline;
            background-color: #f0f0f0;
        }

        .edit-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin-top: 20px;
            margin-left: auto;
            margin-right: auto;
            display: none;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-size: 1rem;
            font-weight: bold;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            background-color: #f9f9f9;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: #ff7043;
            outline: none;
        }

        .form-buttons {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .btn {
            padding: 12px 20px;
            font-size: 1rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 48%;
        }

        .btn-primary {
            background-color: #ff7043;
            color: white;
        }

        .btn-primary:hover {
            background-color: #e65c36;
        }

        .btn-secondary {
            background-color: #ccc;
            color: #333;
        }

        .btn-secondary:hover {
            background-color: #b3b3b3;
        }

        /* Cooking Activity Container */
        .cooking-activity-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            border: 2px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            width: 80%;
            margin: 20px auto;
        }

        .cooking-activity-container i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .cooking-activity-container p {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .cooking-activity-container h4 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .cooking-activity-container .start-button {
            padding: 10px 20px;
            font-size: 1rem;
            background-color: #ff7043;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .cooking-activity-container .start-button:hover {
            background-color: #e65c36;
        }
    </style>
</head>

<body>

    <div class="w3-container profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-img-container">
                <!-- Profile Image with Upload -->
                <label for="fileInput">
                    <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Image"
                        class="profile-img" id="profileImage">
                    <i class="fa fa-camera camera-icon"></i>
                </label>
                <form method="POST" enctype="multipart/form-data" style="display: none;">
                    <input type="file" id="fileInput" name="profileImage" accept="image/*"
                        onchange="previewImage(event)">
                </form>
            </div>
            <div class="description-container">
                <p class="username"><?php echo htmlspecialchars($user['userName']); ?></p>
                <p class="description"><?php echo htmlspecialchars($user['description']); ?></p>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="contact-info">
            <span>
                <i class="fa fa-envelope w3-text-orange" aria-hidden="true"></i>
                <?php echo htmlspecialchars($user['email']); ?>
            </span>
            <span>
                <i class="fa fa-phone w3-text-orange" aria-hidden="true"></i>
                <?php echo htmlspecialchars($user['phoneNum']); ?>
            </span>
        </div>

        <!-- Edit Profile Button -->
        <div class="edit-profile" id="editProfileBtn">
            Edit Profile
            <i class="fa fa-pencil w3-margin-left"></i>
        </div>

        <!-- Edit Form -->
        <div class="edit-form" id="editForm" style="display: none;">
            <form method="POST" enctype="multipart/form-data">
                <!-- Username Field -->
                <div class="form-group">
                    <label for="username">Username</label>
                    <input class="form-control" type="text" id="username" name="username"
                        value="<?php echo htmlspecialchars($user['userName']); ?>" required>
                </div>

                <!-- Email Field -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input class="form-control" type="email" id="email" name="email"
                        value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <!-- Phone Number Field -->
                <div class="form-group">
                    <label for="phoneNum">Phone Number</label>
                    <input class="form-control" type="text" id="phoneNum" name="phoneNum"
                        value="<?php echo htmlspecialchars($user['phoneNum']); ?>" required>
                </div>

                <!-- Description Field -->
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4"
                        required><?php echo htmlspecialchars($user['description']); ?></textarea>
                </div>

                <!-- Buttons -->
                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" id="cancelBtn" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cooking Activity Message -->
    <?php if (!$cooking_activity_uploaded): ?>
        <div class="cooking-activity-container">
            <p><i class="fa fa-utensils"></i><br> Belum ada aktiviti membuat kueh</p>
            <h4> Kongsi resipe idaman anda!</h4>
            <button class="start-button">Mulakan!</button>
        </div>
    <?php endif; ?>


    <script>
        document.getElementById('editProfileBtn').onclick = function () {
            document.getElementById('editForm').style.display = 'block';
        };

        document.getElementById('cancelBtn').onclick = function () {
            document.getElementById('editForm').style.display = 'none';
        };

        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                document.getElementById('profileImage').src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>

<?php include('footer.php') ?>

</html>
