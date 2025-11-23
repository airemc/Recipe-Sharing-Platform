<?php
// Session and database connection
session_start();
include 'db_connection.php';

// 1. Ensure the request is POST and the user is logged in
if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect unauthorized access to homepage
    exit;
}

// 2. Get the recipe_id from the form
$recipe_id = (int)$_POST['recipe_id'];
$user_id = $_SESSION['user_id'];

if ($recipe_id > 0) {
    try {
        // 3. SECURITY: Before deleting, double-check on the server-side
        // that this recipe actually belongs to this user. (Hiding the button is not enough)
        
        $sql_check = "SELECT added_user_id FROM recipes WHERE recipe_id = :recipe_id";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->bindParam(':recipe_id', $recipe_id, PDO::PARAM_INT);
        $stmt_check->execute();
        $recipe = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        // 4. If the recipe exists AND the owner is the logged-in user, delete it.
        if ($recipe && $recipe['added_user_id'] == $user_id) {
            
            // Delete operation
            $sql_delete = "DELETE FROM recipes WHERE recipe_id = :recipe_id";
            $stmt_delete = $pdo->prepare($sql_delete);
            $stmt_delete->bindParam(':recipe_id', $recipe_id, PDO::PARAM_INT);
            $stmt_delete->execute();
            
            // Redirect to homepage with success status
            header("Location: index.php?status=deleted");
            exit;
            
        } else {
            // The user tried to delete someone else's recipe.
            header("Location: index.php?status=delete_unauthorized");
            exit;
        }
        
    } catch (PDOException $e) {
        // Database error
        header("Location: index.php?status=delete_error");
        exit;
    }
}

// If the recipe_id was invalid
header("Location: index.php");
exit;
?>