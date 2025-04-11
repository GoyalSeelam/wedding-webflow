<?php
	include('Functions.php');
	$cookieUser = getCookieUser();
	$dbh = connectToDatabase();

	// Pagination setup
	$blogsPerPage = 5;
	$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
	$offset = ($page - 1) * $blogsPerPage;

	// Fetch blog posts with pagination
	$query = $dbh->prepare("
		SELECT BlogPost.BlogPostID, BlogPost.Title, BlogPost.Content, BlogPost.PostDate, Users.UserName 
		FROM BlogPost 
		INNER JOIN Users ON BlogPost.UserID = Users.UserID 
		ORDER BY BlogPost.PostDate DESC 
		LIMIT :limit OFFSET :offset
	");
	$query->bindValue(':limit', $blogsPerPage, PDO::PARAM_INT);
	$query->bindValue(':offset', $offset, PDO::PARAM_INT);
	$query->execute();
	$blogs = $query->fetchAll(PDO::FETCH_ASSOC);

	// Get total blog count for pagination
	$countQuery = $dbh->query("SELECT COUNT(*) FROM BlogPost");
	$totalBlogs = $countQuery->fetchColumn();
	$totalPages = ceil($totalBlogs / $blogsPerPage);
?>
<!DOCTYPE html>
<html>

    <head>
        <title>Public Blogs</title>
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
                    <a href="PublicView.php" class="bold">Blogs</a>
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

            <!-- Blog Content -->
            <div class="row" id="content">
                <h2>Public Blog Posts</h2>

                <?php if ($blogs): ?>
                    <table border="1">
                        <tr>
                            <th>User</th>
                            <th>Title</th>
                            <th>Blog Post</th>
                            <th>Date</th>
                        </tr>
                        <?php foreach ($blogs as $blog): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($blog['UserName']); ?></td>
                                <td>
                                    <a href="BlogDetail.php?BlogID=<?php echo $blog['BlogPostID']; ?>">
                                        <?php echo htmlspecialchars($blog['Title']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars(substr($blog['Content'], 0, 100)) . '...'; ?></td>
                                <td><?php echo htmlspecialchars($blog['PostDate']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p>No blog posts found.</p>
                <?php endif; ?>

                <!-- Pagination -->
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a class="pagination-button" href="PublicView.php?page=<?php echo $page - 1; ?>" aria-label="Previous Page">
                            &laquo; Previous
                        </a>
                    <?php endif; ?>

                    <span class="pagination-info">
                        Page<?php echo $page; ?> of <?php echo $totalPages; ?>
                    </span>

                    <?php if ($page < $totalPages): ?>
                        <a class="pagination-button" href="PublicView.php?page=<?php echo $page + 1; ?>" aria-label="Next Page">
                            Next &raquo;
                        </a>
                    <?php endif; ?>
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