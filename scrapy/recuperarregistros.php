<?php
	/*
	 * Este script toma de la colección Consultas aquellas que
	 * no ha terminado de recuperar los registros requeridos
	 * y los envía al crawl, acualizando despues la colección Consultas
	 */

	/*$comando = "scrapy crawl goosch -a query='$queryCompleto' -a inicio=0 -a final=$fin -a id_query=$id";
	system($comando, $retorno);
	if (!$retorno) {
		$mongo = new MongoClient();
		$db = $mongo->vitec;
		$coleccion = $db->resultado;
		$contador = $coleccion->count(array('query' => $id));
		$restante = (integer)$_GET['registros'] - $contador;
		$consulta = array(
					'$set'=> array(
					'contador' => $contador, 
					'restante' => $restante,
		));
		$coleccion->update(array('_id' => new MongoId($id)), $consulta);
	}*/
	if (es_cli()) {
		$shortopcs  = "";
		$shortopcs .= "i:";
		$shortopcs .= "q:";
		$shortopcs .= "s:";
		$shortopcs .= "e:";
		$opciones = getopt($shortopcs);
		if ( isset($opciones['i']) || isset($opciones['q']) || isset($opciones['s']) || isset($opciones['e'])) {
			$opciones['e'] = (integer)$opciones['e'];
			$opciones['s'] = (integer)$opciones['s'];
			if ( $opciones['s'] < $opciones['e'] ) {
	        	$comando = "scrapy crawl goosch -a query='".$opciones['q']."' -a inicio=".$opciones['s']." -a final=".$opciones['e']." -a id_query=".$opciones['i'];
	       		$file = fopen("archivo.txt", "a");
	       		$hoy = date("F j, Y, g:i a");  
	       		fwrite($file, $hoy . PHP_EOL);
	       		fwrite($file, $comando . PHP_EOL);
	       		fclose($file);
			} else {
				echo "Ha ocurrido un error: la opcion -e debe se mayor que -s\n"; 
			}
			
		} else {
			echo "Ha ocurrido un error: las opciones -i -q -e -s son requeridas\n"; 
		}
	} else {
		echo "Ha ocurrido un error este script solo se ejecuta desde CLI.\n"; 
	}

	function es_cli()
	{
		if( defined('STDIN') )
		{
			return true;
		}
		if( empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) 
		{
			return true;
		} 
		return false;
	}	
?>