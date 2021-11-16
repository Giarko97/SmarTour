<pre>

<?php

include_once 'pdo.php';

set_time_limit(0);

$pdo = connessione();

function osmScore($idpoi){

    $pdo = connessione();

    foreach($pdo->query("SELECT nw FROM poi WHERE id = '$idpoi' LIMIT 1") as $res){
        $nw = $res['nw'];
        if($nw == 0){
            $json = file_get_contents('http://overpass-api.de/api/interpreter?data=[out:json];node(id:' . $idpoi . ');out%20tags;');
            $nodes = json_decode($json);
            if(isset($nodes->elements)){
                foreach($nodes->elements as $elem){
                    if(isset($elem->tags)){
                        foreach($elem->tags as $key=>$value){
                            inser($idpoi, $key, $value);
                        }
                    }
                }
            }
        }else if($nw == 1){
            $json = file_get_contents('http://overpass-api.de/api/interpreter?data=[out:json];way(id:' . $idpoi . ');out%20tags;');
            $nodes = json_decode($json);
            if(isset($nodes->elements)){
                foreach($nodes->elements as $elem){
                    if(isset($elem->tags)){
                        foreach($elem->tags as $key=>$value){
                            inser($idpoi, $key, $value);
                        }
                    }
                }
            }
        }
    }
}


function inser($idpoi, $key, $value){
    $getcont = file_get_contents('osmToi.json');
    $config = json_decode($getcont);
    $pdo = connessione();
    foreach($config->tags as $name){
        if($name->name == $key){
            foreach($pdo->query("SELECT idtoi FROM tag_osm_toi WHERE names = '$key'") as $res){
                $idtoi = $res['idtoi'];
                if($pdo->query("SELECT * FROM score_osm WHERE idpoi = '$idpoi' AND idtoi = '$idtoi'")->rowCount() != 0){
                    $pdo->exec("DELETE FROM score_osm WHERE idpoi = '$idpoi' AND idtoi = '$idtoi'");
                    $pdo->exec("INSERT INTO score_osm (idpoi, idtoi, score) VALUES ('$idpoi', '$idtoi', '5')");
                }else{
                    $pdo->exec("INSERT INTO score_osm (idpoi, idtoi, score) VALUES ('$idpoi', '$idtoi', '5')");
                }
            }
        }
        foreach($name->values as $sub){
            if($sub == $value){
                foreach($pdo->query("SELECT idtoi FROM tag_osm_toi WHERE names = '$value'") as $res){
                    $idtoi = $res['idtoi'];
                    if($pdo->query("SELECT * FROM score_osm WHERE idpoi = '$idpoi' AND idtoi = '$idtoi'")->rowCount() != 0){
                        $pdo->exec("DELETE FROM score_osm WHERE idpoi = '$idpoi' AND idtoi = '$idtoi'");
                        $pdo->exec("INSERT INTO score_osm (idpoi, idtoi, score) VALUES ('$idpoi', '$idtoi', '5')");
                    }else{
                        $pdo->exec("INSERT INTO score_osm (idpoi, idtoi, score) VALUES ('$idpoi', '$idtoi', '5')");
                    }
                }
            }
        }
    }
}