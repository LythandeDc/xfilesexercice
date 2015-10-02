<?php

// On crée une variable qui correspond au mdp secret

// Si le mdp a été soumis et si il est égal à notre variable alors ....

	// On affiche
	echo '
	<!DOCTYPE html>
	<html>
	<head>
		<meta charset="utf-8"/>
		<title>Xfiles</title>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
	</head>
	<body>
	<br/>
	 <div class="well col-md-6 col-md-offset-3">
		<p>Bienvenue dans le site secret de la Xfiles ! </p>
		<h1>Voici les codes d\'accès :</h1>

        <p><strong>CRD5-GTFT-CK65-JOPM-V29N-24G1-HH28-LLFV</strong></p>   

        <p>

        Cette page est réservée au personnel du Xfiles. N\'oubliez pas de la visiter régulièrement car les codes d\'accès sont changés toutes les semaines.<br />

        La Xfiles vous remercie de votre visite.

        </p>
		
		<a href="../connexion.php"><button type="button" class="btn btn-primary">Connexion</button></a>
		<a href="../inscription.php"><button type="button" class="btn btn-primary">Inscription</button></a>
		
	</div>
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		
	</body>
	</html>
	';

// sinon :

	echo '
	<!DOCTYPE html>
	<html>
	<head>
		<meta charset="utf-8"/>
		<title>Xfiles</title>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
	</head>
	<body>
	<br/>
	<div class="well col-md-6 col-md-offset-3">
		<p>Veuillez réessayer</p>
	</div>
	</body>
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	</html>
	';
	
	
	// Redirection vers index.php après deux sec
}

?>