<?php

	// Connexion à la bdd

	/*var_dump(PDO::getAvailableDrivers());*/

	//Database credentials
	define('DB_HOST', 'localhost');
	define('DB_NAME', 'xfiles');
	define('DB_USERNAME', 'root');
	define('DB_PASSWORD', '');


	try
	{
		$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USERNAME, DB_PASSWORD);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch (Exception $e)
	// Une exception est une erreur se produisant dans un programme qui conduit le plus souvent à l'arrêt de celui-ci.
	{
			die('Erreur  : ' . $e->getMessage());
	}

?>