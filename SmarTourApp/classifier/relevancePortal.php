<pre>
<?php
//old
set_time_limit(0);

include_once "pdo.php";

$poi = "Vittoriano";

$c = 0;

portal($poi, $c, $poi);

function portal($page, $c, $poi){
    
    $user = json_decode(file_get_contents('toi.json'));
    
    $toi = array();
    
    foreach($user->tags as $mydata){
        $cat =  $mydata->name;
        //echo $cat . "<br>";
        array_push($toi, $cat);
        foreach($mydata->values as $subc){
            //echo $subc . "<br>";
            array_push($toi, $subc);
        }
    }
    
    $c++;
    
    $endPoint = "https://it.wikipedia.org/w/api.php";
    $params = [
        "action" => "query",
        "titles" => $page,
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
        "titles" => $page,
        "pllimit" => "10",
        "plnamespace" => "100"
    ];
    
    $url = $endPoint . "?" . http_build_query( $params );
    
    $ch = curl_init( $url );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    $output = curl_exec( $ch );
    curl_close( $ch );
    
    $result = json_decode( $output, true );
    
    //print_r($result);
    if(isset($result['query']['pages'][$pageid]['links'])){
        foreach($result['query']['pages'][$pageid]['links'] as $value){
            $portale = $value['title'];
            //print_r($portale);
            //$portale = str_replace("Portale:", "", $portale);
            $portale = substr($portale, 8);
            //echo $portale . " " . $c . "<br>";
            $portalpoi = "Portale:" . $portale; 
            if(in_array($portale, $toi)){
                $pos = array_search($portale, $toi);
                $sc = 6 - $c;   
                echo "<b>trovato: </b>".$portale. " " . $sc. "<br>";
                $pdo = connessione();
                try{
                    $rn = $pdo->query("SELECT * FROM scorept WHERE poi = '$poi' AND toi = '$portale' AND score > '$sc'")->rowCount();
                    if($rn == 0){
                            $pdo->exec("DELETE FROM scorept WHERE poi = '$poi' AND toi = '$portale'");
                            $pdo->exec("INSERT INTO scorept (poi, toi, score) VALUE ('$poi', '$portale', '$sc')");
                    }
                }catch(PDOException $pdoe){
                    echo $pdoe->getMessage();
                }
                $pdo = null;
            }elseif($c<5){
                portal($portalpoi, $c, $poi);
            }
        }
    }
}   
?>
</pre>
