<?php

session_start();

$server = "127.0.0.1";
$username = "root";
$password = "";
$db = "poidb";

$mysqli = new mysqli($server, $username, $password, $db);

$id = $_GET['id'];

$datas = $mysqli->query("SELECT * FROM poi AS p INNER JOIN openingtimes AS o ON p.id = o.id WHERE p.id = '$id';");

$row = mysqli_fetch_assoc($datas);

$idpoi = $row['id'];
$name = $row['name'];
$description = $row['description'];
$lat = $row['lat'];
$lon = $row['lon'];
$url = $row['url'];
$openingtimes = $row['note'];

$_SESSION["id"] = $id;


echo "
    <head>
		<script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js\"></script>
		<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js\"></script>
	</head>

    <link rel=\"stylesheet\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css\" integrity=\"sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T\" crossorigin=\"anonymous\">
    
    <style> 
    div.ex {
        max-width: 1000px;
        margin: auto;
    }
    .pertitle{
        color: #ff0000;
    }
    </style>
    <br>
    <br>
    <p class=\"text-center h1\" style=\"font-weight:bold;\">Editing: <span class=\"pertitle\">" . $name . "</span></p><br>
    <div  style=\"text-align: center\">
        <form>
            <iframe width=\"600\" height=\"500\" frameborder=\"0\" scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\" 
            src=\"http://www.openstreetmap.org/export/embed.html?marker=" . $lat . "," . $lon ."\" style=\"border: 3px solid grey\">
            </iframe>
        </form>
    </div>
    
<form class=\"container\" action=\"confirmedit.php?id=" . $id . " method=\"post\"\">
    <div class=\"form-group ex\">
    <br>
    <br>
    <label for=\"name\" style=\"font-weight:bold;\">NAME: *</label><br>
    <input type=\"text\" id=\"name\" class=\"form-control\" name=\"name\" placeholder=\"" . $name . "\"><br>
    <label for=\"description\" style=\"font-weight:bold;\">DESCRIPTION: *</label><br>
    <textarea type=\"text\" id=\"description\" class=\"form-control\" name=\"description\" placeholder=\"" . $description . "\" rows=\"4\" cols=\"50\" required></textarea><br>
    <label for=\"lat\" style=\"font-weight:bold;\">LAT: *</label><br>
    <input type=\"text\" id=\"lat\" class=\"form-control\" name=\"lat\" placeholder=\"" . $lat . "\"><br>
    <label for=\"lon\" style=\"font-weight:bold;\">LON: *</label><br>
    <input type=\"text\" id=\"lon\" class=\"form-control\" name=\"lon\" placeholder=\"" . $lon . "\"><br>
    <label for=\"url\" style=\"font-weight:bold;\">URL:</label><br>
    <input type=\"text\" id=\"url\" class=\"form-control\" name=\"url\" placeholder=\"" . $url . "\"><br>
    <label for=\"url\" style=\"font-weight:bold;\">ACCESSIBLE: *</label><br>
    <select class=\"custom-select\" name=\"accessible\" id=\"acc\" required>
        <option selected value=\"yes\">YES</option>
        <option value=\"saab\">NO</option>
    </select>
    <br>
    <br>
    <div class=\"container\">
        <div class=\"form-group\">
            <form name=\"add_name\" id=\"add_name\">
                <div class=\"table-responsive\">
                    <table class=\"table table-bordered\" id=\"dynamic_field\">
                        <tr>
                            <td>
                                <form>
                                    <p  style=\"font-weight:bold;\">OPENING TIMES:</p>
                                    <br>
                                    From the date:
                                    <input type=\"date\" id=\"start\" name=\"start\" value=\"\" min=\"2021-01-31\" max=\"2050-12-31\" required>
                                    To the date: 
                                    <input type=\"date\" id=\"start\" name=\"end\" value=\"\" min=\"2021-01-31\" max=\"2050-12-31\" required>
                                    <br>  
                                    <br>
                                    <label for=\"cars\">From the day:</label>
                                    <select class=\"custom-select\" name=\"dayfrom\" id=\"acc\" required>
                                        <option value=\"Mo\">Monday</option>
                                        <option value=\"Tu\">Tuesday</option>
                                        <option value=\"We\">Wednesday</option>
                                        <option value=\"Th\">Thursday</option>
                                        <option value=\"Fr\">Friday</option>
                                        <option value=\"Sa\">Saturday</option>
                                        <option value=\"Su\">Sunday</option>
                                    </select>
                                    <br>
                                    <br>
                                    <label for=\"cars\">To the day:</label>
                                    <select class=\"custom-select\" name=\"dayto\" id=\"acc\" required>
                                        <option value=\"Mo\">Monday</option>
                                        <option value=\"Tu\">Tuesday</option>
                                        <option value=\"We\">Wednesday</option>
                                        <option value=\"Th\">Thursday</option>
                                        <option value=\"Fr\">Friday</option>
                                        <option value=\"Sa\">Saturday</option>
                                        <option value=\"Su\">Sunday</option>
                                    </select>
                                    <br>  
                                    <br>
                                    From:
                                    <input type=\"time\" id=\"appt\" name=\"hourfrom\" required>
                                    To:
                                    <input type=\"time\" id=\"appt\" name=\"hourto\" required>
                                    <br>
                                    <br>
                                    <button type=\"button\" name=\"add\" id=\"add\" class=\"btn btn-success center\" style=\"float: right;\">Add More</button>
                                </form>
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
    </div>
<div style=\"text-align: center\">
    <input type=\"submit\" name=\"submit\" id=\"submit\" class=\"btn btn-success\" value=\"Submit\" style=\"height:60px; width:180px;\"/>
</div>
<br>
<br>
</form>

    <script>
$(document).ready(function(){
	var i=1;
	$('#add').click(function(){
		i++;
        $('#dynamic_field').append('<tr id=\"row'+i+'\"><td><form> <p  style=\"font-weight:bold;\">OPENING TIMES:</p><br>From the date:<input type=\"date\" id=\"start\" name=\"start\" value=\"\" min=\"2021-01-31\" max=\"2050-12-31\" required>To the date: <input type=\"date\" id=\"start\" name=\"end\" value=\"\" min=\"2021-01-31\" max=\"2050-12-31\" required><br>  <br><label for=\"cars\">From the day:</label><select class=\"custom-select\" name=\"dayfrom\" id=\"acc\" required><option value=\"Mo\">Monday</option><option value=\"Tu\">Tuesday</option><option value=\"We\">Wednesday</option><option value=\"Th\">Thursday</option><option value=\"Fr\">Friday</option><option value=\"Sa\">Saturday</option><option value=\"Su\">Sunday</option></select><br><br><label for=\"cars\">To the day:</label><select class=\"custom-select\" name=\"dayto\" id=\"acc\" required><option value=\"Mo\">Monday</option><option value=\"Tu\">Tuesday</option><option value=\"We\">Wednesday</option><option value=\"Th\">Thursday</option><option value=\"Fr\">Friday</option><option value=\"Sa\">Saturday</option><option value=\"Su\">Sunday</option></select><br>  <br>From:<input type=\"time\" id=\"appt\" name=\"hourfrom\" required>To:<input type=\"time\" id=\"appt\" name=\"hourto\" required><br><br><button type=\"button\" name=\"remove\" id=\"'+i+'\" class=\"btn btn-danger btn_remove\">X</button></form></td></tr>');
    });
	
	$(document).on('click', '.btn_remove', function(){
		var button_id = $(this).attr(\"id\"); 
		$('#row'+button_id+'').remove();
	});
});
</script>

";
