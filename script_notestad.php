<?php
include("./inc/funciones.php");
include("./incl/bd.php");
$objetoBD=new BD(1);
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