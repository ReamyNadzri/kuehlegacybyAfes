<?PHP
include('header_admin.php');

// Process form submission
if (isset($_POST['submit'])) {
    // Initialize arrays to store form data
    $ingredients = [];
    $steps = [];

    // Process ingredients
    if (isset($_POST['ingredients'])) {
        foreach ($_POST['ingredients'] as $ingredient) {
            if (!empty($ingredient)) {
                $ingredients[] = $ingredient;
            }
        }
    }

    // Process steps
    if (isset($_POST['steps'])) {
        foreach ($_POST['steps'] as $step) {
            if (!empty($step)) {
                $steps[] = $step;
            }
        }
    }

    // Get other form data
    $kuehName = $_POST['kuehName'];
    $kuehDesc = $_POST['kuehDesc'];
    $servings = $_POST['servings'];
    $cookingTime = $_POST['cookingTime'];

    // Here you can add your database insertion code
    // For example:
    /*
    $sql = "INSERT INTO KUEH (name, description, servings, cooking_time) 
            VALUES (:name, :desc, :servings, :time)";
    // Execute query and get last insert ID
    
    // Then insert ingredients
    foreach($ingredients as $ingredient) {
        $sql = "INSERT INTO KUEH_INGREDIENTS (kueh_id, ingredient) 
                VALUES (:kueh_id, :ingredient)";
    }
    
    // Then insert steps
    foreach($steps as $step) {
        $sql = "INSERT INTO KUEH_STEPS (kueh_id, step) 
                VALUES (:kueh_id, :step)";
    }
    */
}
?>

<body class="" style="background-color: #FFFAF0;">

    <link rel="stylesheet" href="style.css">
    <title>Legacy Kueh System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/product-sans" rel="stylesheet">


    <!--CONTENT START HERE-->
    <div class="container w-75">
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <!-- Existing image and title section -->
            <div class="row">
                <!-- Your existing image and title code -->
                <div class="col-12 col-md-4 my-4">
                    <img src="sources\kuehDetails\cekodokpisang.jpeg" class="img-fluid text-center rounded-3" alt="test image" style="min-height:380px; object-fit: cover;">
                </div>
                <div class="col-12 col-md gy-4">
                    <div class="col-12 bg-primary">
                        <input class="w-100 p-1 border-0 shadow-none fw-bolder fs-2" style="background-color: #FFFAF0;" type="text" name="kuehName" placeholder="Tajuk: Kuih Lapis Atok" required>
                    </div>
                    <!-- Rest of your title section -->
                    <div class="col-12">
                        <textarea class="w-100 p-1 border-0 shadow-none" name="kuehDesc" style="background-color: #FFFAF0;" placeholder="Share kisah resepi anda" required></textarea>
                    </div>
                </div>
            </div>

            <!-- Ingredients Section -->
            <div class="row mt-5">
                <div class="col-12 col-lg-3 col-md-6 py-3">
                    <div class="row mb-2 align-items-center">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h1 class="fw-bolder m-0">Ramuan</h1>
                            <button type="button" class="btn btn-primary ms-3" id="addIngredientButton"><i class="bi bi-plus"></i></button>
                        </div>
                        <div class="col mt-2">Sajian</div>
                        <div class="col">
                            <input type="text" name="servings" class="rounded" placeholder="2 Orang" required>
                        </div>
                    </div>
                    <div id="ingredientContainer">
                        <div class="input-group mb-2">
                            <button type="button" class="btn btn-outline-secondary border-0"><i class="bi bi-justify"></i></button>
                            <input type="text" name="ingredients[]" class="form-control" required>
                        </div>
                    </div>
                </div>


                <!-- Steps Section -->
                <div class="col-12 col-lg col-md-6 py-3">
                    <div class="row mb-2 align-items-center">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h1 class="fw-bolder m-0">Cara Memasak</h1>
                            <button type="button" class="btn btn-primary ms-3" id="addStepButton"><i class="bi bi-plus"></i></button>
                        </div>
                        <div class="col mt-2">Tempoh Masak</div>
                        <div class="col">
                            <input type="text" name="cookingTime" class="rounded" placeholder="1 jam 30 minit" required>
                        </div>
                    </div>
                    <div id="stepContainer">
                        <div class="input-group mb-2">
                            <button type="button" class="btn btn-outline-secondary border-0"><i class="bi bi-justify"></i></button>
                            <input type="text" name="steps[]" class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" name="submit" class="btn btn-primary mb-4">Save Recipe</button>
                </div>
            </div>

        </form>
    </div>

    <script>
        document.getElementById('addIngredientButton').addEventListener('click', function() {
            const container = document.getElementById('ingredientContainer');
            const newInput = document.createElement('div');
            newInput.className = 'input-group mb-2';
            newInput.innerHTML = `
            <button type="button" class="btn btn-outline-secondary border-0"><i class="bi bi-justify"></i></button>
            <input type="text" name="ingredients[]" class="form-control" required>
            <button type="button" class="btn btn-outline-danger delete-button"><i class="bi bi-x"></i></button>
        `;
            container.appendChild(newInput);

            newInput.querySelector('.delete-button').addEventListener('click', function() {
                newInput.remove();
            });
        });

        document.getElementById('addStepButton').addEventListener('click', function() {
            const container = document.getElementById('stepContainer');
            const newInput = document.createElement('div');
            newInput.className = 'input-group mb-2';
            newInput.innerHTML = `
            <button type="button" class="btn btn-outline-secondary border-0"><i class="bi bi-justify"></i></button>
            <input type="text" name="steps[]" class="form-control" required>
            <button type="button" class="btn btn-outline-danger delete-button"><i class="bi bi-x"></i></button>
        `;
            container.appendChild(newInput);

            newInput.querySelector('.delete-button').addEventListener('click', function() {
                newInput.remove();
            });
        });

        // Add event listeners to existing delete buttons
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function() {
                button.closest('.input-group').remove();
            });
        });
    </script>
</body>