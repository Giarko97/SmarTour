<?php

set_time_limit(0);

include_once 'portal.php';
include_once 'pdo.php';
include_once 'iterate.php';
include_once 'popularity.php';
include_once 'normalizedPop.php';
include_once 'OSMscore.php';

$pdo = connessione();

foreach($pdo->query("SELECT wikipedia, id FROM poi WHERE wikipedia != \"\" LIMIT 8") as $row){

    $poi = $row['wikipedia'];
    $idpoi = $row['id'];

    $jump = 0;

    $multiRes = portal($poi, $jump);
    iterate($poi, $multiRes);
    //osmScore($idpoi);
    popularity($poi);
}

pop();

foreach($pdo->query("SELECT id FROM poi WHERE wikipedia = \"\" LIMIT 8") as $row){

    $idpoi = $row['id'];

    //osmScore($idpoi);
}

$pdo = null;

//echo "<script> document.location.href='../administrator/poitoiscore.php';</script>";

?>

</pre>
