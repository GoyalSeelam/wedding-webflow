<?php 
	include('Functions.php');
	$cookieMessage = getCookieMessage();
	$cookieUser = getCookieUser();

	// Connect to the database
	$dbh = connectToDatabase();

	// Default values for stats
	$totalBlogs = 0;
	$totalComments = 0;

	if ($cookieUser != "") {
		$userID = getUserIDByUserName($dbh, $cookieUser);

		// Count total blogs posted by the user
		$blogQuery = $dbh->prepare("SELECT COUNT(*) FROM BlogPost WHERE UserID = :userID");
		$blogQuery->bindParam(':userID', $userID);
		$blogQuery->execute();
		$totalBlogs = $blogQuery->fetchColumn();

		// Count total comments received on user's blogs
		$commentQuery = $dbh->prepare("
			SELECT COUNT(*) FROM Comments 
			WHERE BlogID IN (SELECT BlogPostID FROM BlogPost WHERE UserID = :userID)
		");
		$commentQuery->bindParam(':userID', $userID);
		$commentQuery->execute();
		$totalComments = $commentQuery->fetchColumn();

		// Fetch users recent blog posts
		$recentBlogsQuery = $dbh->prepare("
			SELECT Title, BlogPostID, PostDate FROM BlogPost 
			WHERE UserID = :userID ORDER BY PostDate DESC LIMIT 5
		");
		$recentBlogsQuery->bindParam(':userID', $userID);
		$recentBlogsQuery->execute();
		$recentBlogs = $recentBlogsQuery->fetchAll(PDO::FETCH_ASSOC);
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Dashboard</title>
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
					<a href="Dashboard.php" class="bold">Dashboard</a>
					<a href="Portfolio.php">Portfolio</a>
					<a href="Blog.php">My Blogs</a>
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

			<!-- Main Content -->
			<div id="content">
				<p><?php echo $cookieMessage; ?></p>
				<h2>Dashboard Overview</h2>
				<p>Welcome back! Here's a snapshot of your activity.</p>

				<div class="dashboard-stats">
					<div class="stat-box">
						<h3>Total Blogs</h3>
						<p><?php echo $totalBlogs; ?></p>
					</div>
					<div class="stat-box">
						<h3>Comments Received</h3>
						<p><?php echo $totalComments; ?></p>
					</div>
				</div>

				<h3>Your Recent Blogs</h3>
				<?php if ($totalBlogs > 0): ?>
					<ul class="recent-blogs">
						<?php foreach ($recentBlogs as $blog): ?>
							<li>
								<a href="BlogDetail.php?BlogID=<?php echo $blog['BlogPostID']; ?>">
									<?php echo htmlspecialchars($blog['Title']); ?>
								</a> 
								<em>(<?php echo htmlspecialchars($blog['PostDate']); ?>)</em>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<p>You haven’t posted any blogs yet. Start writing today!</p>
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
