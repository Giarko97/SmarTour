<?php
require __DIR__ . '\..\vendor\autoload.php';
require 'neo4jconnector.php';
use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Contracts\TransactionInterface;

final class Crawler{
    /**
     * @var client client for connection with neo4j database
     */
    private $client;

    /**
     * @var pdo client for connection with mysql database
     */
    private $pdo;

    /**
     * @var queue queue to store next pages to crawl.
     */
    private $queue;

    /**
     * @var ckeckEnd array to check connectivity for early crawler ending.
     */
    private $checkEnd;

    /**
     * @var toitemplate template array for the insertion of new poi.
     */
    private static array $toitemplate;

    /**
     * @var toiportal array for association between toi and wikipages given a wikipage.
     */
    private static array $toiportal;

    /**
     * @THRESHOLD max distance from seed allowed during crawling.
     */
    private CONST THRESHOLD = 6;

    /**
     * Establish connection with database and load a json string into the queue if provided.
     *
     * @param string $json
     */
    public function __construct(string $city = null, string $json = null){
        $this->client = connectNeo4j();
        $this->pdo = connessione();
        $this->checkEnd = [];
        $portal_assoc = file_get_contents('toiPortal.json');
        self::$toiportal = json_decode($portal_assoc, true);
        $portal_file = file_get_contents('toilist.json');
        self::$toitemplate = json_decode($portal_file, true);
        $this->queue = new \Ds\Queue();
        if($city != null){
            $this->loadCityPOI($city);
        }
        if($json != null){
            $this->loadJSONPOI($json);
        }
    }

    /**
     * Fill the queue with POI extracted by JSON file.
     *
     * @param string $json JSON file containing poi info.
     */
    public final function loadJSONPOI(string $json){
        $data = json_decode($json, true);
        foreach($data[2]['data'] as $elements){
            if($elements['wikipedia'] != ""){
                $next = [
                    "title" => $elements['wikipedia'],
                    "depth" => 0
                ];
                $this->addNewElement($next);
            }
        }
    }
    /**
     * Fill the queue with POI extracted by the query specifying the city.
     *
     * @param string $city String containing the city of the POI to insert in the queue.
     */
    public final function loadCityPOI(string $city){
        $query = $this->pdo->query("SELECT names, wikipedia, position FROM poi WHERE wikipedia != '' AND position = '$city'");
        foreach($query as $row){
            $next = [
                "title" => $row['wikipedia'],
                "depth" => 0
            ];
            $this->addNewElement($next);
        }
        $this->client = connectNeo4jdb($city);
    }

    /**
     * Read the json file stored in the file system and use it to fill the queue.
     *
     * @param string $path The path of the file to parse.
     */
    public final function readJSONFile(string $path){
        $json = file_get_contents($path);
        if($json != false)
            $this->loadJSONPOI($json);
    }

    /**
     * Add a single element to the queue.
     *
     * @param Array $element Array containing title and depth of the element to add.
     */
    public final function addNewElement($element){
        $this->queue->push($element);
        $this->checkEnd[$element['title']] = self::$toitemplate;
    }

    /**
     * Add a single element to the queue.
     *
     * @param Array $element Array containing title and depth of the seed to add.
     */
    public final function addSeedElement($element){
        $this->checkEnd[$element['title']] = self::$toitemplate;
        $links = array_keys(self::$toiportal);
        foreach($links as $link){
            // echo($link);
            // echo("<br>");
            $query_res = $this->client->run('MATCH (poi:Wikipage{nome: $poi}) MATCH (l:Wikipage{nome: $link}) MATCH p=shortestPath((poi)-[*..10]->(l)) RETURN p',
                                ["poi" => $element['title'],
                                "link" => $link
                                ],
            );
            try{
                if(null !== ($query_res->first())){
                    if(is_array(self::$toiportal[$link])){
                        foreach(self::$toiportal[$link] as $sublink){
                            $this->checkEnd[$element['title']][$sublink] = true;
                            // echo($link . "True");
                        }
                    }else{
                        $this->checkEnd[$element['title']][self::$toiportal[$link]] = true;
                    }
                }
            }catch(Throwable $t){
                // echo($link . "False");
            }
        }
    }

