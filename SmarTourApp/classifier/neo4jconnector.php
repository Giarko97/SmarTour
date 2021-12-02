<?php
require __DIR__ . '\..\vendor\autoload.php';
use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Contracts\TransactionInterface;
/**
 * Establish connection with neo4j database and return a client to interact with it.
 *
 */
function connectNeo4j(){
    $client = ClientBuilder::create()
    ->withDriver('bolt', 'bolt://neo4j:smartour.2021.@localhost') // creates a bolt driver
    ->withDefaultDriver('bolt')
    ->build();
    return $client;
}

function connectNeo4jdb(string $db){
    $client = ClientBuilder::create()
    ->withDriver('bolt', 'bolt://neo4j:smartour.2021.@localhost?database='.$db) // creates a bolt driver
    ->withDefaultDriver('bolt')
    ->build();
    return $client;
}