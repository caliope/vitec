<?php
	//Se reccupera el id de la consulta almacenada
	$id = $_GET["id"];
	//Controla qué va a devolver campos de la coleccion o la colección 
	$campos = (isset($_GET["campos"])) ?  $_GET["campos"] : false; 
	//Conexion a la BD
    $mongo = new MongoClient();
    $db = $mongo->vitec;
    if ($campos) {
    	$coleccion = $db->consultas;
    	$item = $coleccion->findOne(array('_id' => new MongoId($id)));
		echo json_encode($item['campos']);	
    } else {
    	$coleccion = $db->resultado;
    	$filtro = array( 'query' => $id );
    	$cursor = $coleccion->find($filtro); 
	    $items = array();
		foreach ($cursor as $documento) {
			$documento['_id'] = (string)$documento['_id']; 
			$items[]=$documento;
		}
		echo json_encode($items);	
    }
 ?>