    /**
     * Continue exploring nodes already in the graph database that have not yet been fully fullfilled
     *
     */
    public final function resumeCrawling(){
        $seed_query_res = $this->client->run('MATCH (n:Wikipage) WHERE n.depth=0 RETURN n');
        foreach($seed_query_res->first() as $node){
            $nome = $node['nome'];
            $depth = $node['depth'];
            $this->addSeedElement([
                "title" => $nome,
                "depth" => $depth
            ]);
        }
        $query_res = $this->client->run('MATCH (n:Wikipage) WHERE n.visitato IS NULL RETURN n');
        foreach($query_res->first() as $node){
            $nome = $node['nome'];
            $depth = $node['depth'];
            $this->queue->push([
                "title" => $nome,
                "depth" => $depth
            ]);
        }
        $this->crawl();
    }

    /**
     * Return in a list all seed elemets linked to the node. 
     *
     * @param string $name string with name of the node.
     */
    private function getRoots(string $name){
        $query_res = $this->client->run('MERGE (poi:Wikipage{nome: $poi}) RETURN poi',
                                [
                                    "poi" => $name
                                ],
        );
        foreach($query_res->first() as $node){
            if(isset($node['root'])){
                $parents = explode(",", $node['root']);
            }else{
                $parents = [$name];
            }
            return $parents;
        }
    }

    /**
     * Look up the check array to see if crawling should be stopped, returns true if so.
     *
     */
    private function check(){
        foreach($this->checkEnd as $poi){
            foreach($poi as $toi){
                if(!$toi)
                    return $toi;
            }
        }
        return true;
    }

