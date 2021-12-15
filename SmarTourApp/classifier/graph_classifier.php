<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'pdo.php';
require_once 'neo4jconnector.php';
use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Contracts\TransactionInterface;
final class WikipediaClassifier{
    private CONST NORMALIZATION_FACTOR = 5;
    private CONST MAX_RELEVANCE = 5;
    /**
     * @var neo4j client for connection with neo4j database
     */
    private $neo4j;
    /**
     * @var pdo client for connection with mysql database
     */
    private $pdo;

    /**
     * Establishes connection with databases.
     *
     */
    public function __construct() {
        $this->neo4j = connectNeo4j();
        $this->pdo = connessione();
    }
    /**
     * Assign a score for a POI which hasn't been yet classified with repect to each of the toi
     *
     */
    // public function classifyPOI(){
        
    //     $portal_file = file_get_contents('portalToi.json');
    //     $portal_data = json_decode($portal_file, true);
    //     $toi_array = [];
    //     foreach($portal_data['tags'] as $firstLevelTOI){
    //         $subtoi_array = [];
    //         foreach($firstLevelTOI['macros'] as $secondLevelTOI){
    //             $link_array = [];
    //             foreach($secondLevelTOI['values'] as $wikipage){
    //                 $arr = [
    //                     "page"=> $wikipage['page'],
    //                     "weight"=> $wikipage['weight']
    //                 ];
    //                 array_push($link_array, $arr);
    //             }
    //             $subtoi_array[$secondLevelTOI['name']] = [
    //                 "links"=>$link_array
    //             ];
    //         }
    //         $toi_array[$firstLevelTOI['name']] = $subtoi_array;
    //     }
    //     $toi_query = $this->pdo->query('SELECT * FROM toi1 JOIN toi2 WHERE toi1.id = idtoi1');
    //     foreach ($toi_query as $row){
    //         $toi_array[$row['toi1.names']][$row['toi2.names']]['id'] = $row['toi2.id'];
    //     }
    //     $poi_query = $this->pdo->query("SELECT names, wikipedia FROM poi 
    //                         WHERE names NOT IN 
    //                         (SELECT poi FROM score_portal");
    //     foreach($poi_query as $row){
    //         $poi = $row['wikipedia'];
    //         foreach($toi_array as $firstleveltoi){
    //             foreach($firstleveltoi as $secondleveltoi){
    //                 $id = $secondleveltoi['id'];
    //                 $min_distance = self::NORMALIZATION_FACTOR + 1;
    //                 foreach($secondleveltoi['links'] as $wikipage){
    //                     $weight = $wikipage['weight'];
    //                     $page = $wikipage['page'];
    //                     try{
    //                         $graph_query = $this->neo4j->run('MATCH (poi:Wikipage {nome:$poi} MATCH (toi {nome:$toi}) MATCH p=shortestPath((poi)-[*..20]->(toi) RETURN length(p)',
    //                         [
    //                             "poi"=>$poi,
    //                             "toi"=>$page
    //                         ]);
    //                         try{
    //                             $dist = $graph_query->first()->first()->getValue();
    //                             if($dist*self::MAX_RELEVANCE / $weight < $min_distance)
    //                                 $min_distance = $dist*self::MAX_RELEVANCE / $weight;
    //                         }catch(Exception $e){
    //                             //DO NOTHING BECAUSE THERE IS NO PATH
    //                         }
    //                     }catch(Throwable $t){
    //                         echo("Error with query");
    //                     }
    //                 }
    //                 $score = 1 - ($min_distance-1)/self::NORMALIZATION_FACTOR;
    //                 // QUERY INSERIMENTO PUNTEGGIO NEL DB RELAZIONALE
    //                 $this->pdo->query("INSERT INTO score_portal(poi, toi, score) VALUES ('$poi', '$id', '$score')");
    //             }
    //         }
    //     }
    // }

