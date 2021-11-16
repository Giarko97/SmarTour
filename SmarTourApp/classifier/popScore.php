<?php

include_once 'pdo.php';

$pdo = connessione();
$pop = $pdo->query("SELECT * FROM popular");

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
        <button style=\"height:80px; width:200px;\" class=\"btn btn-primary\" onclick=\"document.location='../administrator/poimanager.php'\"><i class=\"fa fa-home\">  BACK TO HOME</i></button>
    </div>

    <br>
    <br>
    
                <table class=\"table table-striped\">
                <thead class=\"thead-dark\">
                    <tr>
                            <th style=\"text-align: center\">POI</th>
                            <th id=\"click-his\" style=\"text-align: center\">VIEWS  <i class=\"fa fa-eye\"></i></th>
                            <th id=\"click-art\" style=\"text-align: center\">BYTE LENGTH  <i class=\"fa fa-text-width\"></i></th>
                            <th id=\"click-nat\" style=\"text-align: center\">LANGUAGES  <i class=\"fa fa-language\"></i></th>
                            <th id=\"click-rel\" style=\"text-align: center\">SCORE  <i class=\"fa fa-star\"></i></th>
                        </tr>
                        </thead>";

foreach($pop as $rows){

    $name = $rows['names'];
    $views = $rows['views'];
    $lengths = $rows['lengths'];
    $nlan = $rows['nlan'];
    $name = str_replace("'", "\'", $name);
    $res = $pdo->query("SELECT * FROM norpop WHERE names = '$name'");
    foreach($res as $np){
        $norpop = $np['score'];
        $norpop = intval($norpop/0.1)/2;

    }   

    echo "
                            
    <tr>
        <th scope=\"row\" style=\"text-align: center\">" . $name . "</th>
        <td style=\"text-align: center\">" . $views . " <i class=\"fa\"></i></td>
        <td style=\"text-align: center\">" . $lengths . " <i class=\"fa\"></i></td>
        <td style=\"text-align: center\">" . $nlan . " <i class=\"fa\"></i></td>
        <td style=\"text-align: center\">" . $norpop . " <i class=\"fa fa-star\"></i></td>
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