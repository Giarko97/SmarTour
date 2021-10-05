<?php

session_start();

$server = "127.0.0.1";
$username = "root";
$password = "";
$db = "poidb";

$mysqli = new mysqli($server, $username, $password, $db);

$lat = $_SESSION['lat'];
$lon = $_SESSION['lon'];
$name = $_GET['name'];
$description = $_GET['description'];
$url = $_GET['url'];
$accessible = $_GET['accessible'];
$datefrom = $_GET['start'];
$dateto = $_GET['end'];
$hourfrom = $_GET['hourfrom'];
$hourto = $_GET['hourto'];
$dayfrom = $_GET['dayfrom'];
$dayto = $_GET['dayto'];
$openingtimes = "";

$count = 0;

$id = rand(1, 999999999);
$asking = ("SELECT * FROM poi WHERE id = '$id'");
$exist = $mysqli->query($asking);
$count = $exist->num_rows;

if($count == 0){

  $sql1 = "INSERT INTO poi (id, name, description, lat, lon, url)
  VALUES ('$id', '$name', '$description', '$lat', '$lon', '$url');";
  
  $sql2 = "INSERT INTO openingtimes (id, day_from, day_to, hour_from, hour_to, date_from, date_to, note)
  VALUES ('$id', '$dayfrom', '$dayto', '$hourfrom', '$hourto', '$datefrom','$dateto', '$openingtimes');";

  if (mysqli_query($mysqli, $sql1) && mysqli_query($mysqli, $sql2)) {
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
    } else {
      echo "Error creating record: " . mysqli_error($mysqli);
    }
}
