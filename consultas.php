<?php
	header('Content-Type: application/json; charset=utf-8');
	# Inicia sesion y verifica que este autenticado
	session_start();
	if (isset($_SESSION['usuario'])) {
		# Solo admite metodo GET o DELETE
		if ( $_SERVER['REQUEST_METHOD'] == 'GET') {
			# Devuelve las consultas guardadas por un usuario
			$mongo = new MongoClient();
	    	$db = $mongo->vitec;
	    	$coleccion = $db->consultas;
	    	if (isset($_GET['id'])) {
	    		$items = $coleccion->findOne(array('_id' => new MongoId($_GET['id'])));
	    	} else {
	    		$filtro = array( 'mail' => $_SESSION['usuario'] );
		    	$cursor = $coleccion->find($filtro);
		    	$items = array();
				foreach ($cursor as $id => $valor) {
					$consulta = array();
					$consulta['id'] = $id;
					$consulta['text'] = $valor['query'];
					$consulta['totalRegistros'] = $valor['totalRegistros'];
					$consulta['traerRegistros'] = $valor['traerRegistros'];
					$items[] = $consulta;
				}	
	    	}
			echo json_encode($items);		
		} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
			parse_str(file_get_contents("php://input"),$post_vars);
			# ELimina la consulta y sus registros asociados
			$id = $post_vars['id'];
			$mongo = new MongoClient();
	    	$db = $mongo->vitec;
	    	$db->resultado->remove(array("query" => $id));
	    	$db->consultas->remove(array('_id' => new MongoId($id)));
			$respuesta = array( 'Ok' => array( 'descripcion' => 'Se han eliminado los registros.'));
			echo json_encode($respuesta);
		} else {
			header('HTTP/1.1 405 Method Not Allowed');
      		header('Allow: GET DELETE');	
		}
	} else {
		# Error de autenticación
		header('HTTP/1.1 401 Unauthorized');
		$respuesta = array( 'error' => array( 'descripcion' => 'No se ha iniciado sesion de usuario.'));
		echo json_encode($respuesta);
	}
 ?>