<?php
include "config.php";
include "utils.php";


$dbConn =  connect($db);

/*
  listar todos los vehiculo o solo uno
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  if (isset($_GET['id'])) {
    //Mostrar un post
    $sql = $dbConn->prepare("SELECT * FROM vehiculo-parte where id=:id");
    $sql->bindValue(':id', $_GET['id']);
    $sql->execute();
    header("HTTP/1.1 200 OK");
    echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
    exit();
  } else if (isset($_GET['idVehiculo'])) {
    
    $idVehiculo = strval($_GET['idVehiculo']);
    $query = "SELECT * FROM `vehiculo-parte` WHERE idVehiculo = '" . $idVehiculo . "';";
    $sql = $dbConn->prepare($query);
    $sql->execute();
    $sql->setFetchMode(PDO::FETCH_ASSOC);
    header("HTTP/1.1 200 OK");
    echo json_encode($sql->fetchAll());
    exit();
  }
}

// Crear un nuevo post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  try {
    $input = $_POST;
    $entityBody = file_get_contents('php://input');
    $json = json_decode($entityBody);

    //INSERT INTO `vehiculo-parte`(`idVehiculoParte`, `idVehiculo`, `idParte`, `numeroEtiqueta`) VALUES ([value-1],[value-2],[value-3],[value-4])
    $sql = "INSERT INTO `vehiculo-parte`(`idVehiculo`, `idParte`, `numeroEtiqueta`) VALUES
          ({$json->idVehiculo}, {$json->idParte}, '{$json->numeroEtiqueta}')";
    $statement = $dbConn->prepare($sql);
    bindAllValues($statement, $input);
    $statement->execute();
    $postId = $dbConn->lastInsertId();
    if ($postId) {
      $input['id'] = $postId;
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
  $id = $_GET['id'];
  $statement = $dbConn->prepare("DELETE FROM vehiculo where id=:id");
  $statement->bindValue(':id', $id);
  $statement->execute();
  header("HTTP/1.1 200 OK");
  exit();
}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
  $input = $_GET;
  $postId = $input['id'];
  $fields = getParams($input);

  $sql = "
          UPDATE vehiculo
          SET $fields
          WHERE id='$postId'
           ";

  $statement = $dbConn->prepare($sql);
  bindAllValues($statement, $input);

  $statement->execute();
  header("HTTP/1.1 200 OK");
  exit();
}


//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");
