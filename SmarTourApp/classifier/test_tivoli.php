<?php
require "crawler.php";
set_time_limit(0);
$crawler = new Crawler();
$crawler->loadCityPOI("Tivoli");
$crawler->crawl();