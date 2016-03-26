<?php
	header('Content-Type: application/json; charset=utf-8');	
	# Inicia sesion y verifica que este autenticado
	session_start();
	if (isset($_SESSION['usuario'])) {
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			if (isset($_GET['query'])) {
				# Se envía el qery a scrapy para obtener la cantidad de registros
				$agent[] = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36";
				$agent[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36";
				$agent[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36";
				$agent[] = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36";
				$agent[] = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1";
				$agent[] = "Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0";
				$agent[] = "Mozilla/5.0 (X11; Linux i586; rv:31.0) Gecko/20100101 Firefox/31.0";
				$agent[] = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:31.0) Gecko/20130401 Firefox/31.0";
				$agent[] = "Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0";
				$agent[] = "Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; AS; rv:11.0) like Gecko";
				$agent[] = "Mozilla/5.0 (compatible, MSIE 11, Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko";
				$agent[] = "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 7.0; InfoPath.3; .NET CLR 3.1.40767; Trident/6.0; en-IN)";
				$agent[] = "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)";
				$agent[] = "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)";
				$agent[] = "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/5.0)";
				$agent[] = "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/4.0; InfoPath.2; SV1; .NET CLR 2.0.50727; WOW64)";
				$agent[] = "Mozilla/5.0 (compatible; MSIE 10.0; Macintosh; Intel Mac OS X 10_7_3; Trident/6.0)";
				$agent[] = "Mozilla/4.0 (Compatible; MSIE 8.0; Windows NT 5.2; Trident/6.0)";
				$agent[] = "Mozilla/4.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/5.0)";
				$agent[] = "Mozilla/1.22 (compatible; MSIE 10.0; Windows 3.1)";
				$user_agent = $agent[rand(0, count($agent)-1)];
				$query = $_GET["query"];
			    $query .= "&as_vis=" . (string)$_GET['as_vis'];
			    $query .= "&as_sdt=" . (string)$_GET['as_sdt'];
			    if (isset($_GET['as_ylo'])) {
			     	$query .= "&as_ylo=" . (string)$_GET['as_ylo'];
				    if ($fin > $_GET['as_ylo']) {
				    	$fin = $_GET['as_ylo'];
				    }
			     }  
				$comando = "scrapy runspider goosch_nresultados.py -a query='$query' -s USER_AGENT='$user_agent'";
				exec($comando, $salida, $retorno);
				if (count($salida) >= 1) {
					$resultado = array("registros"=>$salida[0]);
					header('Content-Type: application/json; charset=utf-8');
					echo json_encode($resultado);
				} else {
					header('HTTP/1.1 500 Internal Server Error');	
					$respuesta = array( 'error' => array( 'descripcion' => 'Se ha presentado un error al tratar de recuperar la cantidad de registros que retorna la consulta.'));
					echo json_encode($respuesta);
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