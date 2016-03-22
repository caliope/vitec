<?php
	header('Content-Type: application/json; charset=utf-8');
	# Inicia sesion y verifica que este autenticado
	session_start();
	if (isset($_SESSION['usuario'])) {
		//Se reccupera el id de la consulta almacenada
		$id = $_GET["id"];
		//Controla qué va a devolver: campos de la coleccion o la colección 
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
	    	$cursor = $coleccion->find($filtro)->sort(array('_id' => 1)); 
		    $items = array();
			foreach ($cursor as $documento) {
				$documento['_id'] = (string)$documento['_id']; 
				$items[]=$documento;
			}
			echo json_encode($items);	
	    }
	} else {
		# Error de autenticación
		header('HTTP/1.1 401 Unauthorized');
		$respuesta = array( 'error' => array( 'descripcion' => 'No se ha iniciado sesion de usuario.'));
		echo json_encode($respuesta);
	}
 ?>