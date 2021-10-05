<?php

include('timeParser.php');
include('config.json'); 

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

$json = file_get_contents('http://overpass-api.de/api/interpreter?data=[out:json];node[~%22.%22~%22.%22](' . $south . ',' . $west . ',' . $north . ',' . $east . ');out;');
$obj = json_decode($json);

//I only consider tags in the configuration file "config.json"

$user = json_decode(file_get_contents('config.json'));

foreach($user->tags as $mydata){

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
echo "<script> document.location.href='poimanager.php';</script>";
?>

