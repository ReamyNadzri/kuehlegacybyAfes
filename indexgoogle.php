<?php



?>
<!DOCTYPE html>
<html>
<head>
    <title>Google Login Example</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f4f4f4;
            margin: 0;
        }
        .google-login-button {
            display: inline-flex;
            align-items: center;
            padding: 10px 16px;
            background-color: #ffffff;
            color: rgba(0, 0, 0, 0.87);
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);
            border: 1px solid #dadce0;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        .google-login-button:hover {
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
            background-color: #f8f9fa;
        }
        .google-login-button:active {
            background-color: #e8e8e8;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);
        }
        .google-icon {
            width: 18px;
            height: 18px;
            margin-right: 12px;
        }
    </style>
    <!-- Load Roboto font from Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>
    <a href="<?= $url ?>" class="google-login-button">
        <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0Ij48cGF0aCBkPSJNMTIuMjQgMTAuMjhWMTQuNGg2LjI0Yy0wLjI0IDEuMTItMC41NiAyLjE2LTEuMDggMy4wOGwtMy40OC0yLjY4Yy0wLjY0LTAuNDgtMS4yOC0xLjI4LTEuMjgtMi4yIDAtMS4wOCAwLjgtMi4wOCAxLjgtMi41NnpNMTIgM3MxLjY4IDAgMy4yIDAuNTYgNC40IDEuNDQgMy4wOC0zLjA4QzE3LjYgMC42NCAxNC45NiAwIDEyIDAgNy4yIDAgMy4yOCAzLjA4IDEuNiA3LjY0bDMuMzYgMi42QzUuNiA4LjI4IDguNTIgNiAxMiA2eiIgZmlsbD0iIzQyODVGNCIvPjxwYXRoIGQ9Ik0xMiAyNGMyLjggMCA1LjQtMS4wNCA3LjQtMi43NmwtMy4zNi0yLjZjLTAuOTYgMC42NC0yLjA4IDEuMDQtMy4zNiAxLjA0LTIuNTYgMC00LjgtMS42OC01LjY0LTQuMDhIMS42djIuNjRDMy4yOCAyMC45NiA3LjIgMjQgMTIgMjR6IiBmaWxsPSIjMzRBODUzIi8+PHBhdGggZD0iTTEyIDMuNzZjMC0wLjI0IDAtMC40OCAwLTAuNzJIMHYyLjY0aDMuMzZjMC42NC0xLjkyIDIuMjQtMy4zMiA0LjI0LTMuMzJ6IiBmaWxsPSIjRkJDMDQwIi8+PHBhdGggZD0iTTEyIDIwLjI0YzIuNTYgMCA0LjgtMS42OCA1LjY0LTQuMDhIMTB2LTIuNjRoMTIuMjRjMC4yNCAxLjEyIDAuNCAyLjI0IDAuNCAzLjQgMCAzLjY4LTEuNDggNi44OC00LjI0IDguNzJsLTMuMzYtMi42Yy0wLjY0IDAuNDgtMS4yOCAwLjgtMi4wNCAxLjA0eiIgZmlsbD0iIzM0QTg1MyIvPjwvc3ZnPg==" alt="Google Logo" class="google-icon">
        Daftar masuk dengan Google
    </a>
</body>
</html>