<?php

	include('Functions.php');
	$cookieMessage = getCookieMessage();
	$cookieUser = getCookieUser();

	// Connect to the database
	$dbh = connectToDatabase();

	// Initialize portfolio variables
	$aboutMe = '';
	$skills = '';
	$projects = '';
	$contact = '';
	$portfolioExists = false;

	// Check if the user is logged in
	if ($cookieUser != "") {
		// Fetch the UserID for the logged-in user
		$userID = getUserIDByUserName($dbh, $cookieUser);
		
		// Check if portfolio already exists for the user
		$portfolioQuery = $dbh->prepare("SELECT * FROM Portfolio WHERE UserID = :userid");
		$portfolioQuery->bindParam(':userid', $userID);
		$portfolioQuery->execute();
		$portfolio = $portfolioQuery->fetch(PDO::FETCH_ASSOC);
		
		// If portfolio exists, populate fields for editing
		if ($portfolio) {
			$aboutMe = htmlspecialchars($portfolio['AboutMe']);
			$skills = htmlspecialchars($portfolio['Skills']);
			$projects = htmlspecialchars($portfolio['Projects']);
			$contact = htmlspecialchars($portfolio['Contact']);
			$portfolioExists = true;
		}

		// Handle form submission
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$aboutMe = $_POST['aboutMe'];
			$skills = $_POST['skills'];
			$projects = $_POST['projects'];
			$contact = $_POST['contact'];

			if ($portfolioExists) {
				// Update existing portfolio
				$updateQuery = $dbh->prepare("UPDATE Portfolio SET AboutMe = :aboutMe, Skills = :skills, Projects = :projects, Contact = :contact WHERE UserID = :userid");
				$updateQuery->bindParam(':aboutMe', $aboutMe);
				$updateQuery->bindParam(':skills', $skills);
				$updateQuery->bindParam(':projects', $projects);
				$updateQuery->bindParam(':contact', $contact);
				$updateQuery->bindParam(':userid', $userID);
				$updateQuery->execute();
				$cookieMessage = "Portfolio updated successfully!";
			} else {
				// Insert new portfolio
				$insertQuery = $dbh->prepare("INSERT INTO Portfolio (UserID, AboutMe, Skills, Projects, Contact) VALUES (:userid, :aboutMe, :skills, :projects, :contact)");
				$insertQuery->bindParam(':userid', $userID);
				$insertQuery->bindParam(':aboutMe', $aboutMe);
				$insertQuery->bindParam(':skills', $skills);
				$insertQuery->bindParam(':projects', $projects);
				$insertQuery->bindParam(':contact', $contact);
				$insertQuery->execute();
				$cookieMessage = "Portfolio created successfully!";
			}

			// Refresh portfolio data after submission
			setCookieMessage($cookieMessage);
			header("Location: Portfolio.php");
			exit();
		}
	}
?>
<!DOCTYPE html>
<html>

	<head>
		<title>Manage Portfolio</title>
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
					<a href="Portfolio.php" class="bold">Portfolio</a>
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

			<!-- Content Section -->
			<div class="row" id="content">
				<p><?php echo $cookieMessage; ?></p>
				<div class="form-container">
				<form method="POST">
					<label for="aboutMe">About Me:</label>
					<textarea name="aboutMe" id="aboutMe" required><?php echo $aboutMe; ?></textarea>

					<label for="skills">Skills:</label>
					<textarea name="skills" id="skills" required><?php echo $skills; ?></textarea>

					<label for="projects">Projects:</label>
					<textarea name="projects" id="projects" required><?php echo $projects; ?></textarea>

					<label for="contact">Contact Information:</label>
					<textarea name="contact" id="contact" required><?php echo $contact; ?></textarea>

					<button type="submit"> <?php echo $portfolioExists ? 'Update Portfolio' : 'Create Portfolio'; ?></button>
				</form>
				</div>
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
