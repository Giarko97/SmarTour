<?php

$server = "127.0.0.1";
$username = "root";
$password = "";
$db = "poidb";

$mysqli = new mysqli($server, $username, $password, $db);

$datas = $mysqli->query("SELECT p.id, p.name, t.macro, t.name AS sub, pt.score FROM poi AS p INNER JOIN poitoiscore AS pt ON p.id = pt.idpoi INNER JOIN toi AS t ON pt.idtoi = t.id WHERE t.id%2!=0 GROUP BY p.id;");

    

echo "
    <script src=\"https://www.kryogenix.org/code/browser/sorttable/sorttable.js\"></script>
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css\">
    <link rel=\"stylesheet\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css\" integrity=\"sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T\" crossorigin=\"anonymous\">
    <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js\"></script>
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css\">
    <script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js\" integrity=\"sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS\" crossorigin=\"anonymous\"></script>
    <link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css\" integrity=\"sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7\" crossorigin=\"anonymous\" />
    
    <style>
        body{margin-top:20px;}

        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            }
            th, td {
            padding: 5px;
            text-align: left;    
            }
    
        .toggleDisplayHis {
        display: none;
        }
        .toggleDisplayHis.in {
        display: table-cell;
        }
    
        .toggleDisplayArt {
        display: none;
        }
        .toggleDisplayArt.in {
        display: table-cell;
        }
    
        .toggleDisplayArc {
        display: none;
        }
        .toggleDisplayArc.in {
        display: table-cell;
        }
    
        .toggleDisplayNat {
        display: none;
        }
        .toggleDisplayNat.in {
        display: table-cell;
        }
    
        .toggleDisplayMus {
        display: none;
        }
        .toggleDisplayMus.in {
        display: table-cell;
        }
    
        .toggleDisplayRel {
        display: none;
        }
        .toggleDisplayRel.in {
        display: table-cell;
        }
    </style>
    <br>
    <br>

    <h1 style=\"text-align:center;color:#151B54;font-weight:bold;\">POI TOI SCORE</h1>

    <br>
    <br>

    <div style=\"text-align: center\">
        <button style=\"height:80px; width:200px;\" class=\"btn btn-primary\" onclick=\"document.location='poimanager.php'\"><i class=\"fa fa-home\">  BACK TO HOME</i></button>
    </div>

    <br>
    <br>
    
                <table class=\"table table-striped\">
                <thead class=\"thead-dark\">
                    <tr>
                            <th style=\"text-align: center\">POI</th>
                            <th id=\"click-his\" style=\"text-align: center\">HISTORY<i class=\"fa fa-folder-open\"></i></th>
                            <th class=\"toggleDisplayHis\" style=\"text-align: center\">Roman</th>
                            <th class=\"toggleDisplayHis\" style=\"text-align: center\">Middle age</th>
                            <th class=\"toggleDisplayHis\" style=\"text-align: center\">Modern</th>
                            <th id=\"click-art\" style=\"text-align: center\">ART <i class=\"fa fa-folder-open\"></i></th>
                            <th class=\"toggleDisplayArt\" style=\"text-align: center\">Monuments</th>
                            <th class=\"toggleDisplayArt\" style=\"text-align: center\">Castles</th>
                            <th class=\"toggleDisplayArt\" style=\"text-align: center\">Other</th>
                            <th id=\"click-arc\" style=\"text-align: center\">ARCHITECTURE <i class=\"fa fa-folder-open\"></i></th>
                            <th class=\"toggleDisplayArc\" style=\"text-align: center\">Renaissance</th>
                            <th class=\"toggleDisplayArc\" style=\"text-align: center\">'800/'900</th>
                            <th class=\"toggleDisplayArc\" style=\"text-align: center\">Contemporary</th>
                            <th id=\"click-nat\" style=\"text-align: center\">NATURE <i class=\"fa fa-folder-open\"></i></th>
                            <th class=\"toggleDisplayNat\" style=\"text-align: center\">Parks</th>
                            <th class=\"toggleDisplayNat\" style=\"text-align: center\">Excursions</th>
                            <th class=\"toggleDisplayNat\" style=\"text-align: center\">Rivers/Lakes</th>
                            <th id=\"click-rel\" style=\"text-align: center\">RELIGION <i class=\"fa fa-folder-open\"></i></th>
                            <th class=\"toggleDisplayRel\" style=\"text-align: center\">Western</th>
                            <th class=\"toggleDisplayRel\" style=\"text-align: center\">Oriental</th>
                            <th class=\"toggleDisplayRel\" style=\"text-align: center\">Roman</th>
                            <th id=\"click-mus\" style=\"text-align: center\">MUSIC <i class=\"fa fa-folder-open\"></i></th>
                            <th class=\"toggleDisplayMus\" style=\"text-align: center\">Exhibitions</th>
                            <th class=\"toggleDisplayMus\" style=\"text-align: center\">Live</th>
                            <th class=\"toggleDisplayMus\" style=\"text-align: center\">Festival</th>
                        </tr>
                        </thead>";
                    
