<?php
    include('Functions.php');

    // Check if username is provided
    if (!isset($_POST['username'])) {
        setCookieMessage("Error: Username not provided.");
        exit();
    }

    // Connect to database
    $dbh = connectToDatabase();

    // Trim input value
    $UserName = trim($_POST['username']);

    // Check if username exists in database
    $statement = $dbh->prepare('SELECT * FROM Users WHERE UserName = ? COLLATE NOCASE;');
    $statement->bindValue(1, $UserName);
    $statement->execute();

    if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        // Set cookie to store logged-in user
        setcookie("CookieUser", $UserName, time() + 3600, "/"); // Expires in 1 hour
        setCookieMessage("Hola! $UserName.");

        // Redirect to dashboard or homepage
        redirect("Dashboard.php");
    } else {
        // User does not exist
        setCookieMessage("The username '$UserName' does not exist. Please sign up.");
        redirect("SignIn.php");
    }
?>
