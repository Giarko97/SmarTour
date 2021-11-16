<?php
function portal($poi, $jump){
    $out = null;
    if($jump<2){
        $endPoint = "https://it.wikipedia.org/w/api.php";
        $params = [
            "action" => "query",
            "titles" => $poi,
            "prop" => "info",
            "format" => "json"
        ];
        
        $url = $endPoint . "?" . http_build_query( $params );
        
        $json = file_get_contents($url);
        $data = json_decode($json,true);
        
        //pageid
        foreach($data['query'] as $value){
            $pageid = array_key_first($value);
        };
        
        $endPoint = "https://it.wikipedia.org/w/api.php";
        $params = [
            "action" => "query",
            "format" => "json",
            "prop" => "links",
            "titles" => $poi,
            "pllimit" => "10",
            "plnamespace" => "100"
        ];
        
        $url = $endPoint . "?" . http_build_query( $params );
        
        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $output = curl_exec( $ch );
        curl_close( $ch );
        
        $result = json_decode( $output, true );

        $jump++;

        $out = new \stdClass();

        $out->result = $result;
        $out->jump = $jump;
        $out->pageid = $pageid;
        
    }
    return $out;
}

