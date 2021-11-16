<?php

function getToi(){
    $user = json_decode(file_get_contents('portalToi.json'));
    
    $toi = array();

    foreach($user->tags as $tl1){
        $cat =  $tl1->name;
        array_push($toi, $cat);
        foreach($tl1->macros as $tl2){
            $name = $tl2->name;
            array_push($toi, $name);
            foreach($tl2->values as $v){
                array_push($toi, $v);
            }
        }
    }
    return $toi;
}