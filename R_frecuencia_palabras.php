<?php
	header('Content-Type: application/json; charset=utf-8');
	$locale='es_ES.UTF-8';
	setlocale(LC_ALL,$locale);
	putenv('LC_ALL='.$locale);
	if (isset($_GET['id_query'])) {
		$id_query = $_GET['id_query'];
		$comando = "R/frecuencia_palabras.r $id_query";
		$resultado=exec($comando);
		$vector=json_decode($resultado);
		foreach ($vector as $key => $value) {
			$arr[$key] = $value[0];
		}
		arsort($arr);
		echo json_encode($arr, JSON_UNESCAPED_UNICODE);
	} else {
		echo '{"error": "id_query no esta definido"}';
	}
?>