<?php include('server.php') ?>

   <?php session_start(); 
    STATIC $counter = 0;
     if (!isset($_SESSION['username'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }
  if (isset($_GET['logout'])) {
  	session_destroy();
  	unset($_SESSION['username']);
  	header("location: login.php");
  }
  
   ?>
   
<!DOCTYPE html>
<html>
<head>
  <title>Menu</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="header">
  	<h2>Simedtrieste Menu</h2>
	  	<div>
  	 <a href="chart.php?room=1"  >  Room 1 |</a>
	 <a href="chart.php?room=2"  >  Room 2 | </a>
	  <a href="chart.php?room=3" >  Room 3 | </a>
	  <a href="chart.php?room=4"  > Room 4 | </a>
	  <a href="chart.php?room=5"  > Room 5 </a>
  	</div>
  </div>
</body>
</html>