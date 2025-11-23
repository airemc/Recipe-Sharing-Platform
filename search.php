<?php
// Session and database connection
session_start();
include 'db_connection.php';

// 1. Get the search query from the URL
$search_query = '';
if (isset($_GET['query'])) {
    $search_query = trim($_GET['query']);
}

// 2. Search the database
$results = []; // To store the results
if (!empty($search_query)) {
    try {
        // Use the SQL LIKE operator to search
        // The % sign means "any text".
        // We are searching in both the recipe_name and the ingredients.
        $sql = "SELECT 
                    recipes.recipe_id, 
                    recipes.recipe_name, 
                    recipes.picture,
                    users.user_name, 
                    categories.category_name
                FROM recipes
                JOIN users ON recipes.added_user_id = users.user_id
                JOIN categories ON recipes.category_id = categories.category_id
                WHERE recipes.recipe_name LIKE :query 
                   OR recipes.ingredients LIKE :query
                ORDER BY recipes.recipe_id DESC";
        
        $stmt = $pdo->prepare($sql);
        
        // Bind the parameter (to prevent SQL Injection)
        // We must add the % signs inside the variable.
        $search_term = '%' . $search_query . '%';
        $stmt->bindParam(':query', $search_term);
        
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "Search error: " . $e->getMessage();
    }
}

// Include the header
include 'header.php';

// We add the same CSS styles from index.php here
// (or you could move these styles to style.css)
?>
<style>
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

<h2>Search Results for: "<?php echo htmlspecialchars($search_query); ?>"</h2>

<div class="recipe-list">
    <?php
    // If results were found
    if (count($results) > 0):
        // Display each result as a "card"
        foreach ($results as $recipe):
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
        // If the search query was not empty BUT no results were found
    ?>
        <p>No recipes found matching your search. Try a different term.</p>
    <?php endif; ?>
</div>

<?php
include 'footer.php';
?>