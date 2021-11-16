<?php

$server = "127.0.0.1";
$username = "root";
$password = "";
$db = "poidb";

$mysqli = new mysqli($server, $username, $password, $db);

$datas = $mysqli->query("SELECT * FROM score_osm GROUP BY idpoi");
   

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
                            <th class=\"toggleDisplayHis\" style=\"text-align: center\">Preistoria</th>
                            <th class=\"toggleDisplayHis\" style=\"text-align: center\">Mondo classico</th>
                            <th class=\"toggleDisplayHis\" style=\"text-align: center\">Medioevo</th>
                            <th class=\"toggleDisplayHis\" style=\"text-align: center\">Storia moderna e contemporanea</th>
                            <th class=\"toggleDisplayHis\" style=\"text-align: center\">Altro</th>
                            <th id=\"click-art\" style=\"text-align: center\">ART <i class=\"fa fa-folder-open\"></i></th>
                            <th class=\"toggleDisplayArt\" style=\"text-align: center\">Architettura</th>
                            <th class=\"toggleDisplayArt\" style=\"text-align: center\">Pittura</th>
                            <th class=\"toggleDisplayArt\" style=\"text-align: center\">Scultura</th>
                            <th class=\"toggleDisplayArt\" style=\"text-align: center\">Musica</th>
                            <th class=\"toggleDisplayArt\" style=\"text-align: center\">Altro</th>
                            <th id=\"click-nat\" style=\"text-align: center\">NATURE <i class=\"fa fa-folder-open\"></i></th>
                            <th class=\"toggleDisplayNat\" style=\"text-align: center\">Giardini zoologici</th>
                            <th class=\"toggleDisplayNat\" style=\"text-align: center\">Parchi e orti botanici</th>
                            <th class=\"toggleDisplayNat\" style=\"text-align: center\">Altro</th>
                            <th id=\"click-rel\" style=\"text-align: center\">RELIGION <i class=\"fa fa-folder-open\"></i></th>
                            <th class=\"toggleDisplayRel\" style=\"text-align: center\">Cristianesimo</th>
                            <th class=\"toggleDisplayRel\" style=\"text-align: center\">Altre religioni</th>
                            <th class=\"toggleDisplayRel\" style=\"text-align: center\">Altro</th>
                        </tr>
                        </thead>";
