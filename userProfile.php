<?php
include('header.php');



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

if(isset($_SESSION['google_user'])){
    $user = [
    "userName" => $_SESSION['google_user']['name'],
    "email" => $_SESSION['google_user']['email'],
    "profile_image" => $_SESSION['google_user']['picture'] // Avatar Profile
    ];
}else{
    $user['userName'] = $_SESSION['username'];
    $user['email'] = $_SESSION['email'];
    $user['phoneNum'] = $_SESSION['phoneNum'];
    $user['profile_image'] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAACXBIWXMAAAsTAAALEwEAmpwYAAAFdUlEQVR4nO1aS4hcRRR9UaPJxs9CVBT8oBmDEsVPFE0iGJxEMbixySKSTt+qe9+7t6dHRlQQlMadGozfGH8ouHAnoi5cmLjwFxJJ1CgiMaMjRpB8FxLJZybK7e6Zed0zk3RVve6ZhHegYJjX737Ou1V1696Kohw5cuTIkaOjIOq7FogTg7IBSDYZlEFAOQAkR2uj9jfv0mdA/JpBiSFJeqJTGdbKjUC8zqDsNiT/+Qwg/tOgPF+K+26IThHMMsQrgGSzr9NTDuRvrC3frzqimQgTxwsN8reZO94aFShbgMq3RDMFhYGBuUD8CiCPdNr5cRJ4BIhfKhaLc6bVeUiSHiD5oVuOT7JGfLcmjq+ZFudLJLcZ5H3T5XxqShwokSzqrvOY3GuQ/51u58cjQQ4ZkuXdcZ5kUbjz/Bcgvw8kTwMKAMlSnU7W9l3JzBfoUF0G5RcXEhD5jo46D0nSY4gPejmtOwRKnyZF7eozyM85RsL+jq0Jhdpq77Pg8Q79wt7R5ky0bO/I7gAor3oY83aIMYVC4UxDvMcj4l7MPMkB532e38wiazPE73oQMLyGyjdll96iW4anU6VSqZyThfJSqXIhED8CyIcdbdicSdpsiFe4fgFry8uijAHEFVc7dLvOQLG4HWxQdnbiwEJEs3X7dIyCr4OPtMaRdSB5NuoQgPgt5/XA8IIQheucwz+WB9IySjGvNsRDhvg9Q9IPxL3Wlufp3NZ1QncJY/ovMobn21geNCjPAMnPBmWg1R5DXPJYENd6E2B8ihmG5zeFrWdBRHcda/nmJnsML/CIyD8Csj5xNpyIzhszOO6700dGioRP0zYR0SU+cryyQ9Aanh8Bs8cIQH4ikICR1XF86ag8nTI+ciwxORNgUDb4KGuSQfJhCAH1UbYtMn3krHcngOTzcAJ4WygBgPJOMAHIG30i4PdQAoD472ACSL4IJ0AGPQhgr2pPi4zwognK7lACgGSvOwEkR0JDzZAcDyaA5EgLqRudCUA+7EwAOB4+Ws8AYGVVBs43Bj+UInV5dwgg2RuSA2gCkiEBQ6Nyi8WHz/d4f48zAQZl0FXRqkrl3BAC25nDSrL7+/yrTwRscjY2jheOEUjyVIYEPDkq19rkdg8Zn3WlBAbEr2eRTTbJtIItdr3hLAflZWcCDErsYfBwugBarVbPAOLfvAlAGVQZ44TKUtURSmJbsLY8zzNcD2nXaPyL8aMBBIwdiVVmowHiLsfI1c4EhGSDhuSj8VW7OEcvP3g4vzNdVwTiT3yjKPKFQX7Bk4Dj6QWxhHwXIB9ziKKj6X5fY+HzSqq0qONNACTJ9d7hS7yt6QsiS7vkpY+vtSMwynZfO6yV67wJUGhh0Z+E5mOozukT9Rcaz/qb3iFZ76s/uCg61gn2J0CNWBKlYGK5T1vak/xuf2uH19pkcYjuTMriCkD+yj8EJ/YItAucXtQA5eNiHF8x8XflZQFT8Mso0/s/5L7/6kDkW3316rteUYd8LPN7RIZkrcdXGCqKXDwqQ3sGWticSoc+S/cV9N16SX0G9CYKAwNzDfL37RnAu0oxm0KhcHZaRv2AxAeB5LF0sVP/NsiPN541FS9UhspqN5fQe0Nqa+YEKADk8hO2q1EGdQurVqtnRS3QlLbNaTScTn+b3+dCrWkypX7eV2K+KuokgGTJxJSUdxjilZMZ7nOO199OJadBxMq6zgkpeHcuS1mbLAbkf7QNrq0vvchwsnf0y7RLgO4SDrdSt6rz1iZ3R92EtZXLXLrA2uZqn4DmlthJMKthy8wGEPe2vZLH5Xui0w1A3NvOgai+h3NvdDqiqPt67TwgHwDxT7XOcX38qP/TZ+m8IUeOHDly5MiRI0fUOfwPWUs8T7LFLncAAAAASUVORK5CYII=";

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

<body><br><br>

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
                    <?php
                        if(isset($_SESSION['google_user'])){ ?>
                        <p class="description">You are currently logged in using a Google Account. All sensitive data is hidden as a security measure.</p>
                    <?php } ?>
                    
                    </div>
            
            
        </div>

        <!-- Contact Info -->
        <div class="contact-info">
            <span>
                <i class="fa fa-envelope w3-text-orange" aria-hidden="true"></i>
                <?php echo htmlspecialchars($user['email']); ?>
            </span>
            <?php
                        if(!isset($_SESSION['google_user'])){ ?>
            <span>
                <i class="fa fa-phone w3-text-orange" aria-hidden="true"></i>
                <?php echo htmlspecialchars($user['phoneNum']); ?>
            </span>
            <?php } ?>
        </div>

        <?php if(!isset($_SESSION['google_user'])){ ?>
            <!-- Edit Profile Button -->
            <div class="edit-profile" id="editProfileBtn">
                Edit Profile
                <i class="fa fa-pencil w3-margin-left"></i>
            </div>
        <?php } ?>
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
