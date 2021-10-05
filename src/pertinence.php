<?php
$poi = "Colosseo";

$endPoint = "https://it.wikipedia.org/w/api.php";
$params = [
    "action" => "query",
    "titles" => $poi,
    "prop" => "info",
    //"prop" => "langlinks",
    //"lllimit" => "500",
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
foreach($data['query']['pages'][$pageid]['langlinks'] as $value){
    $nlan++;
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

//Normalizzo i valori secondo i valori della pagina Wikipedia del Colosseo
$pagelength = $pagelength/54443;
$nlan = $nlan/200;
$views = $views/65000;

//Pesi dei valori, somma a 1 (da rivedere)
$views_load = 0.45;
$nlan_load = 0.3;
$pagelength_load = 0.25;

//Normalizzo la funzione popolarità a 1
$popularity = $views_load*$views + $nlan_load*$nlan + $pagelength_load*$pagelength;

echo $popularity;

echo "<br>";

echo $views . " - " . $nlan . " - " . $pagelength;
