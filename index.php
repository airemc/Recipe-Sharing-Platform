<?php
// Start session and database connection
session_start();
include 'db_connection.php';

// Prepare a query to get all recipes from the database.
// We will use JOIN to get the name of the user (from users)
// and the name of the category (from categories), not just their IDs.
$recipes = []; // Initialize as an empty array
try {
    $sql = "SELECT 
                recipes.recipe_id, 
                recipes.recipe_name, 
                recipes.picture,
                users.user_name, 
                categories.category_name
            FROM recipes
            JOIN users ON recipes.added_user_id = users.user_id
            JOIN categories ON recipes.category_id = categories.category_id
            ORDER BY recipes.recipe_id DESC"; // Show the newest recipes first

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // If there's a database error, just display a simple message
    echo "An error occurred while fetching recipes: " . $e->getMessage();
}

// Include the header
include 'header.php';
?>

<style>
.section-title{
    text-align: center;
    font-size: 30px;
    margin-bottom: 20px;
    color: rgb(27, 27, 44);

}
.search-bar {
    background-color: rgb(27, 27, 44);
    padding: 10px;
    text-align: center;
    border-radius: 4px;
    height: 200px;
    background-image: url('Black stone food background cooking ingredients top view free space for your text _ Premium Photo.jpeg');
    background-size: cover;
    background-position: center;
}
.search-bar h2 {
    color: #ffffff;
    margin-bottom: 15px;
    font-size: 24px;
}
.search-bar input[type="text"] {
    width: 500px;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}
.search-bar button {
    padding: 8px 12px;
    background-color: #ffffff;
    color: rgb(27, 27, 44);
    border: none;
    cursor: pointer;
    border-radius: 4px;
}
    /* Kartların grid yapısı (Aynı kalabilir veya güncellenebilir) */
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

<div class="search-bar">

    <h2>What do you want to eat today?</h2>
            <form action="search.php" method="GET">
                <input type="text" name="query" placeholder="Search for recipes..." required>
                <button type="submit">Search</button>
            </form>
</div>

<?php
// Show the success message if we were redirected from add_recipe.php
if (isset($_GET['status']) && $_GET['status'] == 'recipe_added') {
    // This is also an example of "inline CSS"
    echo '<p style="color: green; font-weight: bold;">Your recipe has been added successfully!</p>';
}
?>

<div class="recipe-list">
    <?php
    // If we have recipes from the database
    if (count($recipes) > 0):
        // Create a "card" for each recipe
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
        // If there are no recipes in the database
    ?>
        <p>No recipes have been added yet. Be the first to <a href="add_recipe.php">add one</a>!</p>
    <?php endif; ?>
</div>

<?php
// Include the footer
include 'footer.php';