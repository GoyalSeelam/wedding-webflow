<?php

// Database connection function
function connectToDatabase()
{
    $dbh = new PDO("sqlite:./database/Blog.db");
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    return $dbh;
}

// Redirect to another page
function redirect($newURL)
{
    header("Location: $newURL");
    die();
}

// Set a cookie message
function setCookieMessage($cookieMessage)
{
    setcookie("CookieMessage", $cookieMessage, time() + 3600, "/"); // Expires in 1 hour
}

// Set the user cookie with a 30-day expiration
function setCookieUser($cookieUser)
{
    setcookie("CookieUser", $cookieUser, time() + 60 * 60 * 24 * 30, "/"); // Expires in 30 days
}

// Retrieve the cookie message
function getCookieMessage()
{
    if (isset($_COOKIE['CookieMessage'])) {
        $message = $_COOKIE['CookieMessage'];
        deleteCookie("CookieMessage");
        return makeOutputSafe($message);
    } else {
        return "";
    }
}

// Retrieve the user cookie
function getCookieUser()
{
    if (isset($_COOKIE['CookieUser'])) {
        $user = $_COOKIE['CookieUser'];
        return makeOutputSafe($user);
    } else {
        return "";
    }
}

// Delete a specific cookie
function deleteCookie($cookieName)
{
    setcookie($cookieName, "", time() - 3600, "/"); // Expiry time in the past
}

// Safe output for user input
function makeOutputSafe($unsafeString)
{
    return htmlspecialchars($unsafeString, ENT_QUOTES, "UTF-8");
}

// Function to get UserID by UserName
function getUserIDByUserName($dbh, $username)
{
    try {
        $stmt = $dbh->prepare("SELECT UserID FROM Users WHERE Upper(UserName) = Upper(?)");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return $user['UserID'];
        } else {
            return null; // Return null if user is not found
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

?>
