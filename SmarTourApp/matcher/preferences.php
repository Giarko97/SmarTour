<pre>

<?php

include_once 'pdo.php';


$getcont = file_get_contents('TOIPreferences.json');
$res = getPoi($getcont);
print_r(json_decode($res));
function getPoi($preferences){
    $array = array();
    $pdo = connessione();
    $preference = json_decode($preferences);
    foreach($preference as $n){
        $toiname1 = $n->name;
        foreach($n->item as $s){
            $toiname2 = $s->name;
            $score2 = $s->pref;
            foreach($pdo->query("SELECT id FROM toi1 WHERE names = '$toiname1' LIMIT 1") as $t1){
                $idtoi1 = $t1['id'];
                foreach($pdo->query("SELECT * FROM toi2 WHERE idtoi1 = '$idtoi1' AND names = '$toiname2'") as $t2){
                    $idtoi2 = $t2['id'];
                    foreach($pdo->query("SELECT * FROM score_osm WHERE score <= '$score2' AND idtoi = '$idtoi2' GROUP BY idpoi") as $sc){
                        $idpoi = $sc['idpoi'];
                        foreach($pdo->query("SELECT * FROM poi WHERE id = '$idpoi' LIMIT 1") as $p){
                            if(isset($p)){
                                $namepoi = $p['names'];
                                $description = $p['description'];
                                $lat = $p['lat'];
                                $lon = $p['lon'];
                                $access = $p['access'];
                                $array2 = array("id" => $idpoi, "score" => $score2, "name" => $namepoi, "description" => $description, "lng" => $lon, "lat" => $lat, "visit_time" => -1, "max_capacity" => -1, "opening_times" => -1);
                            }
                        }
                        array_push($array, $array2);
                    }
                }
            }
        }
    }
    $array = json_encode($array);
    return $array;
}