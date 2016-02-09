<?php
 header('Content-Type: application/json; charset=utf-8');
 $id_query = $_GET['id'];
 $campo = $_GET['campo'];
 $mongo = new MongoClient();
 $db = $mongo->vitec;
 $coleccion = $db->resultado;
 if ($campo == "anio") {
 	$agregador = [
        [
        	'$match' => ['query'=>"$id_query"]
        ],
        [
        	'$group' => [ '_id' => '$anio', 'total' => [ '$sum'=> 1]]
        ], 
        [
        	'$sort'  => ['_id'=> 1]
        ]
    ];
 	$resultados = $coleccion->aggregateCursor($agregador);
	foreach ($resultados as $key => $value) {
	 		$vector[$value["_id"]] = $value["total"];
	 }
	 echo json_encode($vector); 	
  } elseif ($campo == "autores") {
    $agregador = [
        [
            '$match' => [ 'query'=> "$id_query"]
        ],
        [
            '$unwind' => '$autores'
        ], 
        [
            '$group'=> [ '_id' => '$autores', 'total' => [ '$sum' => 1 ]]
        ], 
        [
            '$sort' => [ 'total' => -1 ]
        ]
    ];
    $resultados = $coleccion->aggregateCursor($agregador);
    foreach ($resultados as $key => $value) {
            $vector[$value["_id"]] = $value["total"];
     }
     echo json_encode($vector); 
  } elseif ($campo == "sitios") {
    $agregador = [
        [ 
            '$match' => [ 'query' => "$id_query"]
        ],
        [
            '$group' => [ '_id' => '$sitio', 'total' => [ '$sum' => 1 ]]
        ],
        [
            '$sort' => [ 'total' => -1]
        ]
    ];
    $resultados = $coleccion->aggregateCursor($agregador);
    foreach ($resultados as $key => $value) {
            $vector[$value["_id"]] = $value["total"];
     }
     echo json_encode($vector); 
  }
?>