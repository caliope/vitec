<?php
	$metodo = $_SERVER['REQUEST_METHOD'];
	if ( $metodo == 'POST')  {
		# Conexion a la BD
    	$mongo = new MongoClient();
    	$db = $mongo->vitec;
    	$coleccion = $db->usuarios;
		if (isset($_POST['autenticar'])) {
			# Verifica existencia usuario
			$usuario = $coleccion->findOne(array('email' => $_POST['email']));
			if (is_null($usuario)) {
				# Usuario no existe
				$resultado = array('error' => 'Usuario no existe');
				$encabezado = 'HTTP/1.1 401 User not exist';
			} else {
				# Verifica que la clave coincida
				$contrasenaMd5 = md5($_POST['contrasena']);
				if ($contrasenaMd5 === $usuario["contrasena"]) {
				 	$resultado = array('resultado' => 'Ok');
				 	$encabezado = 'HTTP/1.1 204 No content';
				 	session_start();
				 	$_SESSION['usuario'] = $usuario["email"];
				 } else {
				 	# Contrasena errada
					$resultado = array('error' => 'Contraseña incorrecta');
					$encabezado = 'HTTP/1.1 401 Password not match';
				 }  
			}
			header($encabezado);
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($resultado);
		} else {
			# Inserta un usuario -- Falta validar que todos los datos vengan completos
			$usuario = array(
				'nombres' => $_POST['nombres'],
				'apellidos' => $_POST['apellidos'],
				'email' => $_POST['email'],
				'contrasena' => md5($_POST['contrasena'])
			);
			$coleccion->insert($usuario);
			header('HTTP/1.1 201 User created');
		}
	} else {
		header('HTTP/1.1 405 Method Not Allowed');
      	header('Allow: POST');	
	}
?>