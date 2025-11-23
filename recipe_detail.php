<?php
// Session and database connection
session_start();
include 'db_connection.php';

// 1. Get the recipe ID from the URL
$recipe_id = 0;
if (isset($_GET['id'])) {
    $recipe_id = (int)$_GET['id'];
}
if ($recipe_id <= 0) {
    header("Location: index.php");
    exit;
}

// 2. Fetch the specific recipe from the database
$recipe = null;
try {
    
    $sql = "SELECT 
                recipes.recipe_id,
                recipes.recipe_name, 
                recipes.ingredients,
                recipes.instructions,
                recipes.picture,  
                recipes.added_user_id, 
                users.user_name, 
                categories.category_name
            FROM recipes
            JOIN users ON recipes.added_user_id = users.user_id
            JOIN categories ON recipes.category_id = categories.category_id
            WHERE recipes.recipe_id = :id"; 

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $recipe_id, PDO::PARAM_INT);
    $stmt->execute();
    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$reviews=[];
if($recipe){
    try{
        $sql_reviews ="SELECT 

        reviews.rating,
        reviews.comments,
        reviews.created_at,
        users.user_name
    FROM reviews
    JOIN users ON reviews.user_id=users.user_id
    WHERE reviews.recipe_id=:id
    ORDER BY reviews.created_at DESC";
        
        $stmt_reviews=$pdo->prepare($sql_reviews);
        $stmt_reviews->bindParam(':id',$recipe_id,PDO::PARAM_INT);
        $stmt_reviews->execute();
        $reviews=$stmt_reviews->fetchAll(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e){
        echo"Error: ".$e->getMessage();
}
}

// If the recipe is not found
if (!$recipe) {
    include 'header.php';
    echo '<div style="text-align: center; padding: 50px;"><h2>Recipe Not Found</h2><p>Sorry, the recipe you are looking for does not exist.</p><a href="index.php">Back to Homepage</a></div>';
    include 'footer.php';
    exit; // Stop the script
}

// Include the header
include 'header.php';
?>

<style>
    /* Main Container - No shadow/border, full width */
    .recipe-container {
        display: block;
        width: 100%;
        max-width: 900px; /* Narrow the content slightly */
        margin: 20px auto; /* Center it */
        background-color: #ffffff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border-radius: 12px;
        overflow: hidden; /* To round the image corners */

    }

    /* 1. Image Section */
    .recipe-header-image {
        width: 100%; /* Cover the container */
        height: 400px; /* Fixed height */
        object-fit: cover; /* Crop image but maintain aspect ratio */
        display: block;
        object-position: center; /* Center the image */
    }
    
    /* Placeholder for when there is no image */
    .recipe-no-image {
        width: 100%;
        height: 200px;
        background-color: #eee;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #888;
        font-style: italic;
    }

    /* 2. Main Content Section (Title and 2 columns) */
    .recipe-content {
        padding: 30px;
    }

    .recipe-content h2 {
        font-family: 'Georgia', serif; /* More elegant font */
        font-size: 2.5em; /* Big title */
        text-align: center;
        margin-top: 0;
        margin-bottom: 10px;
    }

    .recipe-meta {
        text-align: center;
        font-size: 0.9em;
        color: #777;
        margin-bottom: 30px;
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
    }
    .recipe-meta span {
        margin: 0 10px;
    }

    /* 3. Two-Column Layout (using Flexbox) */
    .recipe-columns {
        display: flex;
        flex-wrap: wrap; /* To stack on mobile */
        gap: 30px; /* Space between columns */
    }

    .recipe-ingredients,
    .recipe-procedure {
        flex: 1; /* Equal width 2 columns */
        min-width: 300px; /* Breakpoint for mobile */
    }

    .recipe-columns h3 {
        font-family: 'Arial', sans-serif;
        font-weight: bold;
        color: #333;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 8px;
        margin-top: 0;
        /* Mimic 'INGREDIENTS' and 'PROCEDURE' from the image */
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 1.1em;
    }
    
    /* List for Ingredients (instead of <pre>) */
    .recipe-ingredients ul {
        list-style-type: disc;
        padding-left: 20px;
        line-height: 1.8;
    }
    
    /* Numbered list for Instructions (instead of <pre>) */
    .recipe-procedure ol {
        padding-left: 20px;
        line-height: 1.8;
    }

    /* Admin Buttons */
    .recipe-actions {
        border-top: 1px solid #eee;
        padding-top: 20px;
        margin-top: 30px;
        display: flex; /* Put buttons side-by-side */
        gap: 10px;
    }
    .recipe-actions form,
    .recipe-actions a {
        margin: 0;
    }
    .btn-delete {
        background-color: #dc3545;
        color: white;
        padding: 10px 15px;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        font-size: 14px;
    }
    .btn-edit {
        display: inline-block;
        background-color: #007bff;
        color: white;
        padding: 10px 15px;
        text-decoration: none;
        border-radius: 5px;
        font-size: 14px;



    .reviews-container {
        max-width: 900px;
        margin: 30px auto;
    }
    .review-form-box {
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
    }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-group select, .form-group textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box; /* Önemli */
    }
    .btn-submit-review {
        background-color: #28a745;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    
    .reviews-list .review-item {
        border-bottom: 1px solid #eee;
        padding: 15px 0;
    }
    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }
    .review-header .stars { color: #f0c14b; font-size: 1.2em; }
    .review-comment { margin: 5px 0; }
    .review-date { font-size: 0.8em; color: #777; }    
    }
</style>
<div class="recipe-container">

    <?php if (!empty($recipe['picture'])): ?>
        <img src="<?php echo htmlspecialchars($recipe['picture']); ?>" alt="<?php echo htmlspecialchars($recipe['recipe_name']); ?>" class="recipe-header-image">
    <?php else: ?>
        <div class="recipe-no-image">
            <span>No Image Uploaded</span>
        </div>
    <?php endif; ?>

    <div class="recipe-content">
    
        <h2><?php echo htmlspecialchars($recipe['recipe_name']); ?></h2>
        
        <div class="recipe-meta">
            <span><strong>Category:</strong> <?php echo htmlspecialchars($recipe['category_name']); ?></span>
            |
            <span><strong>Added by:</strong> <?php echo htmlspecialchars($recipe['user_name']); ?></span>
        </div>
        
        <div class="recipe-columns">
        
            <div class="recipe-ingredients">
                <h3>Ingredients</h3>
                <ul>
                    <?php
                    // Split the plain text by newlines and put each in an <li>
                    $ingredients_list = explode("\n", trim($recipe['ingredients']));
                    foreach ($ingredients_list as $item) {
                        if (!empty(trim($item))) {
                            echo '<li>' . htmlspecialchars(trim($item)) . '</li>';
                        }
                    }
                    ?>
                </ul>
            </div>
            
            <div class="recipe-procedure">
                <h3>Procedure</h3>
                <ol>
                    <?php
                    // Split the plain text by newlines and put each in an <li>
                    $instructions_list = explode("\n", trim($recipe['instructions']));
                    foreach ($instructions_list as $step) {
                        if (!empty(trim($step))) {
                            echo '<li>' . htmlspecialchars(trim($step)) . '</li>';
                        }
                    }
                    ?>
                </ol>
            </div>
            
        </div> <?php
        // If the user is logged in AND they are the owner of this recipe
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $recipe['added_user_id']):
        ?>
            <div class="recipe-actions">
                
                <form action="delete_recipe.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this recipe? This cannot be undone.');">
                    <input type="hidden" name="recipe_id" value="<?php echo $recipe_id; ?>">
                    <button type="submit" class="btn-delete">
                        Delete Recipe
                    </button>
                </form>
                
                <a href="edit_recipe.php?id=<?php echo $recipe_id; ?>" class="btn-edit">
                    Edit Recipe
                </a>
            </div>
        <?php
        endif; 
        ?>

    </div> </div>

    <div class="reviews-container">
    <h2>Reviews & Ratings</h2>

    <?php if (isset($_SESSION['user_id'])): // Kullanıcı giriş yapmışsa formu göster ?>
        <div class="review-form-box">
            <h3>Leave a Review</h3>
            <form action="submit_review.php" method="POST">
                
                <input type="hidden" name="recipe_id" value="<?php echo $recipe_id; ?>">
                
                <div class="form-group">
                    <label for="rating">Rating (1-5):</label>
                    <select name="rating" id="rating" required>
                        <option value="">Select a rating</option>
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="comment">Comment:</label>
                    <textarea name="comment" id="comment" rows="4"></textarea>
                </div>
                <button type="submit" class="btn-submit-review">Submit Review</button>
            </form>
        </div>
    <?php else: // Kullanıcı giriş yapmamışsa ?>
        <p>You must <a href="login.php">log in</a> to leave a review.</p>
    <?php endif; ?>


    <div class="reviews-list">
        <h3>All Reviews (<?php echo count($reviews); ?>)</h3>
        
        <?php if (count($reviews) > 0): // Yorum varsa ?>
            <?php foreach ($reviews as $review): // Her bir yorum için döngü ?>
                <div class="review-item">
                    <div class="review-header">
                        <strong><?php echo htmlspecialchars($review['user_name']); ?></strong>
                        <span class="stars">
                            <?php for ($i = 0; $i < $review['rating']; $i++): ?>★<?php endfor; ?>
                            <?php for ($i = $review['rating']; $i < 5; $i++): ?>☆<?php endfor; ?>
                        </span>
                    </div>
                    <p class="review-comment"><?php echo nl2br(htmlspecialchars($review['comments'])); ?></p>
                    <small class="review-date"><?php echo date('F j, Y, g:i a', strtotime($review['created_at'])); ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: // Hiç yorum yoksa ?>
            <p>No reviews yet. Be the first to leave one!</p>
        <?php endif; ?>
    </div>
</div>
    
     <?php
// Include the footer
include 'footer.php';