<?php

require_once("../dyn/database.inc.php");

// connexion à la base de donnée (je ne peux pas utiliser l'outil d'openmairie ou PDO somehow)
$cnx = pg_connect("host=" . $conn[1][6] ." port=" . $conn[1][7] . " dbname=" . $conn[1][9] ." user=". $conn[1][3] ." password=". $conn[1][4]);

if(!$cnx)
{
    echo'Connexion to db failed';
}else{
    // vérifie si une espèce est choisie
    if($_POST['espece'] != 0)
    {
        $query = pg_query($cnx,"SELECT animal_race, nom from openrefuge.animal_race where animal_espece = ". $_POST['espece']);
        $result = array();
        $result[] = array("animal_race" => "","nom" => "Choisir race");
        while($e = pg_fetch_assoc($query)){
            $result[] = $e;
        }

        // renvoi le tableau associatif en format JSON 
        echo json_encode($result);
    }else{
        echo json_encode(array(array("animal_race" => "", "nom" => "Choisir espece")));
    }
}


