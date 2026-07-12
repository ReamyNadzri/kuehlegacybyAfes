                <!-- Nav Actions (Share, Login/Logout, Profile) -->
                <div class="nav-actions">
                    <a href="<?php echo $root_path; ?>recipes/addKueh.php" class="btn-primary-custom">
                        <span>Kongsi Resipi</span>
                        <div class="icon-wrapper">
                            <i class="bi bi-plus-lg"></i>
                        </div>
                    </a>

                    <?php if (empty($_SESSION['username'])): ?>
                        <a href="<?php echo $root_path; ?>auth/userLogin.php" class="btn-secondary-custom">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Log Masuk</span>
                        </a>
                    <?php else: ?>
                        <div class="d-flex align-items-center gap-3">
                            <a href="<?php echo $root_path; ?>auth/userProfile.php" class="profile-badge text-decoration-none">
                                <?php
                                $defaultProfileImage = 'https://static.vecteezy.com/system/resources/previews/024/983/914/non_2x/simple-user-default-icon-free-png.png';
                                if (isset($_SESSION['google_user'])) {
                                    $profilegambor = $_SESSION['google_user']['picture'];
                                    echo "<img src='$profilegambor' alt='profile' onerror='this.src=\"$defaultProfileImage\";'>";
                                } else {
                                    echo "<img src='$defaultProfileImage' alt='profile'>";
                                }
                                echo "<span>Hi, " . htmlspecialchars($_SESSION['username']) . "</span>";
                                ?>
                            </a>
                            <a href="<?php echo $root_path; ?>auth/logout.php" class="btn-secondary-custom py-2">
                                <span>Log Keluar</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </header> <!-- Close top-nav -->

            <!-- Content Layout Compatibility Wrapper -->
            <div class="content-wrapper-compat-3"> <!-- Container #3 (Closed at bottom of pages) -->
                <div class="content-wrapper-compat-4"> <!-- Container #4 (Closed at bottom of pages) -->
                    
                    <!-- Content Body -->
                    <main class="content-body"> <!-- Container #5 (Closed at bottom of pages) -->