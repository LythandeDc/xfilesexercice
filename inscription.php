<!DOCTYPE html>

<html>
<head>
	<meta charset="utf-8"/>
	<title>Xfiles - Inscription</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
</head>
<body>
<br/>
<form action="traitement/traitement.php" method="post" class="well col-md-6 col-md-offset-3">
			  
		  <!-- Name field -->
			  <div class="form-group">
			  <label class="control-label" for="name">Nom</label>
				  <input type="text" class="form-control" id="name" name="name" required="required" />
			  </div>
		  
		  
		  <!-- Pseudo field -->
			  <div class="form-group">
			  <label class="control-label" for="pseudo">Pseudo</label>
				  <input type="text" class="form-control" id="pseudo" name="pseudo" required="required" />
			  </div>
			  
			  <!-- Email field -->
			  <div class="form-group">
			  <label class="control-label" for="email">Adress Email</label>
				  <input type="email" class="form-control" id="email" name="email" required="required" />
			  </div>
			  
			  <!-- Password field -->
			  <div class="form-group">
			  <label class="control-label" for="pass">Mot de passe</label>
				  <input type="password" class="form-control" id="pass" name="pass" required="required"/>
			  </div>
			  
			  <!-- Password Confirmation field -->
			  <div class="form-group">
			  <label class="control-label" for="password_confirm">Confirmez votre mot de passe</label>
				  <input type="password" class="form-control" id="password_confirm" name="password_confirm" required="required"/>
			  </div>
			  
			  <!-- Password field -->
			  <div class="form-group">
			  <label class="control-label" for="cs">Code secret</label>
				  <input type="password" class="form-control" id="cs" name="cs" required="required"/>
			  </div>
	
			  <input type="submit" class="btn btn-primary" value="Inscription" name="register"/>
			  
</form>
	
    <!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>	
	
</body>
</html>