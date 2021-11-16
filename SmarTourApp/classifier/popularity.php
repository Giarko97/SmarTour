<?php

include_once 'pdo.php';

function popularity($poi){
    $endPoint = "https://it.wikipedia.org/w/api.php";
    $params = [
        "action" => "query",
        "titles" => $poi,
        "prop" => "info",
        "format" => "json"
    ];

    $url = $endPoint . "?" . http_build_query( $params );

    $json = file_get_contents($url);
    $data = json_decode($json,true);

    //pageid
    foreach($data['query'] as $value){
        $pageid = array_key_first($value);
    };

    //lunghezza della pagina in byte
    $pagelength = $data['query']['pages'][$pageid]['length'];

    $params = [
        "action" => "query",
        "titles" => $poi,
        "prop" => "langlinks",
        "lllimit" => "500",
        "format" => "json"
    ];

    $url = $endPoint . "?" . http_build_query( $params );

    $json = file_get_contents($url);
    $data = json_decode($json,true);

    //numero lingue tradotte
    $nlan = 0;
    if(isset($data['query']['pages'][$pageid]['langlinks'])){
        foreach($data['query']['pages'][$pageid]['langlinks'] as $value){
            $nlan++;
       }
    }else{
        $nlan = 1;
    };

    $params = [
        "action" => "query",
        "titles" => $poi,
        "prop" => "pageviews",
        "format" => "json"
    ];

    $url = $endPoint . "?" . http_build_query( $params );

    $json = file_get_contents($url);
    $data = json_decode($json,true);

    //numero di visite mensili
    $views = 0;
    foreach($data['query']['pages'][$pageid]['pageviews'] as $value){
        $views += $value;
    }

    $pdo = connessione();
    $poi = str_replace("'", "\'", $poi);

    if($pdo->query("SELECT * FROM popular WHERE names = '$poi'")->rowCount() == 0){    
        $pdo->exec("INSERT INTO popular (names, nlan, views, lengths) VALUES ('$poi', '$nlan', '$views', '$pagelength')");
    }else{
        $pdo->exec("DELETE FROM popular WHERE names = '$poi'");
        $pdo->exec("INSERT INTO popular (names, nlan, views, lengths) VALUES ('$poi', '$nlan', '$views', '$pagelength')");
    }

    $pdo = null;

}