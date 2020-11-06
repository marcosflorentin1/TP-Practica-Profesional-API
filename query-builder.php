<?php

function Crear($ultimoIdVehiculo, $operador, $dominio, $tramite, $siniestro, $chasis, $etiqueta, $pageSize)
{
    $query = "SELECT * FROM vehiculo WHERE";

    if (empty($dominio) && empty($tramite)) {
        $query = $query . " idVehiculo " . $operador . " " . $ultimoIdVehiculo;
    }

    if (!empty($dominio)) {
        $query = $query . " dominio like '%" . $dominio . "%'";
    }

    if (!empty($tramite)) {
        if (substr($query, strlen($query) - 5) != "WHERE") {
            $query = $query . " AND ";
        }

        $query = $query . " certificadoBaja like '%" . $tramite . "%'";
    }

    if (!empty($siniestro)) {
        if (substr($query, strlen($query) - 5) != "WHERE") {
            $query = $query . " AND ";
        }

        $query = $query . " numeroSiniestro like '%" . $siniestro . "%'";
    }

    if (!empty($chasis)) {
        if (substr($query, strlen($query) - 5) != "WHERE") {
            $query = $query . " AND ";
        }

        $query = $query . " numeroChasis like '%" . $chasis . "%'";
    }

    if (!empty($etiqueta)) {
        if (substr($query, strlen($query) - 5) != "WHERE") {
            $query = $query . " AND ";
        }

        $query = $query . " idVehiculo in (select idVehiculo from `vehiculo-parte` where numeroEtiqueta like '%" . $etiqueta . "%')";
    }
    
    //$query = $query . " ORDER BY idVehiculo DESC LIMIT 10000;";
    $query = $query . " ORDER BY idVehiculo DESC LIMIT ". $pageSize .";";

    return $query;
}