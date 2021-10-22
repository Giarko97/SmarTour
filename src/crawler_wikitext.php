<?php
set_time_limit(600);
$queue = new \Ds\Queue();
$queue->allocate(500);
$input = [
    "title" => "Palazzo_dei_Priori_(Perugia)",
    "depth" => 0,
];

$queue->push($input);
$endPoint = "https://it.wikipedia.org/w/api.php";
while(!$queue->isEmpty()){
    $poi = $queue->pop();
    $params = [
        "action" => "parse",
        "page" => $poi['title'],
        "prop" => "wikitext",
        "section" => "0",
        "format" => "json"
    ];
    $link_params = [
        "action" => "query",
        "titles" => $poi['title'],
        "prop" => "links",
        "pllimi" => 500,
        "plnamespace" => 100,
        "format" => "json"
    ];
    $url = $endPoint . "?" . http_build_query($params);
    $json = file_get_contents($url);
    $data = json_decode($json,true);
    if(isset($data['parse']['wikitext']['*'])){
        preg_match_all('/\[\[(.*?)\]\]/',$data['parse']['wikitext']['*'], $links);
        foreach($links[0] as $link){
            if(preg_match('/\d+.*/', $link) === 0){
                $clean = preg_replace('/\[\[([^\d|#]*)[|#]?([^\d]*?)\]\]/', '$1', $link);
                if(preg_match('/.*secolo.*/', $clean) === 0){
                    echo($data['parse']['title'] . " " . $clean . " " . $poi['depth']);
                    echo("<br>");
                    if($poi['depth'] < 2){
                        $next = [
                            "title" => $clean,
                            "depth" => $poi['depth']+1
                        ];
                        $queue->push($next);
                    }
                }
            }
        }
    }
    $port_url = $endPoint . "?" . http_build_query($link_params);
    $link_json = file_get_contents($url);
    $link_data = json_decode($json,true);
    foreach($link_data['query']['pages'] as $page){
        if(isset($page['links'])){
            foreach($page['links'] as $portal){
                //Inserire collegamento con $portal['title']
            }
        }
    }
}