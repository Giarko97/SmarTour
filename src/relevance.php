<?php

$poi = "Colosseo";

$endPoint = "https://it.wikipedia.org/w/api.php";
$params = [
    "action" => "query",
    "titles" => $poi,
    "prop" => "extracts",
    "format" => "json"
];

$url = $endPoint . "?" . http_build_query( $params );

$json = file_get_contents($url);
$data = json_decode($json,true);

print_r($data);