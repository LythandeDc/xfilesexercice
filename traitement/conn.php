<?php 

	// On se connecte à la bdd

	//Si le formulaire a été soumis (login) et le code secret est juste
		 
	 //Si tous les champs sont remplis (pseudo et pass)
			
			// On créé des variables ...

			// Hachage du mot de passe

			// Vérification des identifiants (SELECT)

			// Le résultat est égal à la récupération des données

			// Si c'est différent de résultat
			if ()
			{
				echo '
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
				<p>Mauvais identifiant ou mot de passe !</p>
				</div>
				<!-- Latest compiled and minified JavaScript -->
				<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
				</body>
				</html>
				';
				header('Refresh:2;url=../connexion.php');
			}
            //sinon
			else
			{
				// On démarre la session
				
				// la séssion id correspond au résultat précédent
				
				// la session pseudo correspond à la variable pseudo
				
				// et on affiche : 
				echo '
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
				<p>Vous êtes connecté !</p>
				</div>
				<!-- Latest compiled and minified JavaScript -->
				<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
				</body>
				</html>';
				
				// On retourne dans nasa.php après deux sec
			}
		}
	 }
?>