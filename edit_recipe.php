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

// take the recipe ID from the URL
$recipe_id = (int)$_GET['id']; 
if ($recipe_id <= 0) {
    header("Location: index.php"); 
    exit;
}

try {
    // take the owner of the recipe
    $sql_fetch = "SELECT * FROM recipes WHERE recipe_id = :id";
    $stmt_fetch = $pdo->prepare($sql_fetch);
    $stmt_fetch->bindParam(':id', $recipe_id, PDO::PARAM_INT);
    $stmt_fetch->execute();
    $recipe = $stmt_fetch->fetch(PDO::FETCH_ASSOC);

    if (!$recipe) {
        header("Location: index.php"); // Recipe not found
        exit;
    }

    //SECURITY CHECK - OWNERSHIP
    // Did the logged-in user add this recipe?
    if ($recipe['added_user_id'] != $_SESSION['user_id']) {
        header("Location: index.php?status=edit_unauthorized"); // Unauthorized
        exit;
    }

} catch (PDOException $e) {
    die("Error fetching recipe: " . $e->getMessage());
}

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
    $posted_recipe_id = (int)$_POST['recipe_id'];     
    
    $picture_path = $recipe['picture']; // Current image path

    // Handle Image Upload
    if (isset($_FILES['recipe_image']) && $_FILES['recipe_image']['error'] == 0) {
        
        $target_dir = "uploads/";
        $safe_filename = preg_replace('/[^A-Za-z0-9_.-]/', '_', basename($_FILES["recipe_image"]["name"]));
        $target_file = $target_dir . uniqid() . '_' . $safe_filename;
        
        // move the uploaded file to the 'uploads/' directory
        if (move_uploaded_file($_FILES["recipe_image"]["tmp_name"], $target_file)) {
            // If successful, update the $picture_path variable with the NEW file path
            $picture_path = $target_file;

        } else {
            $error_message = "Sorry, there was an error uploading your new file.";
        }
    }
    if ($posted_recipe_id == $recipe_id && $recipe['added_user_id'] == $_SESSION['user_id']) {
        
        try {
            // Veritabanını Güncelle (UPDATE)
            $sql_update = "UPDATE recipes SET 
                                recipe_name = :name, 
                                ingredients = :ing, 
                                instructions = :inst, 
                                category_id = :cat_id,
                                picture = :picture
                            WHERE recipe_id = :id";
            
            $stmt_update = $pdo->prepare($sql_update);
            
            $stmt_update->bindParam(':name', $recipe_name);
            $stmt_update->bindParam(':ing', $ingredients);
            $stmt_update->bindParam(':inst', $instructions);
            $stmt_update->bindParam(':cat_id', $category_id);
            $stmt_update->bindParam(':id', $recipe_id);
            $stmt_update->bindParam(':picture', $picture_path);

            $stmt_update->execute();
            
            // Başarılı olursa detay sayfasına geri yönlendir
            header("Location: recipe_detail.php?id=" . $recipe_id . "&status=updated");
            exit;
            
        } catch (PDOException $e) {
            $error_message = "Error updating recipe: " . $e->getMessage();
        }
    } else {
        $error_message = "Unauthorized update attempt.";
    }
}

include 'header.php'; ?>



<h2>Edit Recipe</h2> <?php if (!empty($error_message)) {
    echo '<p style="color: red;">' . $error_message . '</p>';
} ?>

<form class="form-container" id="recipeForm" action="edit_recipe.php?id=<?php echo $recipe_id; ?>" method="POST" enctype="multipart/form-data" onsubmit="return validateRecipeForm()">
    
    <input type="hidden" name="recipe_id" value="<?php echo $recipe_id; ?>">
    
    <div>
        <label for="recipe_name">Recipe Name:</label>
        <input type="text" id="recipe_name" name="recipe_name" value="<?php echo htmlspecialchars($recipe['recipe_name']); ?>">
    </div>
    
    <div>
        <label for="category_id">Category:</label>
        <select id="category_id" name="category_id">
            <option value="">-- Select a Category --</option>
            <?php
            foreach ($categories as $category) {
                // Mevcut kategori hangisiyse onu 'selected' yap
                $selected = ($category['category_id'] == $recipe['category_id']) ? 'selected' : '';
                echo '<option value="' . $category['category_id'] . '" ' . $selected . '>' 
                     . htmlspecialchars($category['category_name']) 
                     . '</option>';
            }
            ?>
        </select>
    </div>
    <?php if (!empty($recipe['picture'])): ?>
    <div>
        <label>Current Image:</label>
        <img src="<?php echo htmlspecialchars($recipe['picture']); ?>" alt="Recipe Image" style="width: 150px; height: auto; border-radius: 4px;">
    </div>
    <?php endif; ?>
    <div>
        <label for="recipe_image">Change Image (Optional):</label>
        <input type="file" id="recipe_image" name="recipe_image" accept="image/jpeg, image/png">
        <small>JPG or PNG files only. Leave blank to keep the current image.</small>
    </div>
    
    <div>
        <label for="ingredients">Ingredients:</label>
        <textarea id="ingredients" name="ingredients"><?php echo htmlspecialchars($recipe['ingredients']); ?></textarea>
    </div>
    
    <div>
        <label for="instructions">Instructions:</label>
        <textarea id="instructions" name="instructions"><?php echo htmlspecialchars($recipe['instructions']); ?></textarea>
    </div>
    
    <div>
        <button type="submit">Update Recipe</button>
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
<?php
include 'footer.php';
?>