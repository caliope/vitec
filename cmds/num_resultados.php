<?php
	header('Content-Type: application/json; charset=utf-8');	
	# Inicia sesion y verifica que este autenticado
	session_start();
	if (isset($_SESSION['usuario'])) {
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			if (isset($_GET['query'])) {
				# Se envía el qery a scrapy para obtener la cantidad de registros 
				$query = $_GET["query"];
			    $query .= "&as_vis=" . (string)$_GET['as_vis'];
			    $query .= "&as_sdt=" . (string)$_GET['as_sdt'];
			    $fin = 300;
			    if (isset($_GET['as_ylo'])) {
			     	$query .= "&as_ylo=" . (string)$_GET['as_ylo'];
				    if ($fin > $_GET['as_ylo']) {
				    	$fin = $_GET['as_ylo'];
				    }
			     }  
				$comando = "scrapy runspider goosch_nresultados.py -a query='$query'";
				exec($comando, $salida, $retorno);
				if (!$retorno) {
					$resultado = array("registros"=>$salida[0]);
					header('Content-Type: application/json; charset=utf-8');
					echo json_encode($resultado);
				}
			} else {
				# La URL debe contener el query a enviar
				header('HTTP/1.1 400 Bad Request');	
				$respuesta = array( 'error' => array( 'descripcion' => 'No se ha recbido el query a procesar.'));
				echo json_encode($respuesta);
			}
		} else {
			# Solo se permite el metodo GET
			header('HTTP/1.1 405 Method Not Allowed');
      		header('Allow: GET');	
		}
	} else {
		# Error de autenticación
		header('HTTP/1.1 401 Unauthorized');
		$respuesta = array( 'error' => array( 'descripcion' => 'No se ha iniciado sesion de usuario.'));
		echo json_encode($respuesta);
	} 
?>