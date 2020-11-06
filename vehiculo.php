<?php
include "config.php";
include "utils.php";
include "query-builder.php";


$dbConn =  connect($db);

/*
  listar todos los vehiculo o solo uno
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  if (isset($_GET['idVehiculo'])) {
    //Mostrar un post
    $sql = $dbConn->prepare("SELECT * FROM vehiculo where idVehiculo=:idVehiculo");
    $sql->bindValue(':idVehiculo', $_GET['idVehiculo']);
    $sql->execute();
    header("HTTP/1.1 200 OK");
    echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
    exit();
  } elseif (isset($_GET['dominio_query'])) {
    $sql = $dbConn->prepare("SELECT * FROM vehiculo where dominio=:dominio_query");
    $sql->bindValue(':dominio_query', $_GET['dominio_query']);
    $sql->execute();
    header("HTTP/1.1 200 OK");
    echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
    exit();
  } else {
    try {
      $operador = "";
      if (strval($_GET['operador']) == "a") {
        $operador = ">=";
      } else {
        $operador = "<";
      }
      $ultimoIdVehiculo = strval($_GET['ultimoIdVehiculo']);
      $dominio = strval($_GET['dominio']);
      $tramite = strval($_GET['tramite']);
      $siniestro = strval($_GET['siniestro']);
      $chasis = strval($_GET['chasis']);
      $etiqueta = strval($_GET['etiqueta']);
      $pageSize = intval($_GET['pageSize']);

      //$sql = $dbConn->prepare("SELECT * FROM vehiculo WHERE idVehiculo " . $operador . " " . $ultimoIdVehiculo . " ORDER BY idVehiculo ASC LIMIT 3;");
      $query = Crear($ultimoIdVehiculo, $operador, $dominio, $tramite, $siniestro, $chasis, $etiqueta, $pageSize);
      $sql = $dbConn->prepare($query);

      $sql->bindValue(':ultimoIdVehiculo', $_GET['ultimoIdVehiculo']);
      $sql->bindValue(':operador', $_GET['operador']);
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);
      header("HTTP/1.1 200 OK");
      echo json_encode($sql->fetchAll());
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

    $sql = "INSERT INTO `vehiculo`(`dominio`, `certificadoBaja`, `formulario04d`, `registroSeccional`, `fechaInicio`, `fechaFin`, `cantidadEtiquetas`, `titular`, `marca`, `modelo`, `marcaMotor`, `numeroMotor`, `marcaChasis`, `numeroChasis`, `localidad`, `provincia`, `estado`, `eliminado`, `numeroSiniestro`, `pagado`, `observacionPago`, `creadoPor`, `creadoFecha`)
          VALUES
          ('{$json->dominio}', '{$json->certificadoBaja}', '{$json->formulario04d}', '{$json->registroSeccional}', '{$json->fechaInicio}', '{$json->fechaFin}', '{$json->cantidadEtiquetas}', '{$json->titular}', '{$json->marca}', '{$json->modelo}', '{$json->marcaMotor}', '{$json->numeroMotor}', '{$json->marcaChasis}', '{$json->numeroChasis}', '{$json->localidad}', '{$json->provincia}', '{$json->estado}', '{$json->eliminado}', '{$json->numeroSiniestro}', '{$json->pagado}', '{$json->observacionPago}', '{$json->creadoPor}', '{$json->creadoFecha}')";

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
  $id = $_GET['id'];
  $statement = $dbConn->prepare("DELETE FROM vehiculo where idVehiculo=:id");
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

// function CrearQuery($ultimoIdVehiculo, $operador, $dominio, $tramite)
// {
//     $query = "SELECT * FROM vehiculo WHERE"; 
    
//     if(!empty($dominio))
//     {
//       if($dominio.substring())
//       $query = $query . " AND dominio like '%". $dominio ."%'";
//     }

//     if(!empty($tramite))
//     {
//       $query = $query . " AND certificadoBaja like '%". $tramite ."%'";
//     }
    
//     if(empty($dominio) && empty($tramite))
//     {
//       $query = $query . " idVehiculo " . $operador . " " . $ultimoIdVehiculo;
//     }

//     $query = $query . " ORDER BY idVehiculo ASC LIMIT 3;";
//     //echo $query;
//     return $query;
// }
