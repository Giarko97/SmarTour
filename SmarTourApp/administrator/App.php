<?php

//devono essere inseriti opening times

include_once 'timeParser.php';
include_once 'config.json'; 
include_once 'pdo.php';
include_once 'OSMscore.php';

$server = "127.0.0.1";
$username = "root";
$password = "";
$db = "poidb";

$file = 'openingTimes.txt';

$mysqli = new mysqli($server, $username, $password, $db);

session_start();
ob_start();

//the position variables chosen by the user are assigned

$sw = $_GET['sw'];
$ne = $_GET['ne'];

$southwest = explode(" ", $sw);
$south = $southwest[0];
$west = $southwest[1];

$northeast = explode(" ", $ne);
$north = $northeast[0];
$east = $northeast[1];

$monthsex = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");

//extraction the data from the API

$json = file_get_contents('http://overpass-api.de/api/interpreter?data=[out:json];node[~%22.%22~%22.%22](' . $south . ',' . $west . ',' . $north . ',' . $east . ');out;out%20geom;');
$objn = json_decode($json);

$json = file_get_contents('http://overpass-api.de/api/interpreter?data=[out:json];way[~%22.%22~%22.%22](' . $south . ',' . $west . ',' . $north . ',' . $east . ');out;out%20geom;');
$objw = json_decode($json);

echo "<pre>";


//I only consider tags in the configuration file "config.json"
$getcont = file_get_contents('config.json');
$config = json_decode($getcont);

//array per TOI
$supCat = array();
$subCat = array();

foreach($config->tags as $name){
    $super = $name->name;
    array_push($supCat, $super);
    foreach($name->values as $sub){
        array_push($subCat, $sub);
    }
}

foreach($objn->elements as $value){
    if(isset($value->tags->name)){
        foreach($supCat as $tag){
            if(isset($value->tags->$tag)){
                $cfinal = $value->tags->$tag;
                foreach($subCat as $subc){
                    if($cfinal == $subc){

                        $type = $value->type;
                        if(isset($value->lat)){
                            $lat = $value->lat;
                            $lon = $value->lon;

                            $nw = 0;
                        }else{
                            break;
                        }

                        $id = $value->id;
                        $name = $value->tags->name;
                        $count1 = 0;
                        $count2 = 0;

                        //quotes replace
                        $name = str_replace("'", "\'", $name);

                        //verifica esistenza poi nel db
                        $exist1 = $mysqli->query("SELECT * FROM poi WHERE id = '$id'");
                        $count1 = $exist1->num_rows;

                        $exist2 = $mysqli->query("SELECT * FROM poi WHERE names = '$name'");
                        $count2 = $exist2->num_rows;
                        
                        if ($count1 == 0 && $count2 == 0){

                            $pdo = connessione();
                            try{
                                if(isset($value->tags->wikipedia)){
                                    $wikipedia = $value->tags->wikipedia;
                                    $wikipedia = substr($wikipedia, 3);
                                    $wikipedia = str_replace("'", "\'", $wikipedia);

                                    if($pdo->exec("INSERT INTO poi (id, names, lat, lon, nw, wikipedia, access) VALUES ('$id', '$name', '$lat', '$lon', '$nw', '$wikipedia', '1')")){
                                        echo $name . " Inserito\n";
                                        osmScore($id);
                                    }
                                }else if($pdo->exec("INSERT INTO poi (id, names, lat, lon, access) VALUES ('$id', '$name', '$lat', '$lon', '1')")){
                                    echo $name . " Inserito\n";
                                    osmScore($id);
                                }
                            }catch(PDOException $pdoe){
                                echo $pdoe->getMessage();
                            }
                            $pdo = null;
                        }
                    }
                }
            }
        }
    }
}

foreach($objw->elements as $value){
    if(isset($value->tags->name)){
        foreach($supCat as $tag){
            if(isset($value->tags->$tag)){
                $cfinal = $value->tags->$tag;
                foreach($subCat as $subc){
                    if($cfinal == $subc){

                        $type = $value->type;
                        if($type == "way" && isset($value->bounds)){
                            $minlat = $value->bounds->minlat;
                            $maxlat = $value->bounds->maxlat;
                            $minlon = $value->bounds->minlon;
                            $maxlon = $value->bounds->maxlon;

                            $pdo = connessione();

                            try{
                                foreach($pdo->query("SELECT id, names FROM poi") as $row){
                                    foreach($value->nodes as $nodes){
                                        //echo $row['id'] . "----" . $nodes . "\n";
                                        if($row['id'] == $nodes){
                                            echo "eccomi sono io \n" . $row['names'];
                                        }
                                    }
                                }
                                /*
                                if($pdo->exec("DELETE FROM poi WHERE '$minlat' < lat < '$maxlat' AND  '$minlon' < lon < '$maxlon'")){
                                    echo "Ho ELIMINATO una tupla" . $value->tags->name . "\n";
                                }
                                */
                            }catch(PDOException $pdoe){
                                echo $pdoe->getMessage();
                            }
                            $pdo = null;

                            $lat = ($minlat+$maxlat)/2;
                            $lon = ($minlon+$maxlon)/2;

                            $nw = 1;
                        }else if(isset($value->lat)){
                            $lat = $value->lat;
                            $lon = $value->lon;

                            $nw = 0;
                        }else{
                            break;
                        }

                        $id = $value->id;
                        $name = $value->tags->name;
                        $count1 = 0;
                        $count2 = 0;

                        //quotes replace
                        $name = str_replace("'", "\'", $name);

                        //verifica esistenza poi nel db
                        $exist1 = $mysqli->query("SELECT * FROM poi WHERE id = '$id'");
                        $count1 = $exist1->num_rows;

                        $exist2 = $mysqli->query("SELECT * FROM poi WHERE names = '$name'");
                        $count2 = $exist2->num_rows;
                        
                        if ($count1 == 0 && $count2 == 0){

                            $pdo = connessione();
                            try{
                                if(isset($value->tags->wikipedia)){
                                    $wikipedia = $value->tags->wikipedia;
                                    $wikipedia = substr($wikipedia, 3);
                                    $wikipedia = str_replace("'", "\'", $wikipedia);

                                    if($pdo->exec("INSERT INTO poi (id, names, lat, lon, nw, wikipedia, access) VALUES ('$id', '$name', '$lat', '$lon', '$nw', '$wikipedia', '1')")){
                                        echo $name . " Inserito\n";
                                        osmScore($id);
                                    }
                                }else if($pdo->exec("INSERT INTO poi (id, names, lat, lon, nw, access) VALUES ('$id', '$name', '$lat', '$lon', '$nw', '1')")){
                                    echo $name . " Inserito\n";
                                    osmScore($id);
                                }
                            }catch(PDOException $pdoe){
                                echo $pdoe->getMessage();
                            }
                            $pdo = null;
                        }
                    }
                }
            }
        }
    }
}


