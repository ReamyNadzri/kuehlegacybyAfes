<?php
include('../includes/header.php');
include('../connection.php');

$user = [];

// Ensure the user is logged in and their data is fetched as $user
if (isset($_SESSION['google_user']) && is_array($_SESSION['google_user'])) {
    $user = [
        "userName" => $_SESSION['google_user']['name'],
        "email" => $_SESSION['google_user']['email'],
        "profile_image" => $_SESSION['google_user']['picture']
    ];
} else {
    $user['userName'] = $_SESSION['username'] ?? 'User';
    $user['email'] = $_SESSION['email'] ?? 'N/A';
    $user['phoneNum'] = $_SESSION['phoneNum'] ?? 'N/A';
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
        $profileImage = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($_FILES['profileImage']['tmp_name']));
    } else {
        $profileImage = $user['profile_image'];
    }

    // Update user data in the database
    if (isset($_SESSION['username'])) {
        $userName = $_SESSION['username'];

        $query = "UPDATE USERS SET USERNAME = ?, EMAIL = ?, PHONENUM = ?, IMAGE = ? WHERE USERNAME = ?";

        if (!empty($password)) {
            $query = "UPDATE USERS SET USERNAME = ?, EMAIL = ?, PHONENUM = ?, PASSWORD = ?, IMAGE = ? WHERE USERNAME = ?";
        }

        $stmt = mysqli_prepare($condb, $query);

        if (!empty($password)) {
            mysqli_stmt_bind_param($stmt, 'ssssss', $userName, $email, $phoneNum, $password, $profileImage, $userName);
        } else {
            mysqli_stmt_bind_param($stmt, 'sssss', $userName, $email, $phoneNum, $profileImage, $userName);
        }

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['username'] = $userName;
            $_SESSION['email'] = $email;
            $_SESSION['phoneNum'] = $phoneNum;
            $_SESSION['profile_image'] = $profileImage;

            $user = [
                "userName" => $_SESSION['username'],
                "email" => $_SESSION['email'],
                "phoneNum" => $_SESSION['phoneNum'],
                "profile_image" => $_SESSION['profile_image']
            ];
            
            // Redirect to show changes and trigger alert
            echo "<script>window.location.href = 'userProfile.php?success=1';</script>";
            exit;
        } else {
            $message = "Gagal mengemas kini profil. Sila cuba lagi.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<head>
    <style>
        .profile-card {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 24px;
            padding: 2.5rem;
        }

        .profile-img-container {
            position: relative;
            width: 120px;
            height: 120px;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid var(--color-border);
            transition: border-color 0.3s var(--ease-custom);
        }
        .profile-img:hover {
            border-color: var(--color-accent);
        }

        .camera-overlay {
            position: absolute;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            color: #FFFFFF;
            font-size: 1.5rem;
            cursor: pointer;
            transition: opacity 0.3s var(--ease-custom);
        }

        .profile-img-container:hover .camera-overlay {
            opacity: 1;
        }

        .edit-form-card {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 20px;
            padding: 2rem;
            max-width: 500px;
            width: 100%;
            margin: 2rem auto 0 auto;
        }
    </style>
</head>

<div class="row justify-content-center g-4 my-2">
    <div class="col-12 col-md-10 col-lg-8">
        
        <!-- Profile Overview Card -->
        <div class="profile-card">
            <div class="row align-items-center g-4">
                <div class="col-12 col-sm-auto d-flex justify-content-center">
                    <div class="profile-img-container">
                        <label for="fileInput" class="mb-0">
                            <img src="<?php echo htmlspecialchars($user['profile_image']); ?>"
                                 alt="Profile Image"
                                 class="profile-img"
                                 id="profileImage"
                                 onerror="this.src='https://static.vecteezy.com/system/resources/previews/024/983/914/non_2x/simple-user-default-icon-free-png.png';">
                            <div class="camera-overlay">
                                <i class="bi bi-camera"></i>
                            </div>
                        </label>
                        <form id="avatarForm" method="POST" enctype="multipart/form-data" style="display: none;">
                            <input type="file" id="fileInput" name="profileImage" accept="image/*" onchange="submitAvatar()">
                        </form>
                    </div>
                </div>
                
                <div class="col-12 col-sm text-center text-sm-start">
                    <h2 class="font-serif fw-bold username mb-2" style="font-family: var(--font-serif) !important;">
                        <?php echo htmlspecialchars($user['userName']); ?>
                    </h2>
                    
                    <div class="d-flex flex-wrap justify-content-center justify-content-sm-start gap-3 mt-1 text-muted font-sans" style="font-size: 0.95rem;">
                        <span>
                            <i class="bi bi-envelope me-1 text-success"></i>
                            <?php echo htmlspecialchars($user['email']); ?>
                        </span>
                        <?php if (!isset($_SESSION['google_user']) && $user['phoneNum'] !== 'N/A'): ?>
                            <span>
                                <i class="bi bi-telephone me-1 text-success"></i>
                                <?php echo htmlspecialchars($user['phoneNum']); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (isset($_SESSION['google_user'])): ?>
                        <p class="text-muted font-mono mt-3 mb-0" style="font-size: 0.75rem;">
                            Log masuk menggunakan Akaun Google. Data profil disegerakkan secara automatik.
                        </p>
                    <?php endif; ?>
                </div>
                
                <?php if (!isset($_SESSION['google_user'])): ?>
                    <div class="col-12 col-sm-auto text-center text-sm-end">
                        <button type="button" id="editProfileBtn" class="btn btn-secondary-custom rounded-pill px-4">
                            <i class="bi bi-pencil-square me-2"></i> Edit Profil
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (isset($message)): ?>
                <div class="alert alert-danger mt-4 mb-0 rounded-3 py-2" role="alert">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Profile Edit Form (Hidden by default) -->
        <div class="edit-form-card" id="editForm" style="display: none;">
            <h4 class="font-serif mb-4" style="font-family: var(--font-serif) !important;">Kemaskini Profil</h4>
            <form method="POST" enctype="multipart/form-data">
                
                <!-- Username -->
                <div class="mb-3">
                    <label for="username" class="form-label font-mono text-uppercase text-muted" style="font-size: 0.75rem;">Username</label>
                    <input class="form-control rounded-pill px-3" type="text" id="username" name="username"
                           value="<?php echo htmlspecialchars($user['userName']); ?>" required>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label font-mono text-uppercase text-muted" style="font-size: 0.75rem;">Email</label>
                    <input class="form-control rounded-pill px-3" type="email" id="email" name="email"
                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <!-- Phone -->
                <div class="mb-3">
                    <label for="phoneNum" class="form-label font-mono text-uppercase text-muted" style="font-size: 0.75rem;">Phone Number</label>
                    <input class="form-control rounded-pill px-3" type="text" id="phoneNum" name="phoneNum"
                           value="<?php echo htmlspecialchars($user['phoneNum'] !== 'N/A' ? $user['phoneNum'] : ''); ?>" required>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="form-label font-mono text-uppercase text-muted" style="font-size: 0.75rem;">New Password</label>
                    <input class="form-control rounded-pill px-3" type="password" id="password" name="password"
                           placeholder="Masukkan kata laluan baharu jika ingin tukar">
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between gap-3">
                    <button type="submit" class="btn btn-primary-custom w-50 justify-content-center">Kemaskini</button>
                    <button type="button" id="cancelBtn" class="btn btn-secondary-custom w-50 justify-content-center">Batal</button>
                </div>
            </form>
        </div>
        
    </div>
</div>

<hr class="custom-divider">

<!-- User Recipes Section -->
<?php include('../recipes/kuehByUser.php'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editProfileBtn = document.getElementById('editProfileBtn');
        const editForm = document.getElementById('editForm');
        const cancelBtn = document.getElementById('cancelBtn');

        if (editProfileBtn) {
            editProfileBtn.onclick = function() {
                editForm.style.display = 'block';
                editForm.scrollIntoView({ behavior: 'smooth' });
            };
        }

        if (cancelBtn) {
            cancelBtn.onclick = function() {
                editForm.style.display = 'none';
            };
        }

        // Phone number input digit filter
        const phoneInput = document.getElementById("phoneNum");
        if (phoneInput) {
            phoneInput.addEventListener("input", function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 11);
            });
        }
        
        // Show success alert if redirected from updates
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            Swal.fire({
                toast: true,
                position: 'top',
                icon: 'success',
                title: 'Profil berjaya dikemas kini!',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            // Clean URL parameter
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });

    function submitAvatar() {
        // Automatically submit the avatar upload when chosen
        const form = document.getElementById('avatarForm');
        const fileInput = document.getElementById('fileInput');
        
        if (fileInput.files.length > 0) {
            // We can submit a post form by appending files and triggering submit
            // However, to keep it simple, we wrap the inputs in a normal POST form and trigger form submit
            const normalForm = document.createElement('form');
            normalForm.method = 'POST';
            normalForm.enctype = 'multipart/form-data';
            normalForm.style.display = 'none';
            
            // Move file input to this temporary form
            const newFileInput = fileInput.cloneNode(true);
            
            // Also need username/email/phone fields to avoid query errors
            const usernameInput = document.createElement('input');
            usernameInput.name = 'username';
            usernameInput.value = '<?php echo addslashes($user['userName']); ?>';
            
            const emailInput = document.createElement('input');
            emailInput.name = 'email';
            emailInput.value = '<?php echo addslashes($user['email']); ?>';
            
            const phoneInput = document.createElement('input');
            phoneInput.name = 'phoneNum';
            phoneInput.value = '<?php echo addslashes($user['phoneNum'] !== 'N/A' ? $user['phoneNum'] : ''); ?>';
            
            normalForm.appendChild(usernameInput);
            normalForm.appendChild(emailInput);
            normalForm.appendChild(phoneInput);
            normalForm.appendChild(fileInput); // Move actual file input so the file uploads
            
            document.body.appendChild(normalForm);
            normalForm.submit();
        }
    }
</script>
<!-- Page Closing Tags (Backwards-compatibility for 5-div open layout) -->
</main> <!-- content-body -->
</div> <!-- content-wrapper-compat-4 -->
</div> <!-- content-wrapper-compat-3 -->
</div> <!-- main-layout -->
</div> <!-- app-container -->

<?php include('../includes/footer.php'); ?>