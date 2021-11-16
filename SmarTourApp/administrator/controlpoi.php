<?php

$server = "127.0.0.1";
$username = "root";
$password = "";
$db = "poidb";

//distanza dell'intorno di controllo esistenza POI
$dist = 0.25;

function degreetometer($lata, $lona, $latb, $lonb){
    $R = 6371;
    $pigreco = 3.1415927;

    $lat_alfa = $pigreco * $lata / 180;
    $lat_beta = $pigreco * $latb / 180;
    $lon_alfa = $pigreco * $lona / 180;
    $lon_beta = $pigreco * $lonb / 180;

    $fi = abs($lon_alfa - $lon_beta);

    $p = acos(sin($lat_beta) * sin($lat_alfa) + 
        cos($lat_beta) * cos($lat_alfa) * cos($fi));

    $d = $p * $R;
    return($d);
}

$mysqli = new mysqli($server, $username, $password, $db);

$lat = $_GET['lat'];
$lon = $_GET['lon'];

$datas = $mysqli->query("SELECT * FROM poi");

echo"
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css\">
    <link rel=\"stylesheet\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css\" integrity=\"sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T\" crossorigin=\"anonymous\">
    ";

echo "
<div style=\"text-align: center\">
    <h3>IS YOUR POI AMONG THESE? </h3>
</div>";

echo"
    <table class=\"table table-striped\">
    <thead class=\"thead-dark\">
    <tr>
        <th scope=\"col\" style=\"text-align: center\">POI</th>
        <th scope=\"col\" style=\"text-align: center\">ACTION</i></th>
    </tr>
    </thead>
";
/*
while($r = mysqli_fetch_assoc($datas)) {
    if ($r['lat'] - $lat <= 0.001 && $r['lon'] - $lon <= 0.001){
        echo"
        <tr>
        <td style=\"text-align: center; vertical-align:middle\">" . $r['name'] . "</td>
        <td style=\"text-align: center; vertical-align:middle\"><button class=\"btn btn-primary\" onclick=\"document.location='edit.php?id=" . $r['id'] . "'\"><i class=\"fa fa-edit\">EDIT</i></button></td>
        </tr>";
    }
}
*/
while($r = mysqli_fetch_assoc($datas)) {
    if (degreetometer($r['lat'], $r['lon'], $lat, $lon) <= $dist){
        echo"
        <tr>
        <td style=\"text-align: center; vertical-align:middle\">" . $r['name'] . "</td>
        <td style=\"text-align: center; vertical-align:middle\"><button class=\"btn btn-primary\" onclick=\"document.location='edit.php?id=" . $r['id'] . "'\"><i class=\"fa fa-edit\">EDIT</i></button></td>
        </tr>";
    }
}

echo "
    </table>
    <div style=\"text-align: center\">
        <button class=\"btn btn-danger\" onclick=\"document.location='createpoi.php?lat=" . $lat . "&lon=" . $lon . "'\"><i class=\"fa fa-plus\"> NO, CREATE</i></button>
    </div>
    ";