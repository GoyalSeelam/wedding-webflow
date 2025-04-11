<?php
	include('Functions.php');
	$cookieMessage = getCookieMessage();
	$cookieUser = getCookieUser();

	// Connect to the database
	$dbh = connectToDatabase();

	// Initialize variables for blog posting and editing
	$title = '';
	$content = '';
	$blogs = [];
	$editMode = false;
	$editBlogID = null;

	// Check if the user is logged in
	if ($cookieUser != "") {
		$dbh = connectToDatabase();
		$userID = getUserIDByUserName($dbh, $cookieUser);
		

		// Handle new blog post submission
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_blog'])) {
			$title = trim($_POST['title']);
			$content = trim($_POST['content']);
			$postDate = date('Y-m-d H:i:s');

			if (!empty($title) && !empty($content)) {
				// Insert new blog post into the database
				$insertQuery = $dbh->prepare("INSERT INTO BlogPost (UserID, Title, Content, PostDate) VALUES (:userid, :title, :content, :postDate)");
				$insertQuery->bindParam(':userid', $userID);
				$insertQuery->bindParam(':title', $title);
				$insertQuery->bindParam(':content', $content);
				$insertQuery->bindParam(':postDate', $postDate);
				$insertQuery->execute();
				setCookieMessage("Blog post created successfully!");
				header("Location: Blog.php");
				exit();
			}
		}

		// Handle edit request
		if (isset($_GET['edit'])) {
			$editBlogID = intval($_GET['edit']);
			$editQuery = $dbh->prepare("SELECT * FROM BlogPost WHERE BlogPostID = :blogID AND UserID = :userID");
			$editQuery->bindParam(':blogID', $editBlogID, PDO::PARAM_INT);
			$editQuery->bindParam(':userID', $userID, PDO::PARAM_INT);
			$editQuery->execute();
			$blogToEdit = $editQuery->fetch(PDO::FETCH_ASSOC);

			if ($blogToEdit) {
				$title = $blogToEdit['Title'];
				$content = $blogToEdit['Content'];
				$editMode = true;
			}
		}

		// Handle blog update
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_blog'])) {
			$editBlogID = intval($_POST['blog_id']);
			$title = trim($_POST['title']);
			$content = trim($_POST['content']);

			if (!empty($title) && !empty($content)) {
				$updateQuery = $dbh->prepare("UPDATE BlogPost SET Title = :title, Content = :content WHERE BlogPostID = :blogID AND UserID = :userID");
				$updateQuery->bindParam(':title', $title);
				$updateQuery->bindParam(':content', $content);
				$updateQuery->bindParam(':blogID', $editBlogID, PDO::PARAM_INT);
				$updateQuery->bindParam(':userID', $userID, PDO::PARAM_INT);
				$updateQuery->execute();
				setCookieMessage("Blog post updated successfully!");
				header("Location: Blog.php");
				exit();
			}
		}

		// Handle delete request
		if (isset($_GET['delete'])) {
			$deleteBlogID = intval($_GET['delete']);
			$deleteQuery = $dbh->prepare("DELETE FROM BlogPost WHERE BlogPostID = :blogID AND UserID = :userID");
			$deleteQuery->bindParam(':blogID', $deleteBlogID, PDO::PARAM_INT);
			$deleteQuery->bindParam(':userID', $userID, PDO::PARAM_INT);
			$deleteQuery->execute();
			setCookieMessage("Blog post deleted successfully!");
			header("Location: Blog.php");
			exit();
		}

		// Fetch the user's existing blog posts
		$blogsQuery = $dbh->prepare("SELECT * FROM BlogPost WHERE UserID = :userid ORDER BY PostDate DESC");
		$blogsQuery->bindParam(':userid', $userID);
		$blogsQuery->execute();
		$blogs = $blogsQuery->fetchAll(PDO::FETCH_ASSOC);
	
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Manage Blogs</title>
		<link rel="stylesheet" type="text/css" href="styles.css">
	</head>
	<body>
		<div class="container">

			<div class="navbar">
				<div class="nav-left">
					<img src="images/logo.jpg" alt="Logo" class="logo">
					<a href="#" class="bold">BlogSphere</a>
				</div>
				<div class="nav-center">
					<a href="Homepage.php">HomePage</a>
					<a href="Dashboard.php">Dashboard</a>
					<a href="Portfolio.php">Portfolio</a>
					<a href="Blog.php" class="bold">My Blogs</a>
				</div>
				<div class="nav-right">
					<?php if ($cookieUser == ""): ?>
						<a href="SignIn.php" class="login">Log in</a>
						<a href="SignUp.php" class="signup">Sign up</a>
					<?php else: ?>
						<span class="user-info">Welcome, <?php echo htmlspecialchars($cookieUser); ?>!</span>
						<div class="dropdown">
							<button class="dropbtn">▼</button>
							<div class="dropdown-content">
								<a href="LogOutUser.php" class="logout">Sign Out</a>
							</div>
						</div>
						
					<?php endif; ?>
				</div>
			</div>

			<!-- Content Section -->
			<div class="row" id="content">
				<p><?php echo $cookieMessage; ?></p>

				<!-- Blog Post Form -->
				<div class="form-container">
				<form method="POST">
					<input type="hidden" name="blog_id" value="<?php echo $editMode ? $editBlogID : ''; ?>">
					
					<label for="title">Blog Title:</label>
					<input type="text" name="title" id="title" value="<?php echo htmlspecialchars($title); ?>" required>

					<label for="content">Blog Content:</label>
					<textarea name="content" id="blog-content" required><?php echo htmlspecialchars($content); ?></textarea>

					<?php if ($editMode): ?>
						<button type="submit" name="update_blog">Update Blog</button>
					<?php else: ?>
						<button type="submit" name="submit_blog">Post Blog</button>
					<?php endif; ?>
				</form>
				</div>

				<h3>Your Blog Posts</h3>

				<?php if (count($blogs) > 0): ?>
					<table border="1">
						<tr>
							<th>Title</th>
							<th>Blog Post</th>
							<th>Date</th>
							<th>Actions</th>
						</tr>
						<?php foreach ($blogs as $blog): ?>
							<tr>
								<td>
									<a href="BlogDetail.php?BlogID=<?php echo $blog['BlogPostID']; ?>">
										<?php echo htmlspecialchars($blog['Title']); ?>
									</a>
								</td>
								<td><?php echo htmlspecialchars(substr($blog['Content'], 0, 100)) . '...'; ?></td>
								<td><?php echo htmlspecialchars($blog['PostDate']); ?></td>
								<td>
									<a href="Blog.php?edit=<?php echo $blog['BlogPostID']; ?>" class="edit-btn">Edit</a>
									<a href="Blog.php?delete=<?php echo $blog['BlogPostID']; ?>" class="delete-btn" 
										onclick="return confirm('Are you sure you want to delete this blog?');">
										Delete
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
				<?php else: ?>
					<p>No blog posts found. Start by creating one!</p>
				<?php endif; ?>

			</div>

			<!-- Footer -->
			<div class="row" id="footer">
			   Goyal Seelam, Goyal.Seelam2405@gmail.com
			</div>

		</div>
	</body>
</html>
