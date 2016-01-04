<?php
	if (isset($_GET['id_query'])) {
		$id_query = $_GET['id_query'];
		$comando = "R/frecuencia_palabras.r $id_query";
		echo exec($comando);
	} else {
		echo '{"error": "id_query no esta definido"}';
	}
	
		 
?>