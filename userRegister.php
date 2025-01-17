<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
            position: relative;
            z-index: 2;
        }

        .form-container h2 {
            text-align: center;
            font-size: 24px;
            color: #333;
            margin-bottom: 25px;
        }

        .input-container {
            position: relative;
            width: 100%;
            margin: 10px 0;
        }

        input {
            width: 100%;
            padding: 15px;
            padding-left: 40px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            color: #333;
            box-sizing: border-box;
        }

        input:focus {
            border-color: #ff6f61;
            outline: none;
        }

        .input-container i {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #999;
        }

        .w3-button {
            width: 100%;
            padding: 15px;
            background-color: orange;
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .w3-button:hover {
            background-color: #e95b4f;
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .footer-text a {
            color: orange;
            text-decoration: none;
        }

        .image-side {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 25%;
            /* Reduce the width of the image containers */
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
            /* Images are behind the login form */
        }

        .image-side img {
            max-width: 100%;
            /* Ensure the image doesn't exceed the width of its container */
            max-height: 100%;
            /* Ensure the image doesn't exceed the height of its container */
            object-fit: cover;
            /* Ensure the image covers the container without distortion */
            border-radius: 10px;
            /* Optional: Add rounded corners */
        }

        .left-image {
            left: 400px;
            /* Adjust the position of the left image */
        }

        .right-image {
            right: 400px;
            /* Adjust the position of the right image */
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="image-side left-image">
            <img src="sources/register/kueh2.png" alt="Left Image" class="img-fluid">
        </div>

        <div class="form-container">
            <h2>Daftar Akaun Baharu</h2>
            <form method="post" action="">
                <div class="input-container">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Nama Pengguna" required>
                </div>
                <div class="input-container">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Kata laluan" required>
                </div>
                <div class="input-container">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-container">
                    <i class="fas fa-phone"></i>
                    <input type="text" name="phoneNum" placeholder="Nombor Telefon" required>
                </div>
                <div class="input-container">
                    <i class="fas fa-user"></i>
                    <input type="text" name="name" placeholder="Nama Penuh" required>
                </div>
                <button type="submit" name="register" class="w3-button">Register</button>
            </form>
            <p class="footer-text">Sudah ada akaun? <a href="userLogin.php">Log masuk</a></p>
        </div>

        <div class="image-side right-image">
            <img src="sources/register/kueh1.png" alt="Right Image" class="img-fluid">
        </div>
    </div>
</body>

</html>