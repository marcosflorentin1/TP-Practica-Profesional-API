<?php
include "config.php";
include "utils.php";


$dbConn =  connect($db);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  try {




$SELECT = "SELECT imagen FROM imagen";

$dbConn->query($SELECT);
$Archivo=$dbConn->datos()[0];
return new soapval('return', 'tns:Archivo', $Archivo);




  } catch (PDOException $e) {
    echo "Exception:" . $e->getMessage();
  }
}

// if ($_SERVER['REQUEST_METHOD'] == 'GET') {
//   //if (isset($_GET['imagen']))
//   //{
//   //Mostrar un post
//   $sql = $dbConn->prepare("SELECT imagen FROM `imagen`");
//   $sql->execute();
//   header("HTTP/1.1 200 OK");
//   $picture = $sql->fetch(PDO::PARAM_LOB);
//   $result = stream_get_contents($picture);
//   echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
//   //echo $result;
//   exit();
//   //}
// }





// Crear un nuevo post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  try {
    $input = $_POST;
    $entityBody = file_get_contents('php://input');
    $json = json_decode($entityBody);

    $sql = "INSERT INTO `parte`(`descripcion`)
          VALUES
          ('{$json->descripcion}')";

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
  $idParte = $_GET['idParte'];
  $statement = $dbConn->prepare("DELETE FROM parte where idParte=:idParte");
  $statement->bindValue(':idParte', $idParte);
  $statement->execute();
  header("HTTP/1.1 200 OK");
  exit();
}

//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");
