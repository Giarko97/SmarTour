<?php

session_start();

$server = "127.0.0.1";
$username = "root";
$password = "";
$db = "poidb";

$mysqli = new mysqli($server, $username, $password, $db);

$id = $_GET['id'];

$_SESSION["id"] = $id;

$datas = $mysqli->query("SELECT * FROM poi WHERE id = '$id'");

$row = mysqli_fetch_assoc($datas);

$name = $row['name'];

echo"
<script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js\"></script>
<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js\"></script>
<link rel=\"stylesheet\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css\" integrity=\"sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T\" crossorigin=\"anonymous\">


<div class=\"container\">
    <div style=\"text-align: center\">
    <h3>ARE YOU SURE YOU WANT DELETE " . $name . "? </h3>
    <br>
        <button class=\"btn btn-success center\" onclick=\"document.location='poimanager.php'\">Back to HOME</button>
        <button class=\"btn btn-danger btn_remove\" onclick=\"document.location='confirmdelete.php'\">DELETE</button>
    </div>
</div>
";


