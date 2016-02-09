<?php
	if (isset($_GET['id_query'])) {
		$id_query = $_GET['id_query'];
		$nombre = uniqid();
		$comando = "R/dendograma_palabras.r $id_query $nombre.png";
		$locale='es_ES.UTF-8';
		setlocale(LC_ALL,$locale);
		putenv('LC_ALL='.$locale);
		$resultado=exec($comando);
		echo $resultado;
	} else {
		echo '{"error": "id_query no esta definido"}';
	}
?>