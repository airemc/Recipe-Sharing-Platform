<?php
// Session and database connection
session_start();
include 'db_connection.php';

// 1. GET THE CATEGORY ID FROM THE URL
$category_id = 0;
if (isset($_GET['id'])) {
    $category_id = (int)$_GET['id']; // Force the ID to be an integer
}

// If the ID is invalid (0 or not set), redirect to the homepage
if ($category_id <= 0) {
    header("Location: index.php");
    exit;
}

// 2. FETCH THE CATEGORY INFO AND THE RECIPES IN THAT CATEGORY
$recipes = []; // To store the recipes
$category_name = "Category"; // Default title

try {
    // First, get the category name (to display in the title)
    $sql_cat_name = "SELECT category_name FROM categories WHERE category_id = :id";
    $stmt_cat_name = $pdo->prepare($sql_cat_name);
    $stmt_cat_name->bindParam(':id', $category_id, PDO::PARAM_INT);
    $stmt_cat_name->execute();
    $category = $stmt_cat_name->fetch(PDO::FETCH_ASSOC);
    
    if ($category) {
        $category_name = $category['category_name'];
    }

    // Now, fetch the recipes belonging to that category (SAME query as index.php + WHERE)
    $sql_recipes = "SELECT 
                        recipes.recipe_id, 
                        recipes.recipe_name,
                        recipes.picture,
                        users.user_name, 
                        categories.category_name
                    FROM recipes
                    JOIN users ON recipes.added_user_id = users.user_id
                    JOIN categories ON recipes.category_id = categories.category_id
                    WHERE recipes.category_id = :cat_id  /* <-- THE ONLY DIFFERENCE IS THIS LINE */
                    ORDER BY recipes.recipe_id DESC";

    $stmt_recipes = $pdo->prepare($sql_recipes);
    $stmt_recipes->bindParam(':cat_id', $category_id, PDO::PARAM_INT); // Bind the ID for the WHERE clause
    $stmt_recipes->execute();
    $recipes = $stmt_recipes->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// === HTML PART (Almost identical to index.php) ===
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category_name); ?> Recipes</title>
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        .recipe-list {
    display: grid;
    /* Tasarımdaki gibi 3 sütunlu bir yapı */
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px; /* Kartlar arası boşluk */
    margin-top: 20px;
}

/* YENİ KART TASARIMI */
.recipe-card {
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    overflow: hidden; /* Resmin köşelerini yuvarlatmak için */
    transition: transform 0.2s ease-in-out;
}

.recipe-card:hover {
    transform: translateY(-5px); /* Kartın üzerine gelince hafifçe yükselmesi */
}

/* Kart Resim Alanı */
.card-image-link {
    display: block;
    text-decoration: none;
}
.card-image {
    width: 100%;
    height: 200px; /* Tüm kart resimleri için sabit yükseklik */
    object-fit: cover; /* Resimleri kırp ve sığdır */
    display: block;
}
.card-image-placeholder {
    width: 100%;
    height: 200px;
    background-color: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #aaa;
    font-style: italic;
}

/* Kart İçerik Alanı (Başlık, Kategori) */
.card-content {
    padding: 20px;
}

.card-content h3 {
    margin-top: 0;
    margin-bottom: 10px;
    font-size: 1.4em;
}

.card-content h3 a {
    text-decoration: none;
    color: #2c3e50; /* Koyu Mavi Başlık */
}
.card-content h3 a:hover {
    color: #007bff; /* Hover Mavi */
}

.card-content p {
    font-size: 0.9em;
    color: #777;
    margin-bottom: 0;
    line-height: 1.5;
}
    </style>
</head>
<body>

    <?php
    // Top Navigation Bar
    include 'header.php'; 
    ?>

    <main> <h2>Recipes in: <?php echo htmlspecialchars($category_name); ?></h2>

        <div class="recipe-list">
            <?php
            if (count($recipes) > 0):
                foreach ($recipes as $recipe):
            ?>
                <div class="recipe-card">
                    <a href="recipe_detail.php?id=<?php echo $recipe['recipe_id']; ?>" class="card-image-link">
                        <?php if (!empty($recipe['picture'])): ?>
                            <img src="<?php echo htmlspecialchars($recipe['picture']); ?>" alt="<?php echo htmlspecialchars($recipe['recipe_name']); ?>" class="card-image">
                        <?php else: ?>
                            <div class="card-image-placeholder"><span>No Image</span></div>
                        <?php endif; ?>
                    </a>

                    <div class="card-content">
                        <h3>
                            <a href="recipe_detail.php?id=<?php echo $recipe['recipe_id']; ?>">
                                <?php echo htmlspecialchars($recipe['recipe_name']); ?>
                            </a>
                        </h3>
                        <p>
                            <strong>Category:</strong> <?php echo htmlspecialchars($recipe['category_name']); ?><br>
                            <strong>Added by:</strong> <?php echo htmlspecialchars($recipe['user_name']); ?>
                        </p>
                    </div>
                </div>
            <?php
                endforeach;
            else:
            ?>
                <p>No recipes found in this category yet.</p>
            <?php endif; ?>
        </div>

        <?php
        include 'footer.php'; 
        ?>
    </main>

</body>
</html>