    /**
     * Begin crawling the queue already stored in the local value.
     *
     */
    public final function crawl(){
        $endPoint = "https://it.wikipedia.org/w/api.php";
        while(!$this->queue->isEmpty()){
            $poi = $this->queue->pop();
            $roots = $this->getRoots($poi['title']);
            $roots = array_unique($roots);
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
                "pllimit" => 500,
                "plnamespace" => 100,
                "format" => "json"
            ];
            $url = $endPoint . "?" . http_build_query($params);
            $json = file_get_contents($url);
            $data = json_decode($json,true);
            if(isset($data['parse']['wikitext']['*'])){
                $data['parse']['wikitext']['*'] = preg_replace("/{{nota disambigua.*?}}/","",$data['parse']['wikitext']['*']); //Rimuove le note di disambiguazione
                preg_match_all('/\[\[(.*?)\]\]/',$data['parse']['wikitext']['*'], $links);
                foreach($links[0] as $link){
                    if(preg_match('/\d+.*/', $link) === 0){
                        $clean = preg_replace('/\[\[([^\d|#]*)[|#]?([^\d]*?)\]\]/', '$1', $link);
                        $clean = ucfirst($clean);
                        if(preg_match('/.*secolo.*/', $clean) === 0 and $clean != "" and preg_match('/File:.*/',$link)=== 0){
                            try{
                                $query_res = $this->client->run('MERGE (poi:Wikipage{nome: $poi}) ON CREATE SET poi.depth=$poidepth, poi.root=$poi WITH poi MERGE (l:Wikipage{nome: $link}) ON CREATE SET l.depth=$depth, l.root=poi.root ON MATCH SET l.root=l.root+","+poi.root WITH poi, l MERGE (poi)-[r:Contiene]->(l) RETURN l',
                                    ["poi" => $data['parse']['title'],
                                    "link" => $clean,
                                    "poidepth" => $poi['depth'],
                                    "depth" => $poi['depth']+1
                                    ],
                                );
                                if(isset(self::$toiportal[$clean])){
                                    if(is_array(self::$toiportal[$clean])){
                                        foreach(self::$toiportal[$clean] as $wik){
                                            $this->checkEnd[$data['parse']['title']][$wik] = true;
                                            foreach($roots as $seed){
                                                $this->checkEnd[$seed][$wik] = true;
                                            }
                                        }
                                    }else{
                                        $this->checkEnd[$data['parse']['title']][self::$toiportal[$clean]] = true; //TODO MODIFICARE IN MODO DA CAMBIARE IL SEED CORRETTAMENTE
                                        foreach($roots as $seed){
                                            $this->checkEnd[$seed][self::$toiportal[$clean]] = true;
                                        }
                                    }
                                }
                                // $query_res->first()->first()->getValue()->getProperty('visitato');
                                foreach($query_res->first() as $node){
                                    if(!isset($node['visitato'])){
                                        if($poi['depth'] < self::THRESHOLD){
                                            $next = [
                                                "title" => $clean,
                                                "depth" => $poi['depth']+1
                                            ];
                                            $this->queue->push($next);
                                        }
                                    }
                                    else{
                                        //NAVIGARE ARRAY CHECK E COPIARE VALORE NEI VALORI DEI SEED DEL POI PRESO IN CONSIDERAZIONE
                                        $active_keys = array_keys($this->checkEnd[$clean]);
                                        foreach($active_keys as $key){
                                            foreach($roots as $seed){
                                                if($this->checkEnd[$clean][$key])
                                                    $this->checkEnd[$seed][$key] = true;
                                            }
                                        }
                                    }
                                }
                            }
                            catch(Throwable $t){
                                echo("Neo4j query error");
                                // $next = [
                                //     "title" => $clean,
                                //     "depth" => $poi['depth']+1
                                // ];
                                // $this->queue->push($next);
                            }
                        }
                    }
                }
            }
            $this->client->run('MATCH (poi:Wikipage{nome: $poi}) SET poi.visitato = true',
                ["poi" => $data['parse']['title']
                ],
            );
            $port_url = $endPoint . "?" . http_build_query($link_params);
            $link_json = file_get_contents($port_url);
            $link_data = json_decode($link_json,true);
            $poi['title'] = preg_replace('/_/',' ', $poi['title']);
            foreach($link_data['query']['pages'] as $page){
                if(isset($page['links'])){
                    foreach($page['links'] as $portal){
                        //Inserire collegamento con $portal['title']
                        $this->client->run(
                            'MERGE (p:Portale {nome: $portal}) MERGE(poi:Wikipage {nome: $poi}) MERGE (poi)-[r:È_contenuto_in]->(p)', //The query is a required parameter
                            ['portal' => $portal['title'],
                                'poi' => $poi['title']
                            ],
                        );
                        if(isset(self::$toiportal[$portal['title']])){
                            print_r($poi['title']);
                            echo("<br>");
                            print_r(self::$toiportal[$portal['title']]);
                            echo("<br>");
                            $this->checkEnd[$poi['title']][self::$toiportal[$portal['title']]] = true;
                        }
                        if(isset(self::$toiportal[$portal['title']])){                //NEW LINES ON PROGRESS
                            if(is_array(self::$toiportal[$portal['title']])){
                                foreach(self::$toiportal[$portal['title']] as $new_portal){
                                    $this->checkEnd[$data['parse']['title']][$new_portal] = true;
                                    foreach($roots as $seed){
                                        $this->checkEnd[$seed][$new_portal] = true;
                                    }
                                }
                            }else{
                                $this->checkEnd[$data['parse']['title']][self::$toiportal[$portal['title']]] = true; //TODO MODIFICARE IN MODO DA CAMBIARE IL SEED CORRETTAMENTE
                                foreach($roots as $seed){
                                    $this->checkEnd[$seed][self::$toiportal[$portal['title']]] = true;
                                }
                            }
                        }
                    }
                }
            }
            if($this->check())
                break;
        }
    }
}