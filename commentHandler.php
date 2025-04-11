<?php 
	include('Functions.php');
	// Trim and store input from POST
	$blogID = trim($_POST['BlogID']);
	$username = getCookieUser();

	// Check if POST data is set
	if (!isset($_POST['BlogID'])) {
		setCookieMessage("No blog specified");
		header("Location: BlogDetail.php?BlogID=" . urlencode($blogID));
		exit;
	}

	// Check if user is logged in
	if (!$username) {
		setCookieMessage("You must be logged in to post a comment");
		header("Location: BlogDetail.php?BlogID=" . urlencode($blogID));
		exit;
	}

	// Retrieve UserID for the logged-in user
	$dbh = connectToDatabase();
	$userID = getUserIDByUserName($dbh, $username);

	// Check if the comment is set
	if (!isset($_POST['comment']) || empty(trim($_POST['comment']))) {
		setCookieMessage("Comment cannot be empty.");
		header("Location: BlogDetail.php?BlogID=" . urlencode($blogID));
		exit;
	}

	// Trim the comment input
	$comment = trim($_POST['comment']);

	try {
		// Insert the comment into the Comments table
		$stmt = $dbh->prepare("INSERT INTO Comments (BlogID, UserID, Comment, CommentDate) VALUES (:blogID, :userID, :comment, CURRENT_TIMESTAMP)");
		$stmt->bindParam(':blogID', $blogID);
		$stmt->bindParam(':userID', $userID);
		$stmt->bindParam(':comment', $comment);
		
		if ($stmt->execute()) {
			setCookieMessage("Your comment has been added.");
		}
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
		setCookieMessage($e);
	}		

	// Redirect back to the blog detail page
	header("Location: BlogDetail.php?BlogID=" . urlencode($blogID));
	exit;
?>