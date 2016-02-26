<?php
	# Solo admite metodo GET 
	if ( $_SERVER['REQUEST_METHOD'] == 'GET') {
		header('Content-Type: application/json; charset=utf-8');
		# Inicia sesion y verifica que este autenticado
		session_start();
		if (isset($_SESSION['usuario'])) {
			if (isset($_GET['query'])) {
				$query = $_GET["query"];
				$consulta = array( 
					"query"=>$query,
					"campos"=> array("_id", "anio", "sitio", "tipo", "cid", "url", "fuente", "citado", "versiones", "extracto", "titulo", "autores", "query"),
					"mail" => $_SESSION['usuario']
				);
			    $mongo = new MongoClient();
			    $db = $mongo->vitec;
			    $coleccion = $db->consultas;
			    $coleccion->insert($consulta);
			    $id= (string)$consulta['_id'];
				$comando = "scrapy crawl goosch -a query='$query' -a inicio=0 -a final=200 -a id_query=$id";
				system($comando, $retorno);
				if (!$retorno) {
					$coleccion1 = $db->resultado;
					$contador = $coleccion1->count(array('query' => $id));
					$consulta = array(
						'$set'=> array(
						'contador' => $contador, 
					));
					$coleccion->update(array('_id' => new MongoId($id)), $consulta);
					$resultado = array(
						"id"=>$id,
						"text"=>$query
					);
					header('Content-Type: application/json; charset=utf-8');
					echo json_encode($resultado);
				}
			} else {
				#Error no trae un query
				header('HTTP/1.1 400 Bad Request');
				$respuesta = array( 'error' => array( 'descripcion' => 'No trae una consulta.'));
				echo json_encode($respuesta); 
				}
		} else {
			# Error de autenticación
			header('HTTP/1.1 401 Method Not Allowed');
			$respuesta = array( 'error' => array( 'descripcion' => 'No se ha iniciado sesion de usuario.'));
			echo json_encode($respuesta);
		}	
	} else {
		header('HTTP/1.1 405 Method Not Allowed');
      	header('Allow: GET');	
	}
?>