    /**
     * Assign a score for a POI which hasn't been yet classified with repect to each of the toi
     * specifying the city of the poi.
     * 
     * @var string $city the city of the poi to be classified.
     */
    public function classifyPOIperCity(string $city){
        $this->neo4j = connectNeo4jdb($city);
        $portal_file = file_get_contents('portalToi.json');
        $portal_data = json_decode($portal_file, true);
        $toi_array = [];
        foreach($portal_data['tags'] as $firstLevelTOI){
            $subtoi_array = [];
            foreach($firstLevelTOI['macros'] as $secondLevelTOI){
                $link_array = [];
                foreach($secondLevelTOI['values'] as $wikipage){
                    $arr = [
                        "page"=> $wikipage['page'],
                        "weight"=> $wikipage["weight"]
                    ];
                    array_push($link_array, $arr);
                }
                $subtoi_array[$secondLevelTOI['name']] = [
                    "links"=>$link_array
                ];
            }
            $toi_array[$firstLevelTOI['name']] = $subtoi_array;
        }
        $toi_query = $this->pdo->query('SELECT toi1.id as toi1id, toi1.names as toi1name, toi2.id as toi2id, toi2.names as toi2name, idtoi1 FROM toi1 JOIN toi2 WHERE toi1.id = idtoi1');
        foreach ($toi_query as $row){
            $toi_array[$row['toi1name']][$row['toi2name']]['id'] = $row['toi2id'];
        }
        $poi_query = $this->pdo->query("SELECT names, wikipedia FROM poi 
                            WHERE position = '$city' AND wikipedia IS NOT NULL");   // AND names NOT IN (SELECT poi FROM score_portal");
        foreach($poi_query as $row){
            $poi = $row['wikipedia'];
            $name = $row['names'];
            $name = str_replace("'", "\'", $name);
            $poi = preg_replace('/_/',' ', $poi);
            echo($poi);
            foreach($toi_array as $firstleveltoi){
                foreach($firstleveltoi as $secondleveltoi){
                    $id = $secondleveltoi['id'];
                    $min_distance = self::NORMALIZATION_FACTOR + 1;
                    foreach($secondleveltoi['links'] as $wikipage){
                        $weight = $wikipage['weight'];
                        $page = $wikipage['page'];
                        try{
                            $graph_query = $this->neo4j->run('MATCH (poi:Wikipage {nome:$poi}) MATCH (toi {nome:$toi}) MATCH p=shortestPath((poi)-[*..10]->(toi)) RETURN length(p)',
                            [
                                "poi"=>$poi,
                                "toi"=>$page
                            ]);
                            $dist = $graph_query->first()->get('length(p)');
                            if($dist*self::MAX_RELEVANCE / $weight < $min_distance)
                                $min_distance = $dist*self::MAX_RELEVANCE / $weight;
                        }catch(OutOfBoundsException $e){
                            //DO NOTHING BECAUSE THERE IS NO PATH
                            // echo("No path with current link.");
                        }catch(Throwable $t){
                            echo("Error with query");
                        }
                    }
                    $score = 1 - (float)($min_distance-1)/(float)self::NORMALIZATION_FACTOR;
                    echo("Score di $name rispetto al TOI $id = $score");
                    echo("<br>");
                    // QUERY INSERIMENTO PUNTEGGIO NEL DB RELAZIONALE
                    $this->pdo->query("INSERT INTO score_portal(poi, toi, score) VALUES ('$name', '$id', '$score')");
                }
            }
        }
        echo("Finita classificazione per la città di ".$city);
    }
    /**
     * Return the array containing the association between TOI and Wikipedia pages.
     *
     */
    public function buildToiArray(){
        $portal_file = file_get_contents('portalToi.json');
        $portal_data = json_decode($portal_file, true);
        $toi_array = [];
        foreach($portal_data['tags'] as $firstLevelTOI){
            $subtoi_array = [];
            foreach($firstLevelTOI['macros'] as $secondLevelTOI){
                $link_array = [];
                foreach($secondLevelTOI['values'] as $wikipage){
                    $arr = [
                        "page"=> $wikipage['page'],
                        "weight"=> $wikipage['weight']
                    ];
                    array_push($link_array, $arr);
                }
                $subtoi_array[$secondLevelTOI['name']] = [
                    "links"=>$link_array
                ];
            }
            $toi_array[$firstLevelTOI['name']] = $subtoi_array;
        }
        return $toi_array;
    }

}