if(isset($datas)){                    
    while($r = mysqli_fetch_assoc($datas)) {

        $id = $r['idpoi'];

        $res = $mysqli->query("SELECT names FROM poi WHERE id = '$id' LIMIT 1");
        $names = mysqli_fetch_assoc($res);
        $name = $names['names'];
        /*
        $data = $mysqli->query("SELECT score FROM score_osm WHERE idpoi='$id' AND idtoi = '1';");
        $s = mysqli_fetch_assoc($data);
        if(isset($s['score'])){
            $h = $s['score'];
        }else{
            $h = 0;
        }
        */
        $data = $mysqli->query("SELECT score FROM score_osm WHERE idpoi='$id' AND idtoi = '1';");
        $s = mysqli_fetch_assoc($data);
        if(isset($s['score'])){
            $h1 = $s['score'];
        }else{
            $h1 = 0;
        }
        $data = $mysqli->query("SELECT score FROM score_osm WHERE idpoi='$id' AND idtoi = '2';");
        $s = mysqli_fetch_assoc($data);
        if(isset($s['score'])){
            $h2 = $s['score'];
        }else{
            $h2 = 0;
        }
        $data = $mysqli->query("SELECT score FROM score_osm WHERE idpoi='$id' AND idtoi = '3';");
        $s = mysqli_fetch_assoc($data);
        if(isset($s['score'])){
            $h3 = $s['score'];
        }else{
            $h3 = 0;
        }
        $data = $mysqli->query("SELECT score FROM score_osm WHERE idpoi='$id' AND idtoi = '4';");
        $s = mysqli_fetch_assoc($data);
        if(isset($s['score'])){
            $h4 = $s['score'];
        }else{
            $h4 = 0;
        }
        $data = $mysqli->query("SELECT score FROM score_osm WHERE idpoi='$id' AND idtoi = '5';");
        $s = mysqli_fetch_assoc($data);
        if(isset($s['score'])){
            $h5 = $s['score'];
        }else{
            $h5 = 0;
        }

        /*
        $data = $mysqli->query("SELECT score FROM score_osm WHERE idpoi='$id' AND idtoi = '7';");
        $s = mysqli_fetch_assoc($data);
        if(isset($s['score'])){
            $a = $s['score'];
        }else{
            $a = 0;
        }
        */
        $data = $mysqli->query("SELECT score FROM score_osm WHERE idpoi='$id' AND idtoi = '6';");
        $s = mysqli_fetch_assoc($data);
        if(isset($s['score'])){
            $a1 = $s['score'];
        }else{
            $a1 = 0;
        }
        $data = $mysqli->query("SELECT score FROM score_osm WHERE idpoi='$id' AND idtoi = '7';");
        $s = mysqli_fetch_assoc($data);
        if(isset($s['score'])){
            $a2 = $s['score'];
        }else{
            $a2 = 0;
        }
        $data = $mysqli->query("SELECT score FROM score_osm WHERE idpoi='$id' AND idtoi = '8';");
        $s = mysqli_fetch_assoc($data);
        if(isset($s['score'])){
            $a3 = $s['score'];
        }else{
            $a3 = 0;
        }
        $data = $mysqli->query("SELECT score FROM score_osm WHERE idpoi='$id' AND idtoi = '9';");
        $s = mysqli_fetch_assoc($data);
        if(isset($s['score'])){
            $a4 = $s['score'];
        }else{
            $a4 = 0;
        }
        $data = $mysqli->query("SELECT score FROM score_osm WHERE idpoi='$id' AND idtoi = '10';");
        $s = mysqli_fetch_assoc($data);
        if(isset($s['score'])){
            $a5 = $s['score'];
        }else{
            $a5 = 0;
        }

        /*
        $data = $mysqli->query("SELECT score FROM score_osm WHERE idpoi='$id' AND idtoi = '13';");
        $s = mysqli_fetch_assoc($data);
        if(isset($s['score'])){
            $n = $s['score'];
        }else{
            $n = 0;
        }
        */  
        $data = $mysqli->query("SELECT score FROM score_osm WHERE idpoi='$id' AND idtoi = '11';");
        $s = mysqli_fetch_assoc($data);
        if(isset($s['score'])){
            $n1 = $s['score'];
        }else{
            $n1 = 0;
        }
        $data = $mysqli->query("SELECT score FROM score_osm WHERE idpoi='$id' AND idtoi = '12';");
        $s = mysqli_fetch_assoc($data);
        if(isset($s['score'])){
            $n2 = $s['score'];
        }else{
            $n2 = 0;
        }
        $data = $mysqli->query("SELECT score FROM score_osm WHERE idpoi='$id' AND idtoi = '13';");
        $s = mysqli_fetch_assoc($data);
        if(isset($s['score'])){
            $n3 = $s['score'];
        }else{
            $n3 = 0;
        }

        /*
        $data = $mysqli->query("SELECT score FROM score_osm WHERE idpoi='$id' AND idtoi = '17';");
        $s = mysqli_fetch_assoc($data);
        if(isset($s['score'])){
            $r = $s['score'];
        }else{
            $r = 0;
        }
        */
        $data = $mysqli->query("SELECT score FROM score_osm WHERE idpoi='$id' AND idtoi = '14';");
        $s = mysqli_fetch_assoc($data);
        if(isset($s['score'])){
            $r1 = $s['score'];
        }else{
            $r1 = 0;
        }
        $data = $mysqli->query("SELECT score FROM score_osm WHERE idpoi='$id' AND idtoi = '15';");
        $s = mysqli_fetch_assoc($data);
        if(isset($s['score'])){
            $r2 = $s['score'];
        }else{
            $r2 = 0;
        }
        $data = $mysqli->query("SELECT score FROM score_osm WHERE idpoi='$id' AND idtoi = '16';");
        $s = mysqli_fetch_assoc($data);
        if(isset($s['score'])){
            $r3 = $s['score'];
        }else{
            $r3 = 0;
        }

        //media degli score dei secondi livelli dei toi 
        /*
        $vh = intval(($h + $h1 + $h2 + $h3 + $h4 + $h5)/5);
        $va = intval(($a + $a1 + $a2 + $a3 + $a4 + $a5)/5);
        $vn = intval(($n + $n1 + $n2 + $n3)/4);
        $vr = intval(($r + $r1 + $r2 + $r3)/4);
        */

        //associo valore massimo ai toi di primo livello tra quelli di secondo livello
        $vh = intval(max($h1, $h2, $h3, $h4, $h5));
        $va = intval(max($a1, $a2, $a3, $a4, $a5));
        $vn = intval(max($n1, $n2, $n3));
        $vr = intval(max($r1, $r2, $r3));

        echo "
                            
                            <tr>
                                <th scope=\"row\" style=\"text-align: center\">" . $name . "</th>
                                <td style=\"text-align: center\">" . $vh . " <i class=\"fa fa-star\"></i></td>
                                <td class=\"toggleDisplayHis\" style=\"text-align: center\">" . $h1 . " <i class=\"fa fa-star\"></i></td>
                                <td class=\"toggleDisplayHis\" style=\"text-align: center\">" . $h2 . " <i class=\"fa fa-star\"></i></td>
                                <td class=\"toggleDisplayHis\" style=\"text-align: center\">" . $h3 . " <i class=\"fa fa-star\"></i></td>
                                <td class=\"toggleDisplayHis\" style=\"text-align: center\">" . $h4 . " <i class=\"fa fa-star\"></i></td>
                                <td class=\"toggleDisplayHis\" style=\"text-align: center\">" . $h5 . " <i class=\"fa fa-star\"></i></td>
                                <td style=\"text-align: center\">" . $va . " <i class=\"fa fa-star\"></i></td>
                                <td class=\"toggleDisplayArt\" style=\"text-align: center\">" . $a1 . " <i class=\"fa fa-star\"></i></td>
                                <td class=\"toggleDisplayArt\" style=\"text-align: center\">" . $a2 . " <i class=\"fa fa-star\"></i></td>
                                <td class=\"toggleDisplayArt\" style=\"text-align: center\">" . $a3 . " <i class=\"fa fa-star\"></i></td>
                                <td class=\"toggleDisplayArt\" style=\"text-align: center\">" . $a4 . " <i class=\"fa fa-star\"></i></td>
                                <td class=\"toggleDisplayArt\" style=\"text-align: center\">" . $a5 . " <i class=\"fa fa-star\"></i></td>
                                <td style=\"text-align: center\">" . $vn . " <i class=\"fa fa-star\"></i></td>
                                <td class=\"toggleDisplayNat\" style=\"text-align: center\">" . $n1 . " <i class=\"fa fa-star\"></i></td>
                                <td class=\"toggleDisplayNat\" style=\"text-align: center\">" . $n2 . " <i class=\"fa fa-star\"></i></td>
                                <td class=\"toggleDisplayNat\" style=\"text-align: center\">" . $n3 . " <i class=\"fa fa-star\"></i></td>
                                <td style=\"text-align: center\">" . $vr . " <i class=\"fa fa-star\"></i></td>
                                <td class=\"toggleDisplayMus\" style=\"text-align: center\">" . $r1 . " <i class=\"fa fa-star\"></i></td>
                                <td class=\"toggleDisplayMus\" style=\"text-align: center\">" . $r2 . " <i class=\"fa fa-star\"></i></td>
                                <td class=\"toggleDisplayMus\" style=\"text-align: center\">" . $r3 . " <i class=\"fa fa-star\"></i></td>
                            </tr>";
    }
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