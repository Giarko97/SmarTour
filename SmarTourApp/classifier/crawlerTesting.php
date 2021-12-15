<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'neo4jconnector.php';
require_once 'pdo.php';
use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Contracts\TransactionInterface;

final class CrawlerTimeTesting{
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
     * @var Array checkEnd array to check connectivity for early crawler ending.
     */
    private $checkEnd;

    /**
     * @var Array array containing all starting POI.
     */
    private $seedlist;

    /**
     * @var array toitemplate template array for the insertion of new poi.
     */
    private static array $toitemplate;

    /**
     * @var array toiportal array for association between toi and wikipages given a wikipage.
     */
    private static array $toiportal;

    /**
     * @var int THRESHOLD max distance from seed allowed during crawling.
     */
    private CONST THRESHOLD = 6;

    /**
     * @var float timeCheck total time spent during the check of the connection of the graph.
     */
    private float $timeCheck;

    /**
     * @var float timeWiki total time spent during wikipedia api request.
     */
    private float $timeWiki;

    /**
     * @var float timeNeo4j total time spent for neo4j query database. 
     */
    private float $timeNeo4j;

    /**
     * @var float timeSQL total time spent for mySQL database query. 
     */
    private float $timeSQL;

    /**
     * @var float timeParsing total time spent during wikipedia content parsing.
     */
    private float $timeParsing;

    /**
     * Establish connection with database and load from database all poi of specified city.
     *
     * @param string $city the city of the poi to be inserted in the queue.
     */
    public function __construct(string $city = null){
        $this->client = connectNeo4j();
        $this->pdo = connessione();
        $this->checkEnd = [];
        $this->seedlist = [];
        $this->timeNeo4j=0;
        $this->timeWiki=0;
        $this->timeSQL=0;
        $this->timeCheck=0;
        $this->timeParsing=0;
        $portal_assoc = file_get_contents('toiPortal.json');
        self::$toiportal = json_decode($portal_assoc, true);
        $portal_file = file_get_contents('toilist.json');
        self::$toitemplate = json_decode($portal_file, true);
        $this->queue = new \Ds\Queue();
        if($city != null){
            $this->loadCityPOI($city);
        }
    }

    // /**
    //  * Fill the queue with POI extracted by JSON file.
    //  *
    //  * @param string $json JSON file containing poi info.
    //  */
    // public final function loadJSONPOI(string $json){
    //     $data = json_decode($json, true);
    //     foreach($data[2]['data'] as $elements){
    //         if($elements['wikipedia'] != ""){
    //             $next = [
    //                 "title" => $elements['wikipedia'],
    //                 "depth" => 0
    //             ];
    //             $this->addNewElement($next);
    //         }
    //     }
    // }


    /**
     * Fill the queue with POI extracted by the query specifying the city.
     *
     * @param string $city String containing the city of the POI to insert in the queue.
     */
    public final function loadCityPOI(string $city){
        $t1 = microtime(true);
        $query = $this->pdo->query("SELECT names, wikipedia, position FROM poi WHERE wikipedia IS NOT NULL AND position = '$city'");
        $t2 = microtime(true);
        $this->timeSQL += ($t2-$t1);
        $this->client = connectNeo4jdb($city);
        foreach($query as $row){
            $next = [
                "title" => $row['wikipedia'],
                "depth" => 0
            ];
            $this->addNewElement($next);
        }
        echo("All seed POI inserted in the queue and ready to be processed.");
    }

    // /**
    //  * Read the json file stored in the file system and use it to fill the queue.
    //  *
    //  * @param string $path The path of the file to parse.
    //  */
    // public final function readJSONFile(string $path){
    //     $json = file_get_contents($path);
    //     if($json != false)
    //         $this->loadJSONPOI($json);
    // }

