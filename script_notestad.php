<?php
include("./inc/funciones.php");
include("./incl/bd.php");
$objetoBD=new BD(1);
if($argc>0)
$tiponoticia=intval($argv[1]);
$url="https://sede.agenciatributaria.gob.es/Sede/todas-noticias.xml"
/*https://sede.agenciatributaria.gob.es/Sede/sala-prensa/notas-prensa.xml
https://sede.agenciatributaria.gob.es/Sede/informacion-institucional/sobre-agencia-tributaria.xml

https://funcionpublica.hacienda.gob.es/rss/?feedPath=/rssaggregatorFP&generatorName=rssInternal&channel=channel-noticias&lang=es

https://www.sanidad.gob.es/gabinete/notap_rss.do

https://www.hacienda.gob.es/_layouts/15/RssEmpleo.aspx?hiloId=4
https://www.hacienda.gob.es/_layouts/15/RssNotasPrensa.aspx?hiloId=1
https://www.hacienda.gob.es/_layouts/15/RssNovedades.aspx?hiloId=9

https://www.boe.es/rss/boe.php
https://www.boe.es/rss/borme.php

https://www.seg-social.es/wps/wcm/connect/wss/poin_contenidos/internet/1139/?srv=cmpnt&source=library&cmpntid=601fa53b-f1d2-4180-a5e7-fe0b130e0296

http://prensa.mites.gob.es/WebPrensa/rss*//*

$url="http://prensa.empleo.gob.es/WebPrensa/rss/laboral";
$objetoRSS=simplexml_load_file($url);
if(count($objetoRSS->channel->item)>0){
    $objetoBD->insertarNoticias($objetoRSS,2);
}
echo("<br><br>la otra<br><br>");
$url="http://prensa.empleo.gob.es/WebPrensa/rss/seguridadsocial";
$objetoRSS=simplexml_load_file($url);
if(count($objetoRSS->channel->item)>0){
    $objetoBD->insertarNoticias($objetoRSS,2);
}
$objetoBD=null;?>