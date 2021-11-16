<pre>

<?php

include_once 'pdo.php';

$res = sendToi();
print_r(json_decode($res));

function sendToi(){
    $array = array();
    $pdo = connessione();
    $toi1 = $pdo->query("SELECT * FROM toi1;");
    foreach($toi1 as $res){
        $id1 = $res['id'];
        $name1 = $res['names'];
        $toi2 = $pdo->query("SELECT * FROM toi2 WHERE idtoi1 = '$id1';");
        $array3 = array();
        $array4 = array();
        foreach($toi2 as $res2){
            $id2 = $res2['id'];
            $name2 = $res2['names'];
            $array3 = array("id" => "$id2", "name" => $name2);
            array_push($array4, $array3);            
        }
        $array2 = array("id" => "$id1", "name" => $name1, "item" => $array4);
        array_push($array, array("$name1" => $array2));
    }

    $toiJson = json_encode($array);

    return $toiJson;

}