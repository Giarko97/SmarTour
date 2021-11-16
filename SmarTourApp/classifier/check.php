<?php

include_once 'pdo.php';
include 'getToi.php';


function check($poi, $portal, $score){
    $toi = getToi();
    if(in_array($portal, $toi)){
        $pdo = connessione();
        try{
            $poi = str_replace("'", "\'", $poi);
            $portal = str_replace("'", "\'", $portal);
            if($pdo->query("SELECT * FROM toi2 WHERE names = '$portal'")->rowCount() != 0 || $pdo->query("SELECT * FROM toi1 WHERE names = '$portal'")->rowCount() != 0){
                $rn = $pdo->query("SELECT * FROM score_portal WHERE poi = '$poi' AND toi = '$portal' AND score > '$score'")->rowCount();
                if($rn == 0){
                        //$pdo->exec("DELETE FROM scorept WHERE poi = '$poi' AND toi = '$portal'");
                        $pdo->exec("INSERT INTO score_portal (poi, toi, score) VALUE ('$poi', '$portal', '$score')");
                }
            }
        }catch(PDOException $pdoe){
            echo $pdoe->getMessage();
        }
        $pdo = null;
    }
}
