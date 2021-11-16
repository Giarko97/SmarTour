<?php

include_once('pdo.php');

function pop(){

    $pdo = connessione();

    $pdo->query("TRUNCATE TABLE norpop");

    foreach($pdo->query("SELECT MAX(nlan) AS nlanMax, MAX(views) AS viewsMax, MAX(lengths) AS lengthsMax FROM popular LIMIT 1") as $row){

        $nalnMax = $row['nlanMax'];
        $viewsMax = $row['viewsMax'];
        $lengthsMax = $row['lengthsMax'];
        
        foreach($pdo->query("SELECT * FROM popular") as $rows){
            $poi = $rows['names'];
            $poi = str_replace("'", "\'", $poi);

            $nlan = $rows['nlan'];
            $lengths = $rows['lengths'];
            $views = $rows['views'];

            $norLengths = $lengths/$lengthsMax;
            $norNlan = $nlan/$nalnMax;
            $norViews = $views/$viewsMax;

            //Pesi dei valori, somma a 1 (da rivedere)
            $views_load = 0.45;
            $nlan_load = 0.3;
            $pagelength_load = 0.25;

            //Normalizzo la funzione popolarità a 1
            $popularity = $views_load*$norViews + $nlan_load*$norNlan + $pagelength_load*$norLengths;

            if($pdo->query("SELECT * FROM norpop WHERE names = '$poi'")->rowCount() == 0){    
                $pdo->exec("INSERT INTO norpop (names, score) VALUES ('$poi', '$popularity')");
            }else{
                $pdo->exec("DELETE FROM norpop WHERE names = '$poi'");
                $pdo->exec("INSERT INTO norpop (names, score) VALUES ('$poi', '$popularity')");
            }
        }
    }
}