<?php
include "config.php";
include "utils.php";

$dbConn =  connect($db);

/*
  listar todos los vehiculo o solo uno
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  if (isset($_GET['usuario'])) {
    //Mostrar un post
    $sql = $dbConn->prepare("SELECT * FROM usuario where usuario=:usuario and pass=:pass");
    $sql->bindValue(':usuario', $_GET['usuario']);
    $sql->bindValue(':pass', $_GET['pass']);
    $sql->execute();
    header("HTTP/1.1 200 OK");
    echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
    exit();
  } else {
    try {
      $sql = $dbConn->prepare("SELECT * FROM usuario");
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);
      header("HTTP/1.1 200 OK");
      echo json_encode( $sql->fetchAll()  );
      exit();
    } catch (Exception $e) {
      echo "Query: " . $query . "Excepción capturada: ',  $e->getMessage(), '\n'";
    }
    exit();
  }
}

// Crear un nuevo post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  try {
    $input = $_POST;
    $entityBody = file_get_contents('php://input');
    $json = json_decode($entityBody);
    
    $sql = "INSERT INTO `usuario`(`usuario`, `pass`)
          VALUES
          ('{$json->usuario}', '{$json->pass}')";

    $statement = $dbConn->prepare($sql);
    
    bindAllValues($statement, $entityBody);
    $statement->execute();

    $postId = $dbConn->lastInsertId();
    if ($postId) {
      $input['idVehiculo'] = $postId;
      header("HTTP/1.1 200 OK");
      echo json_encode($input);
      exit();
    }
  } catch (Exception $e) {
    echo "Query: " . $sql . "Excepción capturada: ',  $e->getMessage(), '\n'";
  }
}

//Borrar
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
  $idUsuario = $_GET['idUsuario'];
  $statement = $dbConn->prepare("DELETE FROM usuario where idUsuario=:idUsuario");
  $statement->bindValue(':idUsuario', $idUsuario);
  $statement->execute();
  header("HTTP/1.1 200 OK");
  exit();
}

//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");