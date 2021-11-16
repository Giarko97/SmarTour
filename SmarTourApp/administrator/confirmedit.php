<?php

session_start();

$server = "127.0.0.1";
$username = "root";
$password = "";
$db = "poidb";

$mysqli = new mysqli($server, $username, $password, $db);

$id = $_SESSION['id'];

$datas = $mysqli->query("SELECT * FROM poi AS p INNER JOIN openingtimes AS o ON p.id = o.id WHERE p.id = '$id';");

$row = mysqli_fetch_assoc($datas);

$idpoi = $row['id'];
$name = $row['name'];
$description = $row['description'];
$lat = $row['lat'];
$lon = $row['lon'];
$url = $row['url'];
$openingtimes = $row['note'];
$dayfrom = $row['day_from'];
$dayto = $row['day_to'];
$hourfrom = $row['hour_from'];
$hourto = $row['hour_to'];
$datefrom = $row['date_from'];
$dateto = $row['date_to'];


if($_GET['description'] != ""){
    $description = $_GET['description'];
}
if($_GET['lat'] != ""){
    $lat = $_GET['lat'];
}
if($_GET['lon'] != ""){
    $lon = $_GET['lon'];
}
if($_GET['url'] != ""){
    $url = $_GET['url'];
}
if($_GET['start'] != ""){
    $datefrom = $_GET['start'];
}
if($_GET['end'] != ""){
    $dateto = $_GET['end'];
}
if($_GET['hourfrom'] != ""){
    $hourfrom = $_GET['hourfrom'];
}
if($_GET['hourto'] != ""){
    $hourto = $_GET['hourto'];
}
if($_GET['dayfrom'] != ""){
    $dayfrom = $_GET['dayfrom'];
}
if($_GET['dayto'] != ""){
    $dayto = $_GET['dayto'];
}
if($_GET['accessible'] != ""){
    $accessible = $_GET['accessible'];
}


$datas = $mysqli->query("UPDATE poi SET name = '$name', description = '$description', lat = '$lat', lon = '$lon', url = '$url' WHERE id = '$id'");
$datas = $mysqli->query("UPDATE openingtimes SET day_from = '$dayfrom',
                                                day_to = '$dayto',
                                                hour_from = '$hourfrom',
                                                hour_to = '$hourto',
                                                date_from = '$datefrom',
                                                date_to = '$dateto',
                                                note = '$openingtimes' WHERE id = '$id'");


echo "
<script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js\"></script>
<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js\"></script>
<link rel=\"stylesheet\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css\" integrity=\"sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T\" crossorigin=\"anonymous\">


<div class=\"container\" style=\"text-align: center\">
    <h3>OPERATION CARRIED OUT SUCCESSFULLY</h3>
    <br>
    <div style=\"text-align: center\">
        <button class=\"btn btn-success\" onclick=\"document.location='poimanager.php'\">Back to HOME</button>
    </div>
</div>
";