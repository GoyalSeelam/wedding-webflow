<?php
    include('Functions.php');
    $cookieUser = getCookieUser();
    $cookieMessage = getCookieMessage();
    $dbh = connectToDatabase();

    // Check if BlogID is set in the URL
    if (!isset($_GET['BlogID']) || !is_numeric($_GET['BlogID'])) {
        redirect("publicview.php");
    }

    // Get BlogID from URL
    $BlogID = intval($_GET['BlogID']);

    // Fetch blog post details
    $query = $dbh->prepare("
        SELECT BlogPost.Title, BlogPost.Content, BlogPost.PostDate, Users.UserName 
        FROM BlogPost 
        INNER JOIN Users ON BlogPost.UserID = Users.UserID 
        WHERE BlogPost.BlogPostID = ?
    ");
    $query->bindValue(1, $BlogID, PDO::PARAM_INT);
    $query->execute();
    $blog = $query->fetch(PDO::FETCH_ASSOC);

    // Redirect if blog does not exist
    if (!$blog) {
        redirect("publicview.php");
    }

    // Fetch all comments for the blog
    $commentQuery = $dbh->prepare("
        SELECT Comments.Comment, Comments.CommentDate, Users.UserName 
        FROM Comments 
        INNER JOIN Users ON Comments.UserID = Users.UserID 
        WHERE Comments.BlogID = ? 
        ORDER BY Comments.CommentDate ASC
    ");
    $commentQuery->bindValue(1, $BlogID, PDO::PARAM_INT);
    $commentQuery->execute();
    $comments = $commentQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Blogs</title>
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

            <!-- Blog Content -->
            <div id="content">
            <main>

                <article>
                    <h2><?php echo htmlspecialchars($blog['Title']); ?></h2>
                    <div class="blog-meta">
                        <span class="author"><strong>Author:</strong> <?php echo htmlspecialchars($blog['UserName']); ?></span>
                        <span class="post-date"><strong>Posted on:</strong> <?php echo htmlspecialchars($blog['PostDate']); ?></span>
                    </div>
                    <p><?php echo nl2br(htmlspecialchars($blog['Content'])); ?></p>
                </article>

                <!-- Comments Section -->
                <section class="comments">
                    <h4>Comments</h4>
                    <?php if ($comments): ?>
                        <ul>
                            <?php foreach ($comments as $comment): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($comment['UserName']); ?>:</strong>
                                    <p><?php echo htmlspecialchars($comment['Comment']); ?></p>
                                    <p>(<?php echo htmlspecialchars($comment['CommentDate']); ?>)</p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No comments yet. Be the first to comment!</p>
                    <?php endif; ?>
                </section>

                <!-- Comment Form -->
                <?php if ($cookieUser): ?>
                    <div class="form-container">
                    <form action="commentHandler.php" method="POST">
                        <input type="hidden" name="BlogID" value="<?php echo $BlogID; ?>">
                        <label for="comment">Add a Comment</label>
                        <input type="text" id="comment" name="comment" required></textarea>
                        <button type="submit">Post Comment</button>
                    </form>
                    </div>
                <?php else: ?>
                    <center><p><strong>Login to post a comment.</strong></p></center>
                <?php endif; ?>
            </main>
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

