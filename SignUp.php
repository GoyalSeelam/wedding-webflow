<?php // <--- do NOT put anything before this PHP tag
	include('Functions.php');
	$cookieMessage = getCookieMessage();
		$cookieUser = getCookieUser();
?>
<!DOCTYPE html>
<html>

	<head>
		<title>Sign Up</title>
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
					<a href="SignIn.php" class="login">Log in</a>
					<a href="SignUp.php" class="signup">Sign up</a>
				</div>
			</div>
			<div class="row" id="content">
				<?php if (!empty($cookieMessage)) echo "<p class='error'>$cookieMessage</p>"; ?>

				<!-- Sign Up Form -->
				<div class="form-container">
					<form action="AddUser.php" method="POST">
						<label for="username">Username:</label>
						<input type="text" id="username" name="username" required>

						<label for="firstname">First Name:</label>
						<input type="text" id="firstname" name="firstname" required>

						<label for="lastname">Last Name:</label>
						<input type="text" id="lastname" name="lastname" required>

						<label for="email">Email:</label>
						<input type="email" id="email" name="email" required>

						<button type="submit">Sign Up</button>
					</form>
				</div>
			</div>

			<!-- Footer -->
			<div class="row" id="footer">
				<h4>Goyal Seelam, Goyal.Seelam2405@gmail.com</h4>
			</div>

		</div>
	</body>
</html>