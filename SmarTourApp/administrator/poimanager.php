<?php

$server = "127.0.0.1";
$username = "root";
$password = "";
$db = "poidb";

$mysqli = new mysqli($server, $username, $password, $db);

$datas = $mysqli->query("SELECT * FROM poi;");

echo "
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css\">
    <link rel=\"stylesheet\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css\" integrity=\"sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T\" crossorigin=\"anonymous\">
    <style>
    table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
    }
    th, td {
    padding: 5px;
    text-align: left;    
    }

        body {font-family: Arial, Helvetica, sans-serif;}
    * {box-sizing: border-box;}

    /* Button used to open the contact form - fixed at the bottom of the page */
    .open-button {
    background-color: #555;
    color: white;
    padding: 16px 20px;
    border: none;
    cursor: pointer;
    opacity: 0.8;
    }

    /* The popup form - hidden by default */
    .form-popup {
    display: none;
    bottom: 0;
    right: 15px;
    border: 3px solid #f1f1f1;
    z-index: 9;
    }

    /* Add styles to the form container */
    .form-container {
    max-width: 300px;
    padding: 10px;
    }

    /* Full-width input fields */
    .form-container input[type=text], .form-container input[type=password] {
    width: 100%;
    padding: 15px;
    margin: 5px 0 22px 0;
    border: none;
    background: #f1f1f1;
    }

    /* When the inputs get focus, do something */
    .form-container input[type=text]:focus, .form-container input[type=password]:focus {
    background-color: #ddd;
    outline: none;
    }

    /* Set a style for the submit/login button */
    .form-container .btn {
    justify-content: center;
    align-items: center;
    background-color: #04AA6D;
    color: white;
    padding: 16px 20px;
    border: none;
    cursor: pointer;
    width: 100%;
    margin-bottom:10px;
    opacity: 0.8;
    }

    /* Add a red background color to the cancel button */
    .form-container .cancel {
    background-color: red;
    margin-left: 12;
    margin-top: 10;
    }

    /* Add some hover effects to buttons */
    .form-container .btn:hover, .open-button:hover {
    opacity: 1;
    }
    </style>

    <script>
    function openForm(id) {
    document.getElementById(\"myForm\" + id).style.display = \"block\";
    }

    function closeForm(id) {
    document.getElementById(\"myForm\" + id).style.display = \"none\";
    }

    </script>

    <script src=\"https://www.kryogenix.org/code/browser/sorttable/sorttable.js\"></script>

    <br>
    <br>

    <h1 style=\"text-align:center;color:#151B54;font-weight:bold;\">POI MANAGER</h1>

    <br>
    <br>

    <div style=\"text-align: center\">
        <button style=\"height:80px; width:200px;\" class=\"btn btn-success\" onclick=\"document.location='selectpoi.php'\"><i class=\"fa fa-plus\"> CREATE NEW POI</i></button>
        <button style=\"height:80px; width:200px;\" class=\"btn btn-primary\" onclick=\"document.location='../classifier/popScore.php'\"><i class=\"fa fa-fire\"> POPULARITY </i></button>
        <button style=\"height:80px; width:200px;\" class=\"btn btn-warning\" onclick=\"document.location='poitoiscore.php'\"><i class=\"fa fa-star\"> POI-TOI SCORE</i></button>
        <button style=\"height:80px; width:200px;\" class=\"btn btn-danger\" onclick=\"document.location='../classifier/main.php'\"><i class=\"fa fa-play-circle\"> RUN SCORE </i></button>
        <button style=\"height:80px; width:200px;\" class=\"btn btn-dark\" onclick=\"document.location='set_zone.html'\"><i class=\"fa fa-plus-square\"> SET NEW ZONE</i></button>
    </div>

    <br>
    <br>

    <table class=\"sortable table table-striped\">
    <thead class=\"thead-dark\">
    <tr>
    <th scope=\"col\" style=\"text-align: center\">ID <i class=\"fa fa-unsorted\"></i></th>
    <th scope=\"col\" style=\"text-align: center\">NAME <i class=\"fa fa-unsorted\"></i></th>
    <th scope=\"col\" style=\"text-align: center\">LAT <i class=\"fa fa-unsorted\"></i></th>
    <th scope=\"col\" style=\"text-align: center\">LON <i class=\"fa fa-unsorted\"></i></th>
    <th scope=\"col\" style=\"text-align: center\">POSITION</th>
    <th scope=\"col\" style=\"text-align: center\">MISSING</th>
    <th scope=\"col\" style=\"text-align: center\">% COMPL <i class=\"fa fa-unsorted\"></i></th>
    <th scope=\"col\" style=\"text-align: center\">ACTION</th>
    </tr>
    </thead>";

while($r = mysqli_fetch_assoc($datas)) {
    $missing = "";
    $c = 0;
    if($r['names'] == ""){
        $missing = $missing . "names" . "<br>";
        $c++;
    }
    if($r['description'] == ""){
        $missing = $missing . "description" . "<br>";
        $c++;
    }
    if($r['lat'] == ""){
        $missing = $missing . "lat" . "<br>";
        $c++;
    }
    if($r['lon'] == ""){
        $missing = $missing . "lon" . "<br>";
        $c++;
    }
    if($r['wikipedia'] == ""){
        $missing = $missing . "wikipedia" . "<br>";
        $c++;
    }
    if($r['access'] == ""){
        $missing = $missing . "access" . "<br>";
        $c++;
    }

    $percentage = intval(100 - $c/7*100) . "%";

    echo "
    <tr>
    <td style=\"text-align: center; vertical-align:middle\">" . $r['id'] . "</td>
    <td style=\"text-align: center; vertical-align:middle\">" . $r['names'] . "</td>
    <td style=\"text-align: center; vertical-align:middle\">" . $r['lat'] . "</td>
    <td style=\"text-align: center; vertical-align:middle\">" . $r['lon'] . "</td>
    <td style=\"text-align: center; vertical-align:middle\">    
        <button class=\"btn btn-info\" class=\"open-button\" onclick=\"openForm(" . $r['id'] . ")\"><i class=\"fa fa-map-marker\"> View on map</i></button>
        <div class=\"form-popup\" id=\"myForm" . $r['id'] . "\">
        <form class=\"form-container\">

            <iframe width=\"300\" height=\"260\" frameborder=\"0\" scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\" 
            src=\"http://www.openstreetmap.org/export/embed.html?marker=" . $r['lat'] . "," . $r['lon'] ."\" 
            style=\"border: 3px solid grey\">

            </iframe>
            <br/>
            <button type=\"button\" class=\"btn cancel\" onclick=\"closeForm(" . $r['id'] . ")\">Close</button>
        </form>
        </div>
    </td>
    
    <td style=\"vertical-align:middle\">" . $missing . "</td>
    <td style=\"text-align: center; vertical-align:middle\">" . $percentage . "</td>
    <td style=\"text-align: center; vertical-align:middle\">
        <button class=\"btn btn-primary\" onclick=\"document.location='edit.php?id=" . $r['id'] . "'\"><i class=\"fa fa-edit\"> Edit</i></button>
        <button class=\"btn btn-danger\" onclick=\"document.location='delete.php?id=" . $r['id'] . "'\"><i class=\"fa fa-trash\"> Delete</i></button>
    </td>
    </tr>";
    }
echo "</table>";