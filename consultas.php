<?php
    $mongo = new MongoClient();
    $db = $mongo->vitec;
    $coleccion = $db->consultas;
    $cursor = $coleccion->find ();
    $items = array();
	foreach ($cursor as $id => $valor) {
		$consulta = array();
		$consulta['id'] = $id;
		$consulta['text'] = $valor['query'];
		$items[] = $consulta;
	}
	echo json_encode($items);
 ?>