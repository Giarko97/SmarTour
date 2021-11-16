<?php
//rivedi discorso punti e virgole
function timeparser($time){

    $timeFinal = "";
    $count = 0;

    $time = str_replace(",", "-", $time);
    $t = explode(";", $time);

    foreach ($t as $value) {

        $noday = explode(" ", $value);

        if(strlen($value) < 20){
            if($value == "24/7" || $value == "24-7" || $value == "Mo-Su"){
                $timeFinal = "Mo-Su 00:00-24:00";
            }else if(strpos($value, 'off') !== false || strpos($value, 'close') !== false){
                //do nothing
            }else if(!isset($noday[1])){
                if(substr($noday, 0, 1) == "0" || substr($noday, 0, 1) == "1" || substr($noday, 0, 1) == "2"){
                    if($count == 0){
                        $timeFinal = "Mo-Su " . $noday;
                    }else{
                        $timeFinal = $timeFinal . "; " . "Mo-Su " . $noday;
                    }
                }else{
                    if($count == 0){
                        $timeFinal = $noday . " 00:00-24:00";
                    }else{
                        $timeFinal = $timeFinal . "; " . $noday . " 00:00-24:00";
                    }
                }
            }else{
                if($count == 0){
                    $timeFinal = $value;
                }else{
                    $timeFinal = $timeFinal . "; " . $value;
                }
            }
        }

        $count++;

    }
    
    return $timeFinal;

}

?>