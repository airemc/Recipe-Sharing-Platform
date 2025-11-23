<?php
// Start the session (already in header, but good practice here too)
session_start();

// Database connection
include 'db_connection.php';

// 1. SECURITY CHECK
// If the user is not logged in, redirect them to the login page.
// This must be at the very top of the script.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit; // Stop executing the script
}

$error_message = ''; // For error messages

// 2. FETCH CATEGORIES FROM DB (for the dropdown menu)
try {
    $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY category_name");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Could not load categories: " . $e->getMessage();
}


// 3. FORM PROCESSING (Was the form submitted?)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get data from the form
    $recipe_name = trim($_POST['recipe_name']);
    $ingredients = trim($_POST['ingredients']);
    $instructions = trim($_POST['instructions']);
    $category_id = $_POST['category_id'];
    
    // Get the logged-in user's ID from the session
    $user_id = $_SESSION['user_id'];
    
    $picture_path = NULL; // Default to NULL if no image is uploaded

    // Handle Image Upload
    if (isset($_FILES['recipe_image']) && $_FILES['recipe_image']['error'] == 0) {
        
        $target_dir = "uploads/"; 
        
        // Create a safe filename
        // This turns "my cake.jpg" into "uploads/605c75f04_my_cake.jpg"
        $safe_filename = preg_replace('/[^A-Za-z0-9_.-]/', '_', basename($_FILES["recipe_image"]["name"]));
        $target_file = $target_dir . uniqid() . '_' . $safe_filename;

        // Move the uploaded file to the 'uploads/' directory
        if (move_uploaded_file($_FILES["recipe_image"]["tmp_name"], $target_file)) {
            // If successful, set the file path to save in the database
            $picture_path = $target_file;
        } else {
            // If the move fails, set an error message
            $error_message = "Sorry, there was an error uploading your file.";
        }
    }
    
    try {
        // Add to Database (INSERT)
        $sql = "INSERT INTO recipes (recipe_name, ingredients, instructions, category_id, added_user_id, picture) 
                VALUES (:name, :ing, :inst, :cat_id, :user_id, :picture)";
        
        $stmt = $pdo->prepare($sql);
        
        // Bind the parameters
        $stmt->bindParam(':name', $recipe_name);
        $stmt->bindParam(':ing', $ingredients);
        $stmt->bindParam(':inst', $instructions);
        $stmt->bindParam(':cat_id', $category_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':picture', $picture_path);
        $stmt->execute();
        
        // If successful, redirect to the homepage
        header("Location: index.php?status=recipe_added");
        exit;
        
    } catch (PDOException $e) {
        $error_message = "Error adding recipe: " . $e->getMessage();
    }
}

include 'header.php'; ?>



<h2 class="form-title">ADD NEW RECIPE</h2>



<?php
// If there is an error, display it
if (!empty($error_message)) {
    echo '<p style="color: red;">' . $error_message . '</p>';
}
?>
<form class="form-container" id="recipeForm" action="add_recipe.php" method="POST" enctype="multipart/form-data" onsubmit="return validateRecipeForm()">
    <div>
        <label for="recipe_name">Recipe Name:</label>
        <input type="text" id="recipe_name" name="recipe_name">
    </div>
    
    <div>
        <label for="category_id">Category:</label>
        <select id="category_id" name="category_id">
            <option value="">-- Select a Category --</option>
            <?php
            // Dynamically populate categories with PHP
            if (isset($categories)) {
                foreach ($categories as $category) {
                    echo '<option value="' . $category['category_id'] . '">' 
                         . htmlspecialchars($category['category_name']) 
                         . '</option>';
                }
            }
            ?>
        </select>
    </div>

    <div>
        <label for="recipe_image">Recipe Image:</label>
        <input type="file" id="recipe_image" name="recipe_image" accept="image/jpeg, image/png">
        <small>Optional. (JPG or PNG files only)</small>
    </div>
    
    <div>
        <label for="ingredients">Ingredients:</label>
        <textarea id="ingredients" name="ingredients"></textarea>
    </div>
    
    <div>
        <label for="instructions">Instructions:</label>
        <textarea id="instructions" name="instructions"></textarea>
    </div>
    
    <div>
        <button type="submit">Add Recipe</button>
    </div>
</form>

<script>
    function validateRecipeForm() {
        // Get the values from the form elements
        var recipeName = document.getElementById('recipe_name').value;
        var category = document.getElementById('category_id').value;
        var ingredients = document.getElementById('ingredients').value;

        // Simple validation: Are the fields empty?
        if (recipeName.trim() === "" || category.trim() === "" || ingredients.trim() === "") {
            alert('Please fill in all required fields (Name, Category, Ingredients).');
            return false; // Prevent the form from being submitted
        }
        
        return true; // Form can be submitted
    }
</script>

<?php include 'footer.php'; ?>