    /**
     * Add a single element to the queue.
     *
     * @param Array $element Array containing title and depth of the element to add.
     */
    public final function addNewElement($element){
        $this->queue->push($element);
        $this->checkEnd[$element['title']] = self::$toitemplate;
        $t1 = microtime(true);
        $query_res = $this->client->run('CREATE (poi:Wikipage {nome: $poi, depth:$poidepth, root:$poi})',
                                [
                                    "poi" => $element['title'],
                                    "poidepth" => $element['depth']
                                ],
        );
        $t2 = microtime(true);
        $this->timeNeo4j += ($t2-$t1);
        array_push($this->seedlist, $element['title']);
    }

    /**
     * Add a single element to the queue.
     *
     * @param Array $element Array containing title and depth of the seed to add.
     */
    // public final function addSeedElement($element){
    //     $this->checkEnd[$element['title']] = self::$toitemplate;
    //     $links = array_keys(self::$toiportal);
    //     foreach($links as $link){
    //         // echo($link);
    //         // echo("<br>");
    //         $query_res = $this->client->run('MATCH (poi:Wikipage{nome: $poi}) MATCH (l:Wikipage{nome: $link}) MATCH p=shortestPath((poi)-[*..10]->(l)) RETURN p',
    //                             ["poi" => $element['title'],
    //                             "link" => $link
    //                             ],
    //         );
    //         try{
    //             if(null !== ($query_res->first())){
    //                 if(is_array(self::$toiportal[$link])){
    //                     foreach(self::$toiportal[$link] as $sublink){
    //                         $this->checkEnd[$element['title']][$sublink] = true;
    //                         // echo($link . "True");
    //                     }
    //                 }else{
    //                     $this->checkEnd[$element['title']][self::$toiportal[$link]] = true;
    //                 }
    //             }
    //         }catch(Throwable $t){
    //             // echo($link . "False");
    //         }
    //     }
    // }

    // /**
    //  * Continue exploring nodes already in the graph database that have not yet been fully fullfilled
    //  *
    //  */
    // public final function resumeCrawling(){
    //     $seed_query_res = $this->client->run('MATCH (n:Wikipage) WHERE n.depth=0 RETURN n');
    //     foreach($seed_query_res->first() as $node){
    //         $nome = $node['nome'];
    //         $depth = $node['depth'];
    //         $this->addSeedElement([
    //             "title" => $nome,
    //             "depth" => $depth
    //         ]);
    //     }
    //     $query_res = $this->client->run('MATCH (n:Wikipage) WHERE n.visitato IS NULL RETURN n');
    //     foreach($query_res->first() as $node){
    //         $nome = $node['nome'];
    //         $depth = $node['depth'];
    //         $this->queue->push([
    //             "title" => $nome,
    //             "depth" => $depth
    //         ]);
    //     }
    //     $this->crawl();
    // }

    /**
     * Return in a list all seed elemets linked to the node. 
     *
     * @param string $name string with name of the node.
     */
    private function getRoots(string $name){
        $t1 = microtime(true);
        $query_res = $this->client->run('MATCH (poi:Wikipage{nome: $poi}) RETURN poi',
                                [
                                    "poi" => $name
                                ],
        );
        $t2 = microtime(true);
        $this->timeNeo4j += ($t2-$t1);
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
        $t1 = microtime(true);
        $outcome = true;
        foreach($this->seedlist as $poi){
            $fullpoi = true;
            foreach($this->checkEnd[$poi] as $toi){
                if(!$toi){
                    $fullpoi = false;
                    $outcome = false;
                }
            }
            if($fullpoi){
                echo("$poi completamente connesso.");
                echo("<br>");
                $this->seedlist = array_diff($this->seedlist, [$poi]);
            }
        }
        $t2 = microtime(true);
        $this->timeCheck += ($t2-$t1);
        return $outcome;
    }

