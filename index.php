<!DOCTYPE html>

<html>
<head>
	<meta charset="utf-8"/>
	<title>Xfiles - Attention : page protégée par mot de passe</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
</head>
<body>
	<br/>
	<form action="traitement/secret.php" method='post' class="well col-md-6 col-md-offset-3">
		<div class="form-group">
			  <label class="control-label" for="mdp">Veuillez insérer le mot de passe secret :</label>
				  <input type="password" class="form-control" id="mdp" name="mdp" required="required"/>
			  </div>
	<p><input type="submit" value="Ok" class="btn btn-primary" /></p>
	</form>
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>