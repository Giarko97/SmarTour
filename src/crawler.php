<?php
require __DIR__ . '\..\vendor\autoload.php';
use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Contracts\TransactionInterface;

final class Crawler{

    // use Laudis\Neo4j\Authentication\Authenticate;
    // use Laudis\Neo4j\ClientBuilder;
    // use Laudis\Neo4j\Contracts\TransactionInterface;

    /**
     * @var client client for connection with neo4j database
     */
    private $client;

    /**
     * @var queue queue to store next pages to crawl.
     */
    private $queue;

    /**
     * Establish connection with database and load a json string if provided.
     *
     * @param string $json
     */
    public function __construct(string $json = null){
        $this->client = ClientBuilder::create()
            ->withDriver('bolt', 'bolt://neo4j:leonardo@localhost') // creates a bolt driver
            ->withDriver('https', 'https://localhost:7473', Authenticate::basic('neo4j', 'leonardo')) // creates an http driver
            ->withDefaultDriver('bolt')
            ->build();
        $this->queue = new \Ds\Queue();
        if($json != null){
            $this->loadJSONPOI($json);
        }
    }

    /**
     * Fill the queue with POI extracted by JSON file.
     *
     * @param string $json JSON file containing poi info
     */
    public final function loadJSONPOI(string $json){
        $data = json_decode($json, true);
        foreach($data[2]['data'] as $elements){
            if($elements['wikipedia'] != ""){
                $next = [
                    "title" => $elements['wikipedia'],
                    "depth" => 0
                ];
                $this->queue->push($next);
            }
        }
    }

    /**
     * Read the json file stored in the file system and use it to fill the queue
     *
     * @param string $path The path of the file to parse
     */
    public final function readJSONFile(string $path){
        $json = file_get_contents($path);
        if($json != false)
            $this->loadJSONPOI($json);
    }

    /**
     * Add a single element to the queue
     *
     * @param Array $element
     */
    public final function addElement($element){
        $this->queue->push($element);
    }

    /**
     * Continue exploring nodes already in the graph database that have not yet been fully fullfilled
     *
     */
    public final function resumeCrawling(){
        $query_res = $this->client->run('MATCH (n:Wikipage) WHERE n.visitato IS NULL RETURN n');
        foreach($query_res as $row){
            $node = $row->first()->getValue();
            $nome = $node->getProperty('nome');
            $this->queue->push([
                "title" => $nome,
                "depth" => 0
            ]);
        }
        $this->crawl();
    }

    /**
     * Begin crawling the queue already stored in the local value.
     *
     */
    public final function crawl(){
        $endPoint = "https://it.wikipedia.org/w/api.php";
        while(!$this->queue->isEmpty()){
            $poi = $this->queue->pop();
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
                $data['parse']['wikitext']['*'] = preg_replace("/{{nota disambigua.*?}}/","",$data['parse']['wikitext']['*']);
                preg_match_all('/\[\[(.*?)\]\]/',$data['parse']['wikitext']['*'], $links);
                foreach($links[0] as $link){
                    if(preg_match('/\d+.*/', $link) === 0){
                        $clean = preg_replace('/\[\[([^\d|#]*)[|#]?([^\d]*?)\]\]/', '$1', $link);
                        $clean = ucfirst($clean);
                        if(preg_match('/.*secolo.*/', $clean) === 0 and $clean != "" and preg_match('/File:.*/',$link)=== 0){
                            $query_res = $this->client->run('MERGE (poi:Wikipage{nome: $poi}) MERGE (l:Wikipage{nome: $link}) MERGE (poi)-[r:Contiene]->(l) RETURN l',
                                ["poi" => $data['parse']['title'],
                                "link" => $clean
                                ],
                            );
                            try{
                                $query_res->first()->first()->getValue()->getProperty('visitato');
                            }
                            catch(Throwable $t){
                                $next = [
                                    "title" => $clean,
                                    "depth" => $poi['depth']+1
                                ];
                                $this->queue->push($next);
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
                    }
                }
            }
        }
    }
}