    /**
     * Begin crawling the queue already stored in the local value.
     *
     */
    public final function crawl(){
        $endPoint = "https://it.wikipedia.org/w/api.php";
        $lastDepth = 0;           //VARIABILE PER CAPIRE QUANDO EFFETTUARE SCANSIONE CONTROLLO CONNESSIONE
        while(!$this->queue->isEmpty()){
            $poi = $this->queue->pop();
            $roots = $this->getRoots($poi['title']);
            $roots = array_unique($roots);
            $rootstring = implode(",", $roots);
            $poi['title'] = preg_replace('/_/',' ', $poi['title']);
            $title = $poi['title'];
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
            $t1wiki = microtime(true);
            $json = file_get_contents($url);
            $data = json_decode($json,true);
            $t2wiki = microtime(true);
            $this->timeWiki += ($t2wiki-$t1wiki);
            $t1parsing = microtime(true);
            if(isset($data['parse']['wikitext']['*'])){
                $data['parse']['wikitext']['*'] = preg_replace("/{{nota disambigua.*?}}/","",$data['parse']['wikitext']['*']); //Rimuove le note di disambiguazione
                preg_match_all('/\[\[(.*?)\]\]/',$data['parse']['wikitext']['*'], $links);
                $t2parsing = microtime(true);
                $this->timeParsing += ($t2parsing-$t1parsing);
                foreach($links[0] as $link){
                    $t3parsing = microtime(true);
                    if(preg_match('/\d+.*/', $link) === 0){
                        $clean = preg_replace('/\[\[([^\d|#]*)[|#]?([^\d]*?)\]\]/', '$1', $link);
                        $clean = ucfirst($clean);
                        if(preg_match('/.*secolo.*/', $clean) === 0 and $clean != "" and preg_match('/File:.*/',$link)=== 0 and preg_match('/Immagine:.*/',$link)===0){
                            $t4parsing = microtime(true);
                            $this->timeParsing += ($t4parsing-$t3parsing);
                            try{
                                $t1neo4j = microtime(true);
                                $query_res = $this->client->run('MERGE (poi:Wikipage{nome: $poi}) MERGE (l:Wikipage{nome: $link}) ON CREATE SET l.prima_visita = true, l.depth=$depth, l.root=$root ON MATCH SET l.prima_visita=false WITH poi, l MERGE (poi)-[r:Contiene]->(l) RETURN l',
                                    ["poi" => $poi['title'],
                                    "link" => $clean,
                                    "depth" => $poi['depth']+1,
                                    "root" => $rootstring
                                    ],
                                );
                                $t2neo4j = microtime(true);
                                $this->timeNeo4j += ($t2neo4j-$t1neo4j);
                                $t1check = microtime(true);
                                if(isset(self::$toiportal[$clean])){
                                    if(is_array(self::$toiportal[$clean])){
                                        foreach(self::$toiportal[$clean] as $wik){
                                            $this->checkEnd[$poi['title']][$wik] = true;
                                            foreach($roots as $seed){
                                                $this->checkEnd[$seed][$wik] = true;
                                            }
                                        }
                                    }else{
                                        $this->checkEnd[$poi['title']][self::$toiportal[$clean]] = true; //TODO MODIFICARE IN MODO DA CAMBIARE IL SEED CORRETTAMENTE
                                        foreach($roots as $seed){
                                            $this->checkEnd[$seed][self::$toiportal[$clean]] = true;
                                        }
                                    }
                                }
                                $t2check = microtime(true);
                                $this->timeCheck += ($t2check-$t1check);
                                foreach($query_res->first() as $node){
                                    if((!$node['prima_visita'])===true){
                                        if(isset($node['root'])){
                                            if(is_array($node['root'])){
                                                $linkroots = array_merge($roots, $node['root']);
                                                $linkroots = array_unique($linkroots);
                                                $linkstring = implode(",", $linkroots);
                                                $this->client->run('MATCH (l:Wikipage {nome: $link}) SET l.root=$root',
                                                    [
                                                        "link" => $node['nome'],
                                                        "root" => $linkstring
                                                    ],
                                                );
                                            }else{
                                                $linkroots = explode(",",$node['root']);
                                                $linkroots = array_merge($roots, $linkroots);
                                                $linkroots = array_unique($linkroots);
                                                $linkstring = implode(",", $linkroots);
                                                $this->client->run('MATCH (l:Wikipage {nome: $link}) SET l.root=$root',
                                                    [
                                                        "link" => $node['nome'],
                                                        "root" => $linkstring
                                                    ],
                                                );
                                            }
                                        }
                                    }
                                    if($node['prima_visita']){
                                        if($poi['depth'] < self::THRESHOLD){
                                            $next = [
                                                "title" => $clean,
                                                "depth" => $poi['depth']+1
                                            ];
                                            $this->queue->push($next);
                                        }
                                    }
                                    elseif(isset($node['visitato'])){
                                        //NAVIGARE ARRAY CHECK E COPIARE VALORE NEI VALORI DEI SEED DEL POI PRESO IN CONSIDERAZIONE
                                        $t3check = microtime(true);
                                        if(isset($this->checkEnd[$clean])){
                                            $active_keys = array_keys($this->checkEnd[$clean]);
                                            foreach($active_keys as $key){
                                                foreach($roots as $seed){
                                                    if($this->checkEnd[$clean][$key])
                                                        $this->checkEnd[$seed][$key] = true;
                                                }
                                            }
                                        }
                                        $t4check = microtime(true);
                                        $this->timeCheck += ($t4check-$t3check);
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
            $port_url = $endPoint . "?" . http_build_query($link_params);
            $t3wiki = microtime(true);
            $link_json = file_get_contents($port_url);
            $link_data = json_decode($link_json,true);
            $t4wiki = microtime(true);
            $this->timeWiki += ($t4wiki-$t3wiki);
            // $poi['title'] = preg_replace('/_/',' ', $poi['title']);                   SPOSTATO SOPRA
            foreach($link_data['query']['pages'] as $page){ 
                if(isset($page['links'])){
                    foreach($page['links'] as $portal){
                        //Inserire collegamento con $portal['title']
                        $t3neo4j = microtime(true);
                        $this->client->run(
                            'MERGE (p:Portale {nome: $portal}) MERGE(poi:Wikipage {nome: $poi}) MERGE (poi)-[r:È_contenuto_in]->(p)',
                            ['portal' => $portal['title'],
                                'poi' => $poi['title']
                            ],
                        );
                        $t4neo4j = microtime(true);
                        $this->timeNeo4j += ($t4neo4j-$t3neo4j);
                        if(array_key_exists($portal['title'], self::$toiportal)){
                            if(is_array(self::$toiportal[$portal['title']])){
                                foreach(self::$toiportal[$portal['title']] as $new_portal){
                                    $this->checkEnd[$poi['title']][$new_portal] = true;
                                    foreach($roots as $seed){
                                        $this->checkEnd[$seed][$new_portal] = true;
                                    }
                                }
                            }else{
                                $this->checkEnd[$poi['title']][self::$toiportal[$portal['title']]] = true;
                                foreach($roots as $seed){
                                    $this->checkEnd[$seed][self::$toiportal[$portal['title']]] = true;
                                }
                            }
                        }
                    }
                }
            }
            $t5neo4j = microtime(true);
            $this->client->run('MATCH (poi:Wikipage{nome: $poi}) SET poi.visitato = true',
                ["poi" => $poi['title']
                ],
            );
            $t6neo4j = microtime(true);
            $this->timeNeo4j += ($t6neo4j-$t5neo4j);
            // echo($poi['title']." processed.");
            // echo("<br>\n");
            if($lastDepth < $poi['depth']){
                echo("Eseguo controllo connessione del grafo.\n");
                print_r($this->checkEnd);
                echo("Tempo totale per chiamate API Wikipedia fino a profondità $lastDepth = $this->timeWiki. \n");
                echo("Tempo totale per controllo connessione del grafo fino a profondità $lastDepth = $this->timeCheck. \n");
                echo("Tempo totale per query Neo4j fino a profondità $lastDepth = $this->timeNeo4j. \n");
                echo("Tempo totale per query SQL fino a profondità $lastDepth = $this->timeSQL. \n");
                echo("Tempo totale per parsing wikitext fino a profondità $lastDepth = $this->timeParsing\n");
                if($this->check()){
                    echo("Crawler ended correctly.\n");
                    break;
                }
            }
            $lastDepth = $poi['depth'];
        }
    }
}