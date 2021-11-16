<?php

include 'check.php';

function iterate($poi, $multiRes){
    
    $result = $multiRes->result;
    $jump = $multiRes->jump;
    $pageid = $multiRes->pageid;

    if(isset($result['query']['pages'][$pageid]['links'])){
        foreach($result['query']['pages'][$pageid]['links'] as $value){
            $portal = substr($value['title'], 8);
            $score = 6 - $jump;
            check($poi, $portal, $score);
            $newPortal = "Portale:" . $portal;
            $multiRes = portal($newPortal,$jump);
            if(isset($multiRes)){
                $multiRes = portal($newPortal, $jump);
                iterate($poi, $multiRes);
            }
        }
    }
}