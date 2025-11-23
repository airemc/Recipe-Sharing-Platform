<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once 'db_connection.php';

$categories_list = []; // Empty array to store categories
try {
    // $pdo variable should come from db_connection.php
    if (isset($pdo)) {
        $sql_cat = "SELECT category_id, category_name FROM categories ORDER BY category_name";
        $stmt_cat = $pdo->prepare($sql_cat);
        $stmt_cat->execute();
        $categories_list = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    // If fetching categories fails, don't break the page, just leave them blank.
    // error_log("Category fetch error: " . $e->getMessage()); 
}

// This function returns the correct Font Awesome icon class based on the category name
function getCategoryIcon($categoryName) {
    $categoryNameLower = strtolower($categoryName); // Convert name to lowercase

    switch ($categoryNameLower) {
        case 'soups':
            return 'fa-solid fa-bowl-food';
        case 'main courses':
            return 'fa-solid fa-utensils';
        case 'desserts':
            return 'fa-solid fa-cake-candles';
        case 'salads':
            return 'fa-solid fa-leaf';
        case 'appetizers':
            return 'fa-solid fa-pepper-hot';
        case 'breakfast':
            return 'fa-solid fa-bacon';
        // You can add more categories here based on your database
        
        default: // If no matching category is found
            return 'fa-solid fa-tag'; // Return a default icon
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Sharing Site</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <header>
        <nav>
        <a href="index.php"><i class="fa-solid fa-house"></i> Home</a> <?php foreach ($categories_list as $category): ?>
            <?php
            // Get the correct icon for this category name
            $iconClass = getCategoryIcon($category['category_name']);
            ?>
            <a href="category.php?id=<?php echo $category['category_id']; ?>">
                <i class="<?php echo $iconClass; ?>"></i> 
                <?php echo htmlspecialchars($category['category_name']); ?>
            </a>
        <?php endforeach; ?>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="add_recipe.php"><i class="fa-solid fa-plus"></i> Add Recipe</a>
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        <?php else: ?>
            <a href="login.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
            <a href="register.php"><i class="fa-solid fa-user-plus"></i> Register</a>
        <?php endif; ?>
    </nav>
        





    </header>
    
    <main> 