/*

foreach($config->tags as $mydata){

    $cat =  $mydata->name;

    foreach ($obj->elements as $value){
        if(isset($value->tags->name)){
            if(isset($value->tags->$cat)){
                foreach($mydata->values as $values){
                    if($values == $value->tags->$cat){   
                        
                        $id = $value->id;
                        $lat = $value->lat;
                        $lon = $value->lon;
                        $name = $value->tags->name;
                        $count = 0;

                        //valutazioni fittizie
                        for($i = 1; $i<=36; $i++){
                            $score = rand(1, 5);
                            if($mysqli->query("INSERT INTO poitoiscore (idpoi, idtoi, score) 
                            VALUES ('$id', '$i', '$score')") == false){
                                //echo "FALSE <br>";
                            }
                        }

                        //quotes replace
                        $name = str_replace("'", "\'", $name);
                        $time = "";
                        $exist = $mysqli->query("SELECT * FROM poi WHERE id = '$id'");

                        //opening times
                        if(isset($value->tags->opening_hours)){

                            $opening_hours = $value->tags->opening_hours;
                            $time = timeparser($opening_hours);
                            
                            file_put_contents($file, $time . "\n", FILE_APPEND);

                            $t = explode(";", $time);

                            if($mysqli->query("INSERT INTO openingtimes (id, day_from, day_to, hour_from_ hour_to, date_from, date_to note) 
                            VALUES ('$id', 'Mo', 'Su', '8:00', '20:00', '01/01/2020', 
                                    '31/12/2050', '$opening_hours')") == false){
                                //echo "FALSE <br>";
                                
                            }
                        }elseif($value->tags->$cat == "museum"){

                            $opening_hours = "Mo-Su 08:00-13:30; Mo-Su 16:30-20:00";

                            file_put_contents($file, $opening_hours . "\n", FILE_APPEND);
                            
                            if($mysqli->query("INSERT INTO openingtimes (id, day_from, day_to, hour_from_ hour_to, date_from, date_to note) 
                                               VALUES ('$id', 'Mo', 'Su', '8:00', '13:30', '01/01/2020', '31/12/2050', '$opening_hours')") == false){
                                //echo "FALSE <br>";
                            }
                            if($mysqli->query("INSERT INTO openingtimes (id, day_from, day_to, hour_from_ hour_to, date_from, date_to note) 
                                               VALUES ('$id', 'Mo', 'Su', '16:30', '20:00', '01/01/2020', '31/12/2050', '$opening_hours')") == false){
                                //echo "FALSE <br>";
                            }
                        }elseif($value->tags->$cat == "park"){

                            $opening_hours = "Mo-Su 00:00-23:59";

                            file_put_contents($file, $opening_hours . "\n", FILE_APPEND);

                            if($mysqli->query("INSERT INTO openingtimes (id, day_from, day_to, hour_from_ hour_to, date_from, date_to note) 
                                               VALUES ('$id', 'Mo', 'Su', '00:00', '23:59', '01/01/2020', '31/12/2050', '$opening_hours')") == false){
                                //echo "FALSE <br>";
                            }
                        }else{
                            if($mysqli->query("INSERT INTO openingtimes (id) VALUES ('$id')") == false){
                                //echo "FALSE <br>";
                            }
                        }

                        //control
                        $count = $exist->num_rows;
                        
                        //attributo accessible settato a 1 di default, capire casi contrari
                        if($count == 0){
                            //the following cycle has been added to monitor any errors, it can be eliminated
                            if($mysqli->query("INSERT INTO poi (id, name, lat, lon, access) VALUES ('$id', '$name', '$lat', '$lon', '1')") == false){
                                //echo "FALSE <br>";
                            }
                        }
                    }                    
                }
            }
        }
    }
} 
*/
echo "<script> document.location.href='poimanager.php';</script>";
?>