<?php 
	include('Functions.php');
	$cookieMessage = getCookieMessage();
	$cookieUser = getCookieUser();
    
	// Connect to the database
	$dbh = connectToDatabase();

	// Fetch the latest blog post
	$query = $dbh->prepare("
		SELECT BlogPost.Title, BlogPost.Content, BlogPost.PostDate, Users.UserName , BlogPost.BlogPostID
		FROM BlogPost 
		INNER JOIN Users ON BlogPost.UserID = Users.UserID 
		ORDER BY BlogPost.PostDate DESC 
		LIMIT 1
	");
	$query->execute();
	$latestPost = $query->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>CSE4IFU - Homepage</title>
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
					<a href="Homepage.php" class="bold">HomePage</a>
					<a href="PublicView.php">Blogs</a>
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
								<a href="Dashboard.php" class="dashboard">Dashboard</a>
								<a href="LogOutUser.php" class="logout">Sign Out</a>
							</div>
						</div>
						
					<?php endif; ?>
				</div>
			</div>

			<center><p><?php echo $cookieMessage; ?></p></center>

			<!-- Welcome Section -->
			<div class="row" id="welcome-section">
				<img src="images/blogging_concept.jpg" alt="Blogging concept" class="welcome-image">
				<div class="welcome-text">
					<h2>Welcome to CSE4IFU Blogging Hub</h2>
					<p>
						Explore insightful blogs, share your thoughts, and be part of an engaging community. 
						Whether you're here to read, write, or discuss – this is your space to grow. 
						Let's create, connect, and inspire together!
					</p>
				</div>
			</div>

			<!-- Content Section -->
			<div class="row" id="content">

				<h3>Latest Blog Post</h3>

				<?php if ($latestPost): ?>
					<table border="1">
						<tr>
							<th>Author</th>
							<th>Title</th>
							<th>Blog Post</th>
							<th>Date</th>
						</tr>
						<tr>
							<td><?php echo htmlspecialchars($latestPost['UserName']); ?></td>
							<td>
								<a href="BlogDetail.php?BlogID=<?php echo $latestPost['BlogPostID']; ?>">
									<?php echo htmlspecialchars($latestPost['Title']); ?>
								</a>
							</td>
							<td><?php echo htmlspecialchars(substr($latestPost['Content'], 0, 100)) . '...'; ?></td>
							<td><?php echo htmlspecialchars($latestPost['PostDate']); ?></td>
						</tr>
					</table>
					
					<!-- View All Blogs Button -->
					<div class="button-container">
						<a href="PublicView.php" class="view-blogs-btn">View All Blogs</a>
					</div>
				<?php else: ?>
					<p>No blog posts found.</p>
				<?php endif; ?>
			</div>

			<!-- Footer -->
			<div class="row" id="footer">
				<h4>
					Goyal Seelam, Goyal.Seelam2405@gmail.com
				</h4>
			</div>

		</div>

	</body>
</html>
