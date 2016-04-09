<?php
	# Solo admite metodo GET 
	if ( $_SERVER['REQUEST_METHOD'] == 'GET') {
		header('Content-Type: application/json; charset=utf-8');
		# Inicia sesion y verifica que este autenticado
		session_start();
		if (isset($_SESSION['usuario'])) {
			if (isset($_GET['query'])) {
				/*
				 * Inserta el Query en la colección de consultas con los campos de usuario, campos, registros, etc. 
				 */
				$query = $_GET["query"];
				$queryCompleto = $query;
			    $queryCompleto .= "&as_vis=" . (string)$_GET['as_vis'];
			    $queryCompleto .= "&as_sdt=" . (string)$_GET['as_sdt'];
			    $fin = 200;
			    if ($_GET['registros'] < $fin) {
			    	$fin = $_GET['registros'];
			    }
			    if (isset($_GET['as_ylo'])) {
			     	$queryCompleto .= "&as_ylo=" . (string)$_GET['as_ylo'];
			    }
				if ($fin > $_GET['registros']) {
				  	$fin = $_GET['registros'];
				}  
 				$consulta = array( 
					"query" => $query,
					"queryCompleto" => $queryCompleto,
					"campos"=> array("_id", "anio", "sitio", "tipo", "cid", "url", "fuente", "citado", "versiones", "extracto", "titulo", "autores", "query"),
					"mail" => $_SESSION['usuario'],
					"totalRegistros" => (integer)$_GET['totalregistros'],
					"traerRegistros" => (integer)$_GET['registros'],
				);
			    $mongo = new MongoClient();
			    $db = $mongo->vitec;
			    $coleccion = $db->consultas;
			    $coleccion->insert($consulta);
			    $id= (string)$consulta['_id'];
			    /*
			     *
			     * Se envía la consulta al crawler y se actualiza el Documento de en la colección Consultas
			     * con los registros recuperados.
			     * 
			     */
				$comando = "scrapy crawl goosch -a query='$queryCompleto' -a inicio=0 -a final=$fin -a id_query=$id";
				system($comando, $retorno);
				if (!$retorno) {
					$coleccion1 = $db->resultado;
					$contador = $coleccion1->count(array('query' => $id));
					$restante = (integer)$_GET['registros'] - $contador;
					$consulta = array(
						'$set'=> array(
						'contador' => $contador, 
						'restante' => $restante,
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