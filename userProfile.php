<?php


include('header.php');
include('connection.php');

// Initialize the user array
$user = [];

// Ensure the user is logged in and their data is fetched as $user
if (isset($_SESSION['google_user']) && is_array($_SESSION['google_user'])) {
    $user = [
        "userName" => $_SESSION['google_user']['name'],
        "email" => $_SESSION['google_user']['email'],
        "profile_image" => $_SESSION['google_user']['picture']
    ];
} else {
    $user['userName'] = $_SESSION['username'] ?? 'User'; // Fallback for missing session data
    $user['email'] = $_SESSION['email'] ?? 'N/A';
    $user['phoneNum'] = $_SESSION['phoneNum'] ?? 'N/A';
    // Default icon if no image is uploaded
    $user['profile_image'] = "https://static.vecteezy.com/system/resources/previews/024/983/914/non_2x/simple-user-default-icon-free-png.png";
}

// Handle form submission and update the user data in the database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = $_POST['username'];
    $email = $_POST['email'];
    $phoneNum = $_POST['phoneNum'];
    $password = $_POST['password'];

    // Handle the uploaded profile image if provided
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
        // Check for file size limit, etc.
        $profileImage = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($_FILES['profileImage']['tmp_name']));
    } else {
        // If no image is uploaded, keep the existing image
        $profileImage = $user['profile_image'];
    }

    // Update user data in the database
    if (isset($_SESSION['username'])) {
        $userName = $_SESSION['username'];

        $query = "UPDATE USERS SET USERNAME = :username, EMAIL = :email, PHONENUM = :phoneNum, IMAGE = :profile_image WHERE USERNAME = :username";

        // update new password
        if (!empty($password)) {
            $query = "UPDATE USERS SET USERNAME = :username, EMAIL = :email, PHONENUM = :phoneNum, PASSWORD = :password, IMAGE = :profile_image WHERE USERNAME = :username";
        }

        // Parse the SQL statement
        $stmt = oci_parse($condb, $query);

        // Bind the parameters
        oci_bind_by_name($stmt, ':username', $userName);
        oci_bind_by_name($stmt, ':email', $email);
        oci_bind_by_name($stmt, ':phoneNum', $phoneNum);
        oci_bind_by_name($stmt, ':profile_image', $profileImage);


        if (!empty($password)) {
            oci_bind_by_name($stmt, ':password', $password);
        }

        // Execute the query
        if (oci_execute($stmt)) {
            oci_commit($condb); // Commit the transaction



            $_SESSION['username'] = $userName;
            $_SESSION['email'] = $email;
            $_SESSION['phoneNum'] = $phoneNum;
            $_SESSION['profile_image'] = $profileImage;

            // Update the $user array to reflect the session changes
            $user = [
                "userName" => $_SESSION['username'],
                "email" => $_SESSION['email'],
                "phoneNum" => $_SESSION['phoneNum'],
                "profile_image" => $_SESSION['profile_image']
            ];
        } else {
            $message = "Failed to update profile. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">



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

        .btn .userProf {
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

        .listing-container {
            display: flex;
            background-color: #f9f9f9;
            width: 80%;
            margin: 20px auto;
        }

        .cooking-activity-i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .cooking-activity-p {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .cooking-activity-h4 {
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
                <?php if (isset($message)) { ?>
                    <p class="message"><?php echo $message; ?></p>
                <?php } ?>
                <?php if (isset($_SESSION['google_user'])) { ?>
                    <p class="description">You are currently logged in using a Google Account. All sensitive data is hidden
                        as a security measure.</p>
                <?php } ?>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="contact-info">
            <span>
                <i class="fa fa-envelope w3-text-orange" aria-hidden="true"></i>
                <?php echo htmlspecialchars($user['email']); ?>
            </span>
            <?php if (!isset($_SESSION['google_user'])) { ?>
                <span>
                    <i class="fa fa-phone w3-text-orange" aria-hidden="true"></i>
                    <?php echo htmlspecialchars($user['phoneNum']); ?>
                </span>
            <?php } ?>
        </div>

        <!-- Edit Profile Button -->
        <?php if (!isset($_SESSION['google_user'])) { ?>
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

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password">New Password</label>
                    <input class="form-control" type="password" id="password" name="password"
                        placeholder="masukkan kata laluan baharu anda">
                </div>

                <!-- Buttons -->
                <div class="form-buttons">
                    <button type="submit" class="btn userProf btn-primary">Update</button>
                    <button type="button" id="cancelBtn" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div><br>

    <div class="w3-container" style="width:60%;display:flex;justify-content:center;align-items:center;margin-left:10%">
                <?php include('kuehByUser.php'); ?>
            </div>
    <hr>

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

<?php
// Check if the message is present in the URL
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];

    // Define messages based on the msg value
    $messages = [
        'delete_success' => 'Record deleted successfully!',
        'delete_error' => 'Failed to delete the record.',
        'add_success' => 'Record added successfully!',
        'add_error' => 'Failed to add the record!',
        'update_success' => 'Record updated successfully!',
        'update_error' => 'Failed to update the record!'
    ];

    // Get the appropriate message
    $toastMessage = $messages[$msg] ?? 'An unknown error occurred.';

    // Determine the icon (success or error)
    $icon = (strpos($msg, 'success') !== false) ? 'success' : 'error';

    // Display the SweetAlert2 toast
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                toast: true,
                position: 'top',
                icon: '$icon',
                title: '$toastMessage',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        });
    </script>";
}
?>
<?php include('footer.php') ?>

</html>