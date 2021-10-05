<?php

session_start();

$server = "127.0.0.1";
$username = "root";
$password = "";
$db = "poidb";

$mysqli = new mysqli($server, $username, $password, $db);

$id = $_SESSION['id'];

$sql = "DELETE FROM poi WHERE id = '$id'";

if (mysqli_query($mysqli, $sql)) {
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
    echo "Error deleting record: " . mysqli_error($mysqli);
}
