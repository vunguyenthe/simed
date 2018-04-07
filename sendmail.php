<?php include('apihelper.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Send mail</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="header">
  	<h2>Send mail</h2>
  </div>
	
  <form method="post" >
  	<?php include('errors.php'); ?>
  	<div class="input-group">
  	  <label>Email</label>
  	  <input type="email" name="email" value="<?php echo $email; ?>">
  	</div>
  	<div class="input-group">
  	  <label>content</label>
  	  <input type="text" name="content">
	</div>  
 	<div class="input-group">
  	  <button type="submit" class="btn" name="reg_user">Send</button>
  	</div>
	
  </form>
</body>
</html>
 