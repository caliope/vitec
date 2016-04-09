<?php
	/**
	 * Trae consultas que les falta recuperar resultados
	 */
	$mongo = new MongoClient();
	$db = $mongo->vitec;
	$coleccion = $db->consultas;
	$filtro = array('restante' => array( '$gt' => 0));
	$cursor = $coleccion->find($filtro);
	$items = array();
	foreach ($cursor as $id => $valor) {
		$consulta = array();
		$consulta['id'] = $id;
		$consulta['queryCompleto'] = $valor['queryCompleto'];
		$consulta['inicial'] = $valor['contador'];
		$consulta['final'] = $valor['traerRegistros'];
		$items[] = $consulta;
	}
	foreach ($items as $key => $value) {
		$minutos = rand(1, 10);
		$comando = "at now + $minutos minutes <<< \"php recuperarregistros.php";
		$comando .= " -i ".$value['id'];
		$comando .= " -s ".$value['inicial'];
		$comando .= " -e ".$value['final'];
		$comando .= " -q '".$value['queryCompleto']."'\"";
		system($comando);
	}	 
?>