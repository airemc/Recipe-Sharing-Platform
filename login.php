<?php
// Start the session
session_start();

// Database connection
include 'db_connection.php';

$error_message = ''; // A variable to store error messages

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Get data from the form
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    try {
        // 2. Search for the user in the database (by username)
        $sql = "SELECT user_id, user_name, user_password FROM users WHERE user_name = :username";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        // 3. Find the user and fetch their data
        // fetch() -> Fetches the matching row if one exists, or returns false.
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // 4. Does the user exist? AND is the password correct?
        // password_verify() -> Compares the password entered by the user ($password)
        // with the hashed password from the database ($user['user_password']).
        if ($user && password_verify($password, $user['user_password'])) {
            
            // 5. LOGIN SUCCESSFUL
            // Save user information in session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];
            
            // Redirect the user to the homepage
            header("Location: index.php");
            exit;
            
        } else {
            // 6. LOGIN FAILED
            $error_message = "Invalid username or password.";
        }
        
    } catch(PDOException $e) {
        $error_message = "Login error: " . $e->getMessage();
    }
}

// ---- PHP Processing Part Ends, HTML Part Begins ----

include 'header.php';
?>

<h2>Login</h2>
<p>Log in to your account to add recipes.</p>

<?php
// If there is an error, display it
if (!empty($error_message)) {
    echo '<p style="color: red;">' . $error_message . '</p>';
}

// If redirected from the register page, show success message
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    echo '<p style="color: green;">Registration successful! You can now log in.</p>';
}
?>

<form action="login.php" method="POST" class="form-container">
    <div>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <button type="submit">Login</button>
    </div>
</form>

<?php
include 'footer.php';
?>
