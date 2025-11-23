<?php
// Start the session (required for login/registration)
session_start();

// Include our database connection
include 'db_connection.php';

// Check if the form has been submitted (via POST method)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Get data from the form (Basic sanitation with trim)
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // 2. Hash the password (NEVER store plaintext passwords for security)
    // This is necessary for the VARCHAR(255) column we set up.
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        // 3. Prepare the insert query (PROTECTED against SQL Injection)
        $sql = "INSERT INTO users (user_name, user_email, user_password) VALUES (:username, :email, :password)";
        
        // Prepare the statement
        $stmt = $pdo->prepare($sql);
        
        // Bind the parameters
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        
        // 4. Execute the query
        $stmt->execute();
        
        // 5. If registration is successful, redirect to the login page
        header("Location: login.php?status=success");
        exit;

    } catch(PDOException $e) {
        // If an error occurs (e.g., username or email is already taken)
        // It will throw an error due to the 'UNIQUE' constraint.
        echo "Registration error: " . $e->getMessage();
    }
}

// ---- PHP Processing Part Ends, HTML Part Begins ----

// Include the header
include 'header.php'; 
?>

<h2>Register</h2>
<p>Create a new account to share your recipes.</p>

<form action="register.php" method="POST" class="form-container">
    <div>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <button type="submit">Register</button>
    </div>
</form>

<?php
// Include the footer
include 'footer.php'; 
?>