while($r = mysqli_fetch_assoc($datas)) {

    $data = $mysqli->query("SELECT score FROM poitoiscore WHERE idpoi='" . $r['id'] . "' AND idtoi = '2';");
    $s = mysqli_fetch_assoc($data);
    $h1 = $s['score'];
    $data = $mysqli->query("SELECT score FROM poitoiscore WHERE idpoi='" . $r['id'] . "' AND idtoi = '4';");
    $s = mysqli_fetch_assoc($data);
    $h2 = $s['score'];
    $data = $mysqli->query("SELECT score FROM poitoiscore WHERE idpoi='" . $r['id'] . "' AND idtoi = '6';");
    $s = mysqli_fetch_assoc($data);
    $h3 = $s['score'];
    $data = $mysqli->query("SELECT score FROM poitoiscore WHERE idpoi='" . $r['id'] . "' AND idtoi = '8';");
    $s = mysqli_fetch_assoc($data);
    $ac1 = $s['score'];
    $data = $mysqli->query("SELECT score FROM poitoiscore WHERE idpoi='" . $r['id'] . "' AND idtoi = '10';");
    $s = mysqli_fetch_assoc($data);
    $ac2 = $s['score'];
    $data = $mysqli->query("SELECT score FROM poitoiscore WHERE idpoi='" . $r['id'] . "' AND idtoi = '12';");
    $s = mysqli_fetch_assoc($data);
    $ac3 = $s['score'];
    $data = $mysqli->query("SELECT score FROM poitoiscore WHERE idpoi='" . $r['id'] . "' AND idtoi = '14';");
    $s = mysqli_fetch_assoc($data);
    $at1 = $s['score'];
    $data = $mysqli->query("SELECT score FROM poitoiscore WHERE idpoi='" . $r['id'] . "' AND idtoi = '16';");
    $s = mysqli_fetch_assoc($data);
    $at2 = $s['score'];
    $data = $mysqli->query("SELECT score FROM poitoiscore WHERE idpoi='" . $r['id'] . "' AND idtoi = '18';");
    $s = mysqli_fetch_assoc($data);
    $at3 = $s['score'];
    $data = $mysqli->query("SELECT score FROM poitoiscore WHERE idpoi='" . $r['id'] . "' AND idtoi = '20';");
    $s = mysqli_fetch_assoc($data);
    $n1 = $s['score'];
    $data = $mysqli->query("SELECT score FROM poitoiscore WHERE idpoi='" . $r['id'] . "' AND idtoi = '22';");
    $s = mysqli_fetch_assoc($data);
    $n2 = $s['score'];
    $data = $mysqli->query("SELECT score FROM poitoiscore WHERE idpoi='" . $r['id'] . "' AND idtoi = '24';");
    $s = mysqli_fetch_assoc($data);
    $n3 = $s['score'];
    $data = $mysqli->query("SELECT score FROM poitoiscore WHERE idpoi='" . $r['id'] . "' AND idtoi = '26';");
    $s = mysqli_fetch_assoc($data);
    $r1 = $s['score'];
    $data = $mysqli->query("SELECT score FROM poitoiscore WHERE idpoi='" . $r['id'] . "' AND idtoi = '28';");
    $s = mysqli_fetch_assoc($data);
    $r2 = $s['score'];
    $data = $mysqli->query("SELECT score FROM poitoiscore WHERE idpoi='" . $r['id'] . "' AND idtoi = '30';");
    $s = mysqli_fetch_assoc($data);
    $r3 = $s['score'];
    $data = $mysqli->query("SELECT score FROM poitoiscore WHERE idpoi='" . $r['id'] . "' AND idtoi = '32';");
    $s = mysqli_fetch_assoc($data);
    $m1 = $s['score'];
    $data = $mysqli->query("SELECT score FROM poitoiscore WHERE idpoi='" . $r['id'] . "' AND idtoi = '34';");
    $s = mysqli_fetch_assoc($data);
    $m2 = $s['score'];
    $data = $mysqli->query("SELECT score FROM poitoiscore WHERE idpoi='" . $r['id'] . "' AND idtoi = '36';");
    $s = mysqli_fetch_assoc($data);
    $m3 = $s['score'];

    $h = intval(($h1 + $h2 + $h3)/3);
    $ac = intval(($ac1 + $ac2 + $ac3)/3);
    $at = intval(($at1 + $at2 + $at3)/3);
    $n = intval(($n1 + $n2 + $n3)/3);
    $re = intval(($r1 + $r2 + $r3)/3);
    $m = intval(($m1 + $m2 + $m3)/3);

    $name = $r['name'];

    echo "
                        
                        <tr>
                            <th scope=\"row\">" . $name . "</th>
                            <td style=\"text-align: center\">" . $h . " <i class=\"fa fa-star\"></i></td>
                            <td class=\"toggleDisplayHis\" style=\"text-align: center\">" . $h1 . " <i class=\"fa fa-star\"></i></td>
                            <td class=\"toggleDisplayHis\" style=\"text-align: center\">" . $h2 . " <i class=\"fa fa-star\"></i></td>
                            <td class=\"toggleDisplayHis\" style=\"text-align: center\">" . $h3 . " <i class=\"fa fa-star\"></i></td>
                            <td style=\"text-align: center\">" . $at . " <i class=\"fa fa-star\"></i></td>
                            <td class=\"toggleDisplayArt\" style=\"text-align: center\">" . $at1 . " <i class=\"fa fa-star\"></i></td>
                            <td class=\"toggleDisplayArt\" style=\"text-align: center\">" . $at2 . " <i class=\"fa fa-star\"></i></td>
                            <td class=\"toggleDisplayArt\" style=\"text-align: center\">" . $at3 . " <i class=\"fa fa-star\"></i></td>
                            <td style=\"text-align: center\">" . $ac . " <i class=\"fa fa-star\"></i></td>
                            <td class=\"toggleDisplayArc\" style=\"text-align: center\">" . $ac1 . " <i class=\"fa fa-star\"></i></td>
                            <td class=\"toggleDisplayArc\" style=\"text-align: center\">" . $ac2 . " <i class=\"fa fa-star\"></i></td>
                            <td class=\"toggleDisplayArc\" style=\"text-align: center\">" . $ac3 . " <i class=\"fa fa-star\"></i></td>
                            <td style=\"text-align: center\">" . $n . " <i class=\"fa fa-star\"></i></td>
                            <td class=\"toggleDisplayNat\" style=\"text-align: center\">" . $n1 . " <i class=\"fa fa-star\"></i></td>
                            <td class=\"toggleDisplayNat\" style=\"text-align: center\">" . $n2 . " <i class=\"fa fa-star\"></i></td>
                            <td class=\"toggleDisplayNat\" style=\"text-align: center\">" . $n3 . " <i class=\"fa fa-star\"></i></td>
                            <td style=\"text-align: center\">" . $re . " <i class=\"fa fa-star\"></i></td>
                            <td class=\"toggleDisplayMus\" style=\"text-align: center\">" . $r1 . " <i class=\"fa fa-star\"></i></td>
                            <td class=\"toggleDisplayMus\" style=\"text-align: center\">" . $r2 . " <i class=\"fa fa-star\"></i></td>
                            <td class=\"toggleDisplayMus\" style=\"text-align: center\">" . $r3 . " <i class=\"fa fa-star\"></i></td>
                            <td style=\"text-align: center\">" . $m . "<i class=\"fa fa-star\"></i></td>
                            <td class=\"toggleDisplayRel\" style=\"text-align: center\">" . $m1 . " <i class=\"fa fa-star\"></i></td>
                            <td class=\"toggleDisplayRel\" style=\"text-align: center\">" . $m2 . " <i class=\"fa fa-star\"></i></td>
                            <td class=\"toggleDisplayRel\" style=\"text-align: center\">" . $m3 . " <i class=\"fa fa-star\"></i></td>
                        </tr>";
}


echo"
</table>
    <script>
    $(\"#click-his\").click(function() {
    $(\".table .toggleDisplayHis\").toggleClass(\"in\");
    }); 
    $(\"#click-art\").click(function() {
    $(\".table .toggleDisplayArt\").toggleClass(\"in\");
    }); 
    $(\"#click-arc\").click(function() {
    $(\".table .toggleDisplayArc\").toggleClass(\"in\");
    }); 
    $(\"#click-nat\").click(function() {
    $(\".table .toggleDisplayNat\").toggleClass(\"in\");
    }); 
    $(\"#click-rel\").click(function() {
    $(\".table .toggleDisplayRel\").toggleClass(\"in\");
    }); 
    $(\"#click-mus\").click(function() {
    $(\".table .toggleDisplayMus\").toggleClass(\"in\");
    }); 
    </script>";