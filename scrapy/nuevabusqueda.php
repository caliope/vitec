<?php
	$query = $_GET["query"];
	$consulta = array( 
		"query"=>$query,
		"campos"=> array("_id", "anio", "sitio", "tipo", "cid", "url", "fuente", "citado", "versiones", "extracto", "titulo", "autores", "query"),
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
?>