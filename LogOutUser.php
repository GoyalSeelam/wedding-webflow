<?php 
	include('Functions.php');
	//$cookieUser = getCookieUser();
	deleteCookie("CookieUser");
	setCookieMessage("Logged Out Successfully!");
    redirect("Homepage.php");
?>