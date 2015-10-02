<!DOCTYPE html>

<html>
<head>
	<meta charset="utf-8"/>
	<title>Xfiles - Connexion </title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
</head>
<body>
<br/>
<form action="traitement/conn.php" method="post" class="well col-md-6 col-md-offset-3">
		  
		  
		  	<!-- Pseudo and Email field -->
			  <div class="form-group">
			  <label class="control-label" for="pseudo">Pseudo</label>
				  <input type="text" class="form-control" id="pseudo" name="pseudo" required="required" />
			  </div>
			  
			  
			  <!-- Password field -->
			  <div class="form-group">
			  <label class="control-label" for="pass">Mot de passe</label>
				  <input type="password" class="form-control" id="pass" name="pass" required="required"/>
			  </div>
			  
			  <!-- Password field -->
			  <div class="form-group">
			  <label class="control-label" for="cs">Code secret</label>
				  <input type="password" class="form-control" id="cs" name="cs" required="required"/>
			  </div>
			  
			  <input type="submit" class="btn btn-primary" value="Connexion" name="login"/>
			  
</form>
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>